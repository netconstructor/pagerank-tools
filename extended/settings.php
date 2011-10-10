<?php

/*
 * Extended Fields in Settings page
 *****************************************/
 function prtools_extend_settings(){
	global $prtools_debug;
	
	$prtools_settings=get_option('pagerank_tools_settings');	

	echo '<p>' . __('Email Notification address:','prtools') . '</p>';
	echo '<input type="text" name="email_notification" id="email_notification" value="' . $prtools_settings['email_notification'] . '" size="20" /> ' . __('Email address for getting update information about pagerank changes ','prtools');
	
	if(ini_get('allow_url_fopen')==1){
		echo '<h3>' . __('Title fetching options','prtools') . '</h3>';
		echo '<p>' . __('This has only performance reasons. Just try out and see if performance is going down or not. Titles will be fetched on every visit of user until all titles are fetched.','prtools') . '</p>';
		echo '<p>' . __('How much titles should be fetched at once:','prtools') . '</p>';
		echo '<input type="text" name="fetch_titles_num" id="fetch_titles_num" value="' . $prtools_settings['fetch_titles_num'] . '" size="2" /> ' . __('titles','prtools');
	}
	
}
add_action( 'prtools_settings_page', 'prtools_extend_settings' , 10, 0); 

/*
 * Extended Fields in Settings page
 *****************************************/
 function prtools_extend_settings_save(){
	global $prtools_debug;
	$prtools_settings=get_option('pagerank_tools_settings');
	
	$prtools_settings['email_notification']=$_POST['email_notification'];
	$prtools_settings['fetch_titles_num']=$_POST['fetch_titles_num'];
	
	if($prtools_debug)echo "Saving: " . $prtools_settings['email_notification'] . "\n";

	update_option('pagerank_tools_settings',$prtools_settings);


}
add_action( 'prtools_settings_save', 'prtools_extend_settings_save' , 10, 0); 

?>