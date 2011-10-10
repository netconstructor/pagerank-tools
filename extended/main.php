<?php 

/*
 * Extend SQL Statement
 *****************************************/
function prtools_pro_main_sql(){
	global $wpdb, $prtools_order, $prtools_limit, $prtools_start, $prtools_sum_urls, $prtools_data, $prtools_debug;
	$prtools_settings=get_option('pagerank_tools_settings');
	
	$url_fetch_interval=$prtools_settings['fetch_url_interval']*24*60*60;
	$new_url_fetch_interval=$prtools_settings['fetch_url_interval_new']*24*60*60;

	$table_name = $wpdb->prefix . 'prtools_url';
		
	// Order
	$prtools_order='pr';
	if($_GET['order']!=""){
		$prtools_order=$_GET['order'];
	}else{
		// Order
		$prtools_order='pr';		
	}
	
	// Direction
	$direction="DESC";
	if($_GET['direction']!="")$direction=$_GET['direction'];
	
	// Limit
	$prtools_limit=25;
	if($_GET['limit']!="")$prtools_limit=$_GET['limit'];
	
	// Start
	$prtools_start=0;
	if($_GET['start']!="")$prtools_start=$_GET['start'];
	
	// Pagerank
	$pagerank="";
	if($_GET['pagerank']!="")$pagerank=$_GET['pagerank'];	
		
	// Creating additional columns
	$sql_col_nextcheck='IF(pr=-2,lastcheck+' . $new_url_fetch_interval . ',IF(pr=-1,lastcheck+' . $new_url_fetch_interval . ',lastcheck+' . $url_fetch_interval . ')) AS nextcheck';
	$sql_col_change='IF(pr=-2,0,IF(pr=-1,IF(pr-diff_last_pr=-2,0,diff_last_pr+1),IF(pr>-1,IF(pr-diff_last_pr<0,pr,diff_last_pr),0))) AS prchange';
	
	// SQL Statement
	$sql = 'SELECT ID, entrydate, lastupdate, lastcheck, url_type, url, title, pr, diff_last_pr, pr_entries, queue, ' .$sql_col_nextcheck . ',  ' .$sql_col_change . ' FROM '.$table_name;
	
	if($pagerank!=""){
		$sql.=' WHERE pr="' . $pagerank . '"';
	}
	


	if($_GET['order']=="nextcheck"){
		if($direction=="DESC"){
			// $prtools_order=" ";
			$sql.= ' ORDER by queue DESC, nextcheck ASC';
		}elseif($direction=="ASC"){
			$sql.= ' ORDER by queue ASC, nextcheck DESC';
		}
		
	}elseif($_GET['order']=="prchange"){
		if($direction=="DESC"){
			// $prtools_order=" ";
			$sql.= ' ORDER by prchange DESC, pr DESC';
		}elseif($direction=="ASC"){
			$sql.= ' ORDER by prchange ASC, pr ASC';
		}		
		
	}else{
		$sql.= ' ORDER by ' . $prtools_order . ' ' . $direction;
	}
	
	if($prtools_limit!="all"){
		$sql.= ' LIMIT ' . $prtools_start . ', ' . $prtools_limit ;
	}
	
	if($prtools_debug)echo $sql."<br />\n";
	
	/*
	echo $_GET['order']."<br />\n";
	echo $_GET['direction']."<br />\n";
	
	echo $sql;
	*/
	// $prtools_page=$_SERVER['PHP_SELF'] . "?page=" . $_GET['page'];
	
	return $sql;
}
add_filter( 'prtools_main_sql', 'prtools_pro_main_sql' , 10, 0);

/*
 * Mini statistics in main view
 *****************************************/
function prtools_pro_ministat(){ 
	global $prtools_limit, $prtools_start, $prtools_sum_urls, $prtools_sum_urls_query;
	global $prtools_absolute_path;
	global $prtools_plugin_path;	
	
	$order=$_GET['order'];
	$direction=$_GET['direction'];
	$start=$_GET['start'];
	$limit=$_GET['limit'];
	$pagerank=$_GET['pagerank'];
	$page=$_GET['page'];
	
	?>
    <div class="statistics">
        <div class="diagram" style="width:700px;margin:0.83em 0;">
            <img src="<?php echo $prtools_plugin_path; ?>/extended/stat_graph.php" alt="Pagerank overview" width="700" height="230" >
        </div>
        <div class="info" style="float:left;width:200px;margin:0.83em 0;">
            <table width="200" class="widefat">
            	<thead>
                    <tr>
                        <th colspan="2"><?php _e('Statistic','prtools'); ?></th>
                     </tr>
                 </thead>
                <tr>
                    <td><?php _e('URLs','prtools'); ?></td>
                    <td><?php echo $prtools_sum_urls; ?></td>
                 </tr>
                 <tr>
                    <td><?php _e('Showing','prtools'); ?></td>
                    <td><?php echo $prtools_sum_urls_query; ?></td>
                 </tr>
                 <tr>
                    <td><?php _e('from - to','prtools'); ?></td>
                    <?php if($limit=="all"){ ?>
                    <td><?php echo $prtools_start+1; ?> - <?php echo $prtools_sum_urls; ?></td>
                    <?php }else{ ?>
                    <td><?php echo $prtools_start+1; ?> - <?php echo $prtools_start+$prtools_limit; ?></td>
                    <?php } ?>						
                 </tr>
            </table>
        </div>
        
        <div class="filter" style="width:200px;margin:0.83em 0 0.83em 1em;">
        	<form name="filter" id="filer" method="get" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <table width="200" class="widefat">
                    <thead>
                        <tr>
                            <th colspan="2"><?php _e('Filter results','prtools'); ?></th>
                         </tr>
                     </thead>
                     <tr>
                        <td><?php _e('Pagerank','prtools'); ?></td>
                        <td>
                        <select name="pagerank" onchange="document.filter.submit();">
                            <option value=""<?php if($pagerank=="") echo " selected"; ?>>-</option>
                            <option value="-2"<?php if($pagerank=="-2") echo " selected"; ?>>none</option>
                            <option value="-1"<?php if($pagerank=="-1") echo " selected"; ?>>n/a</option>
                            <option value="0"<?php if($pagerank=="0") echo " selected"; ?>>0</option>
                            <option value="1"<?php if($pagerank=="1") echo " selected"; ?>>1</option>
                            <option value="2"<?php if($pagerank=="2") echo " selected"; ?>>2</option>
                            <option value="3"<?php if($pagerank=="3") echo " selected"; ?>>3</option>
                            <option value="4"<?php if($pagerank=="4") echo " selected"; ?>>4</option>
                            <option value="5"<?php if($pagerank=="5") echo " selected"; ?>>5</option>
                            <option value="6"<?php if($pagerank=="6") echo " selected"; ?>>6</option>
                            <option value="7"<?php if($pagerank=="7") echo " selected"; ?>>7</option>
                            <option value="8"<?php if($pagerank=="8") echo " selected"; ?>>8</option>
                            <option value="9"<?php if($pagerank=="9") echo " selected"; ?>>9</option>
                            <option value="10"<?php if($pagerank=="10") echo " selected"; ?>>10</option>
                        </select>
                        </td>
                     </tr>
                     <tr>
                        <td><?php _e('Num of entries','prtools'); ?></td>
                        <td>
                        <select name="limit" onchange="document.filter.submit();">
                            <option value="25"<?php if($limit==25) echo " selected"; ?>>25</option>
                            <option value="50"<?php if($limit==50) echo " selected"; ?>>50</option>
                            <option value="100"<?php if($limit==100) echo " selected"; ?>>100</option>
                            <option value="250"<?php if($limit==200) echo " selected"; ?>>250</option>
                            <option value="500"<?php if($limit==500) echo " selected"; ?>>500</option>
                            <option value="1000"<?php if($limit==1000) echo " selected"; ?>>1000</option>
                            <option value="all"<?php if($limit=="all") echo " selected"; ?>>all</option>
                        </select>
                        </td>
                     </tr>
                </table>
                <input type="hidden" name="start" value="<?php echo $start; ?>" />                
                <input type="hidden" name="order" value="<?php echo $order; ?>" />
                <input type="hidden" name="direction" value="<?php echo $direction; ?>" /> 
                <input type="hidden" name="page" value="<?php echo $page; ?>" />                
            </form>
        </div>
        <div class="filter" style="width:200px;margin:0.83em 0 0.83em 1em;">
            <table width="200" class="widefat">
            	<thead>
                    <tr>
                        <th colspan="2"><?php _e('Export','prtools'); ?></th>
                     </tr>
                 </thead>
                 <tr>
                    <td><a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&export=csv" title="Export"><?php _e('Download as CSV','prtools'); ?></a></td>
                    <td>
                    	<a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&export=csv" title="Export"><img src="/<?php echo $prtools_absolute_path; ?>/extended/images/icon-download.png"alt="Export" border="0" /></a>
                    </td>
                 </tr>
                 
            </table>
        </div>
        
        <div class="filter" style="width:200px;margin:0.83em 0 0.83em 1em;">
	        <form name="actions" id="actions" method="get" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <table width="200" class="widefat">
            	<thead>
                    <tr>
                        <th><?php _e('Actions','prtools'); ?></th>
                     </tr>
                 </thead>
                 <tr>
                    <td><input type="button" name="update_all" value="<?php _e('Add all urls to updatequeue','prtools'); ?>" onclick="question_redirect('<?php _e('Do you really want to add all urls to queue?','prtools'); ?>','<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) ?>&update_all=true');" /></td>
                 </tr>
                 
            </table>
				<input type="hidden" name="start" value="<?php echo $start; ?>" />                
                <input type="hidden" name="order" value="<?php echo $order; ?>" />
                <input type="hidden" name="direction" value="<?php echo $direction; ?>" /> 
                <input type="hidden" name="page" value="<?php echo $page; ?>" />              
            </form>
        </div>
    </div>
	<?php 
} 
add_action( 'prtools_main_head', 'prtools_pro_ministat' , 10, 0);



/*
 * Tablehead of main view
 *****************************************/
function prtools_main_tablehead($tablehead){
	
	$page=$_SERVER['PHP_SELF'] . "?page=" . $_GET['page'];
	
	// Directipon
	$direction="DESC";
	if($_GET['direction']!="")$direction=$_GET['direction'];

	// Order
	$order="pr";
	if($_GET['order']!="")$order=$_GET['order'];
	
	// Reordering
	if($order=="pr" && $direction=="DESC"){
		$pr_direction="ASC";
	}else{
		$pr_direction="DESC";
	}
	
	// Limit
	$limit=25;
	if($_GET['limit']!="")$limit=$_GET['limit'];
	
	// Start
	$start=0;
	if($_GET['start']!="")$start=$_GET['start'];
	
	// Pagerank
	$pagerank="";
	if($_GET['pagerank']!="")$pagerank=$_GET['pagerank'];		
	
	if($order=="prchange" && $direction=="DESC"){
		$prchange_direction="ASC";
	}else{
		$prchange_direction="DESC";
	}
	
	if($order=="url" && $direction=="DESC"){
		$url_direction="ASC";
	}else{
		$url_direction="DESC";
	}
	
	if($order=="title" && $direction=="DESC"){
		$title_direction="ASC";
	}else{
		$title_direction="DESC";
	}
	
	if($order=="url_type" && $direction=="DESC"){
		$url_type_direction="ASC";
	}else{
		$url_type_direction="DESC";
	}
	
	if($order=="lastupdate" && $direction=="DESC"){
		$lastupdate_direction="ASC";
	}else{
		$lastupdate_direction="DESC";
	}
	
	if($order=="lastcheck" && $direction=="DESC"){
		$lastcheck_direction="ASC";
	}else{
		$lastcheck_direction="DESC";
	}
	
	if($order=="nextcheck" && $direction=="DESC"){
		$nextcheck_direction="ASC";
	}else{
		$nextcheck_direction="DESC";
	}	
	
	
	$tablehead = '<thead><tr class="pagerankhead">';
    $tablehead.= '<th scope="col"><a href="' . $page. '&order=pr&direction=' . $pr_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('PR','prtools') . '</a></th>';
	$tablehead.= '<th scope="col"><a href="' . $page. '&order=prchange&direction=' . $prchange_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('+/-','prtools') . '</a></th>';
	
	if(ini_get('allow_url_fopen')==1){
		$tablehead.= '<th scope="col"><a href="' . $page. '&order=title&direction=' . $title_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('Title','prtools') . '</a> / <a href="' . $page. '&order=url&direction=' . $url_direction . '">' . __('URL','prtools') . '</a></th><th scope="col">&nbsp;</th>';
	}else{
		$tablehead.= '<th scope="col"><a href="' . $page. '&order=url&direction=' . $url_direction . '">' . __('URL','prtools') . '</a></th><th scope="col">&nbsp;</th>';
	}

	$tablehead.= '<th scope="col"><a href="' . $page. '&order=url_type&direction=' . $url_type_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('Type','prtools') . '</a></th>';
	
	
	$tablehead.= '<th scope="col"><a href="' . $page. '&order=lastupdate&direction=' . $lastupdate_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('Last change','prtools') . '</a></th>';
	$tablehead.= '<th scope="col"><a href="' . $page. '&order=lastcheck&direction=' . $lastcheck_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('Last check','prtools') . '</a></th>';
		$tablehead.= '<th scope="col"><a href="' . $page. '&order=nextcheck&direction=' . $nextcheck_direction . '&start=' . $start . '&limit=' . $limit . '&pagerank=' . $pagerank . '">' . __('Next check','prtools') . '</a></th>';
	$tablehead.= '</tr></thead>';

	return $tablehead;
}
add_filter( 'prtools_main_tablehead', 'prtools_main_tablehead' );

/*
 * Tablerow of main view
 *****************************************/
function prtools_main_tablerow($tablerow,$row){
	global $prtools_absolute_path;
	global $prtools_plugin_path;
	
	$prtools_settings=get_option('pagerank_tools_settings');
	
	$page=$_SERVER['PHP_SELF'] . "?page=" . $_GET['page'];
	
	$title = $row->title;
	if( $title == "" ){
		$title="<i>(" . __('Title not read yet, processing soon ...','prtools') . ")</i>";
	}else{
		$title = "" . $title . "";		
	}
	
	// Getting pagerannk image
	$pr=prtools_html_pr($row->pr);	

	// Change of pagerannk
	if($row->prchange>0){
		$change = '<span style="color:#5eaa5e;">+' . $row->prchange . '</span><br />';
	}
	if($row->prchange<0){
		$change = '<span style="color:#F00;">' . $row->prchange . '</span><br />';
	}
	if($row->pr-$row->diff_last_pr<0){
		$change.= '<span style="font-size:10px; color:#CCC;">(NEW)</span>';
	}else{
		$change.= '<span style="font-size:10px; color:#CCC;">(PR' . ($row->pr - $row->diff_last_pr) . ')</span>';
	}	
	if($row->prchange==0){
		$change = "-";
	}


	// Calculating next check
	if($row->queue==1){
		$next_update="Immediately";
	}else{
		if($row->pr==-2 || $row->pr==-1){
			$next_update=$row->lastcheck+($prtools_settings['fetch_url_interval_new']*24*60*60);
		}else{
			$next_update=$row->lastcheck+($prtools_settings['fetch_url_interval']*24*60*60);			
		}
		if($row->lastcheck!=0){
			$next_update=date("d.m.Y",$next_update);		
		}else{
			$next_update="Immediately";
		} 
	}

	// Calculating last check
	if($row->lastcheck!=0){
		$lastcheck=date("d.m.Y",$row->lastcheck);
	}else{
		$lastcheck="-";
	}
	
	$tablerow = '<tr>';
	
	$tablerow.= '<td scope="row">' . $pr . '</td>';
	$tablerow.= '<td scope="row">' . $change . '</td>';	
	
	// $tablerow.= '<td scope="row">' . $pr . '<br />' . $row->pr . '</td>';
	// $tablerow.= '<td scope="row">' . $change . '<br />' . $row->diff_last_pr . '</td>';
	
	if(ini_get('allow_url_fopen')==1){
		$tablerow.= '<td scope="row"><a href="' . $row->url . '" target="_blank">' . $title . '</a><br /><a href="' . $row->url . '" target="_blank" style="color:#CCC;">' . $row->url . '</a></td>';
	}else{
		$tablerow.= '<td scope="row"><a href="' . $row->url . '" target="_blank">' . $row->url . '</a></td>';
	}
	
	$tablerow.= '<td scope="row" width="75">
					<a href="' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '&url=' . $row->url . '" title="Details">
						<img src="' . $prtools_plugin_path . '/extended/images/icon-info.png" border="0" alt="Details" />
				    </a>
					<a href="' . $row->url . '" target="_blank" border="0" title="Visit">
						<img src="' . $prtools_plugin_path . '/extended/images/icon-visit.png" border="0" alt="Visit" />
					</a>
					<a href="#" border="0" title="Delete" onclick="question_redirect(\'' . __('Do you really want to delete','prtools') . ' ' . $row->url . ' and all data from Pagerank tools?\', \'' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '&delete_url=' . $row->url . '\' );">
					<img src="' . $prtools_plugin_path . '/extended/images/icon-delete.png" border="0" alt="Visit" />
					</a>
				 </td>';
				 
	$tablerow.= '<td scope="row">' . $row->url_type . '</td>';
	$tablerow.= '<td scope="row">' . $row->date . '</td>';
	
	$tablerow.= '<td scope="row">' . $lastcheck . '</td>';
	
	$tablerow.= '<td scope="row">' . $next_update . '</td>';
	$tablerow.= '</tr>';
	
	return $tablerow;
}
add_filter( 'prtools_main_tablerow', 'prtools_main_tablerow', 1, 2 );

/*
 * Navigation under main view
 *****************************************/
function prtools_navigation(){
	
	global $prtools_limit, $prtools_start, $prtools_sum_urls, $prtools_data;

	if($_GET['url']=="" && $_GET['limit']!='all' && $prtools_sum_urls>($prtools_limit-$prtools_start)){
			
		$page=$_SERVER['PHP_SELF'] . "?page=" . $_GET['page'];
		
		$prtools_limit=30;
				
		if($_GET['limit']!=""){
			$prtools_limit=$_GET['limit'];
		}
		
		$prtools_start=0;
		
		if($_GET['start']!=""){
			$prtools_start=$_GET['start'];
		}
		
		$prtools_order="pr";
		
		if($_GET['order']!=""){
			$prtools_order=$_GET['order'];
	
		}
		
		// Pagerank
		$pagerank="";
		if($_GET['pagerank']!="")$pagerank=$_GET['pagerank'];		
		
		if($order=="diff_last_pr" && $direction=="DESC"){
			$diff_last_pr_direction="ASC";
		}else{
			$diff_last_pr_direction="DESC";
		}
		
		$prtools_direction="DESC";
		
		if($_GET['direction']!=""){
			$prtools_direction=$_GET['direction'];
		}
		
		if($prtools_debug) echo "S: " . $prtools_start." L: " . $prtools_limit;			
		
		if( ($prtools_start-$prtools_limit) > -1 ){
			$prtools_start_prev=$prtools_start-$prtools_limit;
			$prev_link = $page . '&start=' . $prtools_start_prev . '&limit=' . $prtools_limit."&order=".$prtools_order."&direction=".$prtools_direction."&pagerank=".$pagerank;
			echo '<a href="' . $prev_link . '" class="prtools_button">' . __('Previous','prtools') . '</a> ';	
		}else if($prtools_debug){
			$prtools_start_prev=$prtools_start-$prtools_limit;
			$prev_link = $page . '&start=' . $prtools_start_prev . '&limit=' . $prtools_limit."&order=".$prtools_order."&direction=".$prtools_direction."&pagerank=".$pagerank;
			echo '(<a href="' . $prev_link . '" class="prtools_button">' . __('Previous','prtools') . '</a>)';			
			
		}
		
		if($prtools_debug) echo "Summe: " . $prtools_sum_urls;
		
		if( $prtools_sum_urls > ($prtools_start+$prtools_limit)){
			$prtools_start_next=$prtools_start+$prtools_limit;
			$next_link = $page . '&start=' . $prtools_start_next . '&limit=' . $prtools_limit."&order=".$prtools_order."&direction=".$prtools_direction;
			echo '<a href="' . $next_link . '" class="prtools_button">' . __('Next','prtools') . '</a>';		
		}else if($prtools_debug){
			$prtools_start_next=$prtools_start+$prtools_limit;
			$next_link = $page . '&start=' . $prtools_start_next . '&limit=' . $prtools_limit."&order=".$prtools_order."&direction=".$prtools_direction;
			echo '(<a href="' . $next_link . '" class="prtools_button">' . __('Next','prtools') . '</a>)';			
		}
	}
	//echo '<a href="' . $prev_link . '" class="prtools_button">' . __('Previous','prtools') . '</a> ';	
	
}
add_action( 'prtools_main_bottom', 'prtools_navigation' , 10, 0);

?>