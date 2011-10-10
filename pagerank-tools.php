<?php
/*
Plugin Name: Pagerank tools
Plugin URI: http://www.rheinschmiede.de
Description: Monitor pageranks of your wordpress urls. 
Version: 1.1
Author: Sven Lehnert, Sven Wagener
Author URI: http://www.rheinschmiede.de
*/

/**********************************************************************
This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
***********************************************************************/

global $prtools_url_table;
global $prtools_pr_table;
global $prtools_version;
global $prtools_debug;
global $prtools_absolute_path;
global $prtools_absolute_path_absolute;
global $prtools_plugin_path;
global $prtools_titles;
global $prtools_extended;
global $wpdb;

$prtools_debug=false;
$prtools_version="1.1";

$prtools_url_table=$wpdb->prefix."prtools_url";
$prtools_pr_table=$wpdb->prefix."prtools_pr";

$prtools_name=__('Pagerank tools','prtools');

$prtools_absolute_path_absolute=dirname(__FILE__);
$prtools_absolute_path=substr($prtools_absolute_path_absolute,strlen($_SERVER['DOCUMENT_ROOT']),strlen($prtools_absolute_path_absolute)-strlen($_SERVER['DOCUMENT_ROOT']));

$prtools_plugin_path=substr(dirname(__FILE__),strlen($_SERVER['DOCUMENT_ROOT']),strlen(dirname(__FILE__))-strlen($_SERVER['DOCUMENT_ROOT']));
if(substr($prtools_plugin_path,0,1)!="/"){$prtools_plugin_path="/".$prtools_plugin_path;}

include($prtools_absolute_path_absolute.'/lib/io.inc.php');
include($prtools_absolute_path_absolute.'/lib/html.inc.php');
include($prtools_absolute_path_absolute.'/lib/wp_url.inc.php');
include($prtools_absolute_path_absolute.'/res/pagerank.php');
include($prtools_absolute_path_absolute.'/ui/functions_layout.inc.php');

$prtools_extended=false;
if(file_exists($prtools_absolute_path_absolute."/extended.php")){include($prtools_absolute_path_absolute."/extended.php");}

include($prtools_absolute_path_absolute.'/functions.inc.php');
include($prtools_absolute_path_absolute.'/updates.php');

include($prtools_absolute_path_absolute.'/admin/main.php');
include($prtools_absolute_path_absolute.'/admin/pagerank_overview.php');
include($prtools_absolute_path_absolute.'/admin/settings.php');
include($prtools_absolute_path_absolute.'/admin/get_pro.php');

register_activation_hook(__FILE__,'prtools_install');

add_action('admin_head','pr_ajaxui_css');
add_action('admin_head','prtools_css');
add_action('init','pr_ajaxui_js');

add_action('admin_menu','add_prtools');
add_action('wp_footer','fetch_pr');

/**
 * PR fetcher menue
 */
function add_prtools() {
	// add_menu_page(__('PR'),__('PR Tools'), 'administrator', 'menueprtools', 'prtools_dashbord');
	add_submenu_page( 'tools.php', __( 'Pagerank tools', 'prtools'),__( 'Pagerank tools', 'prtools' ), 'administrator', 'page_pageranks', 'page_pageranks' );
}

/**
 * PR fetcher installation
 */
function prtools_install() {
	global $wpdb;
	global $prtools_url_table;
	global $prtools_pr_table;	
	
	/**
	 * Installing tables
	 */
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$prtools_url_table."'") != $prtools_url_table) {
	
		$sql = "CREATE TABLE ".$prtools_url_table." (
		ID int(11) NOT NULL AUTO_INCREMENT,
		entrydate int(11) DEFAULT '0' NOT NULL,
		lastupdate int(11) DEFAULT '0' NOT NULL,
		lastcheck int(11) NOT NULL,
		url_type char(50) NOT NULL,
		url VARCHAR(500) NOT NULL,
		title text NOT NULL,
		pr int(11) NOT NULL,
		diff_last_pr int(1) NOT NULL,
		pr_entries int(11) NOT NULL,
		UNIQUE KEY id (id)
		);";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	if($wpdb->get_var("SHOW TABLES LIKE '".$prtools_pr_table."'") != $prtools_pr_table) {
	
		$sql = "CREATE TABLE ".$prtools_pr_table." (
		ID int(11) NOT NULL AUTO_INCREMENT,
		entrydate int(11) DEFAULT '0' NOT NULL,
		url VARCHAR(500) NOT NULL,
		pr int(11) NOT NULL,
		UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	update_url_table();
	add_option("prtools_db_version",'1.0');
	
	/**
	 * Setting standard options
	 */
	if(get_option('pagerank_tools_settings')==""){
		$prtools_settings['fetch_interval']=5;
		$prtools_settings['fetch_url_interval']=120;
		$prtools_settings['fetch_url_interval_new']=14;
		$prtools_settings['fetch_num']=1;
		$prtools_settings['fetch_titles_num']=2;
		$prtools_settings['running_number']=1;
		
		update_option('pagerank_tools_settings',$prtools_settings);	
	}
}

function update_pr_tools(){
	global $prtools_debug;
	global $prtools_version;	
	
	$prtools_settings=get_option('pagerank_tools_settings');
	// unset($prtools_settings['running_number']);
	if($prtools_debug) print_r_html($prtools_settings);
	
	// Reorganizing tables // 08. December 2010 // Version 0.2 to 0.2.1

	// Adding entries for requests urls without pr from google,
	// adding first creation log,
	// get difference between last PR and new PR in table  
	// and getting num of entries in table
	if($prtools_settings['running_number']==""){
		cleanup_db_from_02();
	}
	
	if($prtools_settings['running_number']<3){
		alter_table_from_02();
		update_url_table(true,true);
		$prtools_settings['fetch_titles_num']=2;
	}
	// Saving which updates have been made
	$prtools_settings['version']=$prtools_version;	
	$prtools_settings['running_number']=3;
	
	// if($prtools_debug)unset($prtools_settings['running_number']);
				
	update_option('pagerank_tools_settings',$prtools_settings);	
}

?>