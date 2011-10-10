<?php

/*
 * Extended Fields in Settings page
 *****************************************/
 function prtools_extend_update_pr($url_row,$act_pr,$last_pr){
	 	 $prtools_settings=get_option('pagerank_tools_settings');
		 
		 if($prtools_settings['email_notification']!=""){
			 
			 if($last_pr==-2){
				 $txt_last_pr="Not requested";
				 $txt_act_pr=$act_pr;				 
				 $txt_diff_last_pr = $act_pr;				 				 
			 }elseif($last_pr==-1){
				 $txt_last_pr="N/A";
				 $txt_act_pr=$act_pr;				 
				 $txt_diff_last_pr = $act_pr;
			 }elseif($last_pr>-1){
			  	 $txt_last_pr=$last_pr;
				 $txt_act_pr=$act_pr;				 
				 $txt_diff_last_pr = $act_pr - $last_pr;
			 }
			 
			 if($txt_diff_last_pr>0){
	 			 $txt_diff_last_pr = '+' . $txt_diff_last_pr;
			 }
			 if($act_pr==-1 && $last_pr!=-2){
				 $txt_act_pr="N/A";
				 $txt_diff_last_pr=$last_pr*(-1);
			 }
			 if($act_pr==-1 && $last_pr==-2){
				 $txt_act_pr="N/A";
				 $txt_diff_last_pr=0;
			 }
			 
			 		 	 
			 $subject = __('Pagerank changed for','prtools') . ' ' . $url_row->url;
			 
			 $msg = 'Site:
' . $url_row->url . '

Old pagerank: ' .$txt_last_pr . '
New Pagerank: ' . $txt_act_pr . '

Difference: ' . $txt_diff_last_pr  . '


Automated mail by Pagerank tools prefessional.';
		 
		
			 wp_mail($prtools_settings['email_notification'],$subject,$msg);
		 }
}
add_action( 'prtools_update_pr', 'prtools_extend_update_pr' , 10, 3);

function fetch_titles(){
	if($prtools_debug)echo "Drin!";
	global $wpdb;
	global $prtools_debug;
	global $prtools_url_table;
	global $prtools_pr_table;

	$prtools_settings=get_option('pagerank_tools_settings');
	$num=$prtools_settings['fetch_titles_num'];	
	
	// Getting sites which are updatable by settings
	$sql="SELECT * FROM ".$prtools_url_table." WHERE title='' ORDER BY pr DESC LIMIT 0,".$num;
	
	if($prtools_debug)echo "SQL fetching titles: ".$sql."<br />\n";
	
	$url_rows=$wpdb->get_results($sql);
	
	// echo rs_get_title($url_row->url);
	
	foreach($url_rows as $url_row){
		if($prtools_debug){
			$time_start=time();
			echo "<br>GETTING TITLE<br>URL: <a href=\"".$url_row->url."\" target=\"_blank\">".$url_row->url."</a><br />\n";
		}
		
		$title=rs_get_title($url_row->url);
		
		if($prtools_debug){
			echo "Title: ".$title."<br>";
			$time_end=time();
			echo "Request time: ".($time_end-$time_start)."<br />\n";
		}
		if($title!=""){
			$wpdb->update($prtools_url_table, array('title'=>$title) , array('url'=>$url_row->url));
		}
	}
}

function pr_tools_download_csv(){
	global $wpdb;
	global $prtools_url_table;
	
	$prtools_settings=get_option('pagerank_tools_settings');
	
	$url_fetch_interval=$prtools_settings['fetch_url_interval']*24*60*60;
	$new_url_fetch_interval=$prtools_settings['fetch_url_interval_new']*24*60*60;	
	
	$filename = "pagerank_export_".date("Y_m_d_His",time()).".csv";

	$application="text/csv";
	header( "Content-Type: $application" ); 
	header( "Content-Disposition: attachment; filename=$filename"); 
	header( "Content-Description: csv File" ); 
	header( "Pragma: no-cache" ); 
	header( "Expires: 0" );
	
	// Creating additional columns
	$sql_col_nextcheck='IF(pr=-2,lastcheck+' . $new_url_fetch_interval . ',IF(pr=-1,lastcheck+' . $new_url_fetch_interval . ',lastcheck+' . $url_fetch_interval . ')) AS nextcheck';
	$sql_col_change='IF(pr=-2,0,IF(pr=-1,IF(pr-diff_last_pr=-2,0,diff_last_pr+1),IF(pr>-1,IF(pr-diff_last_pr<0,pr,diff_last_pr),0))) AS prchange';
	
	// SQL Statement
	$sql = 'SELECT ID, entrydate, lastupdate, lastcheck, url_type, url, title, pr, diff_last_pr, pr_entries, queue, ' .$sql_col_nextcheck . ',  ' .$sql_col_change . ' FROM ' . $prtools_url_table . ' ORDER by prchange DESC, pr DESC';
	
	$sql = apply_filters( 'prtools_export_sql', $sql );
	
	// echo $sql;
	
	$prtools_rows = $wpdb->get_results( $sql );

	$content = "Pagerank;Change;Title;URL;URL Type;Last change;Last check;Next check" . chr(10);
	
	foreach($prtools_rows AS $prtools_row){
		$content.=$prtools_row->pr . ';' . $prtools_row->prchange . ';' . $prtools_row->title . ';' . $prtools_row->url . ';' . $prtools_row->url_type . ';' . date("d.m.Y",$prtools_row->lastupdate) . ';' . date("d.m.Y",$prtools_row->lastcheck) .  ';' . date("d.m.Y",$prtools_row->nextcheck) . chr(10) ;  	
	}	
	
	echo $content;
	
	exit;
}

function pr_tools_download(){
	if(isset($_GET['export'])){
		if($_GET['export']=="csv"){
			pr_tools_download_csv();
		}
	}
}
add_action( 'admin_init', 'pr_tools_download' , 10); 


/*
 * Image for pageranks
 *****************************************/
function prtools_html_pr($pr){
	global $prtools_absolute_path;
	
	if($pr==-2){
		$html="-";
	}
		
	if($pr==-1)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-na.gif" />';
	if($pr==0)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-0.gif" />';
	if($pr==1)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-1.gif" />';
	if($pr==2)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-2.gif" />';
	if($pr==3)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-3.gif" />';
	if($pr==4)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-4.gif" />';
	if($pr==5)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-5.gif" />';
	if($pr==6)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-6.gif" />';
	if($pr==7)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-7.gif" />';
	if($pr==8)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-8.gif" />';
	if($pr==9)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-9.gif" />';
	if($pr==10)$html='<img src="/' . $prtools_absolute_path . '/extended/images/pr-10.gif" />';
	
	return $html;
}

function pr_add_all_urls_to_queue(){
	global $wpdb;
	global $prtools_url_table;
	if(isset($_GET['update_all'])){
		if($_GET['update_all']!=""){
			global $wpdb;
			
			$sql="SELECT * FROM ".$prtools_url_table;
			$url_rows=$wpdb->get_results($sql);
			
			// print_r_html($url_rows);
		
			foreach($url_rows AS $url_row){
				pr_add_url_to_queue($url_row->url);
			}
		}
	}
}
add_action( 'admin_init', 'pr_add_all_urls_to_queue' , 10); 

function pr_add_url_to_queue($url){
	global $prtools_debug;
	global $wpdb;
	global $prtools_url_table;
	
	
	if($prtools_debug) echo "Queued ".$url." for updating pr<br />";
	
	$wpdb->update($prtools_url_table, array('queue'=>1), array('url'=>$url));
}



?>