<?php

function fetch_pr(){
	global $wpdb;
	global $prtools_debug;
	global $prtools_url_table;
	global $prtools_pr_table;
	
	$prtools_settings=get_option('pagerank_tools_settings');

	$lastcheck=time()-$prtools_settings['fetch_interval']*60;
	$lastcheck_url=time()-$prtools_settings['fetch_url_interval']*24*60*60;
	$lastcheck_url_new=time()-$prtools_settings['fetch_url_interval_new']*24*60*60;

	$sql="SELECT * FROM ".$prtools_url_table." ORDER BY lastcheck DESC LIMIT 0,1";
	$prtools_last = $wpdb->get_row($sql);
	
	$sql="SELECT * FROM ".$prtools_url_table." WHERE queue=1 LIMIT 0,1";
	$queued_urls = $wpdb->get_results($sql);

	/*
	echo "LU: ".$prtools_last->lastupdate."<br>";
	$s=time()-$prtools_last->lastupdate;
	echo "S: ".$s."<br>";
	echo "T: ".time()."<br>";
	*/

	/*
	* Checking time of last pr request
	***************************************/

	if($prtools_last->lastcheck<$lastcheck || $queued_urls>0){

		// Getting sites which are updatable by settings

		$sql="SELECT * FROM ".$prtools_url_table." WHERE (lastcheck<".$lastcheck_url." OR (lastcheck<".$lastcheck_url_new." AND pr=-1) OR (lastcheck<".$lastcheck_url_new." AND pr=-2)) OR queue=1 ORDER BY pr DESC LIMIT 0,".$prtools_settings['fetch_num'];
		
		// $sql="SELECT * FROM ".$prtools_url_table." WHERE lastcheck<=".$lastcheck_url." ORDER BY pr DESC LIMIT 0,".$prtools_settings['fetch_num'];
		
		$url_rows=$wpdb->get_results($sql);
		
		if($prtools_debug) echo $sql."<br />\n";
		if($prtools_debug) echo "Lastcheck to be maximum at: " . date("d.m.Y - H:i",$lastcheck)."<br />";
		if($prtools_debug) echo "Lastcheck URL have to be maximum at: " . date("d.m.Y - H:i",$lastcheck_url)."<br />";
		if($prtools_debug) echo "Lastcheck of new URL have to be maximum at: " . date("d.m.Y - H:i",$lastcheck_url)."<br />";
		if($prtools_debug) print_r_html($prtools_settings);
		if($prtools_debug) print_r_html($url_rows);
	
		/*
		* Checking pageranks
		***************************************/	

		foreach($url_rows AS $url_row){
			if($prtools_debug){
				$time_start=time();
			}
			
			if($prtools_debug) echo $lastcheck . " > " . $prtools_settings['last_google_request']."<br />";
			
			if($lastcheck>$prtools_settings['last_google_request']){
						
				$pr=getpagerank($url_row->url);
				
				if($url_row->queue==1){
					if($prtools_debug) echo "GETTING PR FOR QUEUE<br>URL: ".$url_row->url."<br />\n";
				}else{
					if($prtools_debug) echo "GETTING PR<br>URL: ".$url_row->url."<br />\n";
				}
				
				if($prtools_debug){
					$time_end=time();
					echo "Request time: ".($time_end-$time_start)."<br />\n";
				}	
				
				if($pr==""){$pr=-1;}
	
				// If PR is a new rank update and insert
				
				$actual_pr=(int) $pr;
				$last_pr=(int) $url_row->pr;
				
				if($prtools_debug) echo "URL: ".$url_row->url."<br />";
				if($prtools_debug) echo "New PR: ".$actual_pr." <br />Old PR: ".$last_pr."<br />";
	
				if($actual_pr!=$last_pr){
					
					if($prtools_debug) echo "<b>updating</b><br /><br />";	
	
					$diff_last_pr = $actual_pr - $last_pr;
					$url_row->pr_entries++;
	
					$wpdb->update($prtools_url_table, array('lastcheck'=>time(),'lastupdate'=>time(),'pr'=>$actual_pr,'diff_last_pr'=>$diff_last_pr, 'pr_entries'=>$url_row->pr_entries, 'queue' => 0), array('url'=>$url_row->url));
					$wpdb->insert($prtools_pr_table,array('entrydate'=>time(),'url'=>$url_row->url,'pr'=>$actual_pr));
					
					do_action( 'prtools_update_pr',$url_row,$actual_pr,$last_pr);
					
				}else{
					if($prtools_debug) echo "<b>not updating</b><br /><br />";
					$wpdb->update($prtools_url_table, array('lastcheck'=>time(), 'queue' => 0), array('url'=>$url_row->url));					
				}
			}

			
		}
		$prtools_settings['last_google_request']=time();
		update_option('pagerank_tools_settings',$prtools_settings);			
	}
}

function fetch_pr_sidewide(){
	global $wpdb;
	global $prtools_debug;
	global $prtools_url_table;
	global $prtools_pr_table;

	// Getting sites which are updatable by settings
	$sql="SELECT * FROM ".$prtools_url_table;
	$url_rows=$wpdb->get_results($sql);
	
	/*
	* Checking pageranks
	***************************************/	
	foreach($url_rows AS $url_row){
		if($prtools_debug){
			$time_start=time();
			echo "GETTING PR<br>URL: ".$url_row->url."<br />\n";
		}		
		$pr=getpagerank($url_row->url);
		if($prtools_debug){
			$time_end=time();
			echo "Request time: ".($time_end-$time_start)."<br />\n";
		}	
		if($pr==""){$pr=-1;}
		
		// echo "PR: ".$pr." (".$prtools_row->url.")<br>";
		
		$wpdb->update($prtools_url_table, array('lastupdate'=>time(),'pr'=>$pr), array('url'=>$url_row->url));
		// If PR is a new rank update and insert
		if($pr!=$url_row->pr){
				$diff_last_pr = $pr - $url_row->pr;
				$url_row->pr_entries++;
				
				$wpdb->update($prtools_url_table, array('lastupdate'=>time(),'pr'=>$pr,'diff_last_pr'=>$diff_last_pr, 'pr_entries'=>$url_row->pr_entries), array('url'=>$url_row->url));
				$wpdb->insert($prtools_pr_table,array('entrydate'=>time(),'url'=>$url_row->url,'pr'=>$pr));
		}	
	}
}

function update_url_table($update_posts=true,$update_pages=true){
	global $wpdb;
	global $prtools_url_table;
	global $prtools_pr_table;
	
	if($update_posts){
		$urls=wp_get_post_urls();
		foreach($urls AS $url){pr_insert_url($url);}
	}
	
	if($update_pages){
		$urls=wp_get_page_urls();
		foreach($urls AS $url){pr_insert_url($url);}
	}
	
	$urls=wp_get_cat_urls();
	foreach($urls AS $url){pr_insert_url($url);}
	
	$urls=wp_get_tag_urls();
	foreach($urls AS $url){pr_insert_url($url);}
	
	$home=get_bloginfo("url").'/';
	pr_insert_url($home);
}

function pr_insert_url_by_post_ID( $post_ID ){
	$post=get_post( $post_ID );
	if( $post->post_status == "publish" ){			
		$url = get_permalink( $post_ID );
		pr_insert_url( $url );
	}
}
add_action('save_post' , 'pr_insert_url_by_post_ID', 1, 1);

function pr_insert_url($url){
	global $wpdb;
	global $prtools_url_table;
	global $prtools_pr_table;
		
	$prtools_rows = $wpdb->get_results( "SELECT * FROM ".$prtools_url_table." WHERE url='".$url."'");
	if(count($prtools_rows)==0){
		$wpdb->insert($prtools_url_table,array('entrydate'=>time(),'lastupdate'=>time(),'url_type'=>'post','url'=>$url,'pr'=>-2));
		$wpdb->insert($prtools_pr_table,array('entrydate'=>time(),'url'=>$url,'pr'=>-2));
	}
}

function pr_save_settings(){
	if(isset($_POST['savesettings'])){
		if($_POST['savesettings']!=""){
			
			$prtools_settings=get_option('pagerank_tools_settings');
		
			$prtools_settings['fetch_interval']=$_POST['fetch_interval'];
			$prtools_settings['fetch_url_interval']=$_POST['fetch_url_interval'];
			$prtools_settings['fetch_url_interval_new']=$_POST['fetch_url_interval_new'];
			$prtools_settings['fetch_num']=$_POST['fetch_num'];
		
			update_option('pagerank_tools_settings',$prtools_settings);	
			
			do_action( 'prtools_settings_save');
		}
	}
}
add_action( 'admin_init', 'pr_save_settings' , 10); 

function pr_delete_url(){
	global $wpdb;
	global $prtools_url_table;
	global $prtools_pr_table;
		
	if(isset($_GET['delete_url'])){
		if($_GET['delete_url']!=""){
			$sql = 'DELETE FROM ' . $prtools_url_table . ' WHERE url="' . $_GET['delete_url'] . '"';
			$wpdb->query($sql);
			
			$sql = 'DELETE FROM ' . $prtools_pr_table . ' WHERE url="' . $_GET['delete_url'] . '"';
			$wpdb->query($sql);									
		}
	}
}
add_action( 'admin_init', 'pr_delete_url' , 10); 

if(!$prtools_extended){
	function pr_get_pro_stats(){
		global $prtools_plugin_path;
		echo '<div class="statistics">
				<div class="diagram" style="margin:0.83em 0;">
					<a class="get_pro" href="#" title="Get professional version of pagerank tools"><img src="' . $prtools_plugin_path . '/images/statistics_get_pro.jpg" alt="Pagerank overview" border="0"></a>
			</div>
		</div>';
	
	}
	add_action( 'prtools_main_head', 'pr_get_pro_stats' , 10, 0);
	
	function pr_get_pro_footer(){
		echo '<a class="get_pro" href="#" title="Get professional version of pagerank tools"><img src="' . $prtools_plugin_path . '/images/footer_get_pro.jpg" alt="Pagerank overview" border="0"></a>';
	}
	add_action( 'prtools_main_bottom', 'pr_get_pro_footer' , 10, 0);
	
	 function pr_get_pro_email(){
		echo '<br /><br /><a class="get_pro" href="#" title="Get professional version of pagerank tools"><img src="' . $prtools_plugin_path . '/images/email_get_pro.jpg" /></a>';
		
	}	
	add_action( 'prtools_settings_page', 'pr_get_pro_email' , 10, 0); 
}


?>