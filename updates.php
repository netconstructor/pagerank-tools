<?php
/*
* Update Scripts
***************************************/

/**
 * Updating database from Version 0.2 or less
 */
function cleanup_db_from_02(){
	global $prtools_debug;
	global $wpdb;
	global $prtools_url_table;
	global $prtools_pr_table;
	
	// Updating table
	$sql = "ALTER TABLE " . $prtools_url_table. " ADD diff_last_pr INT( 1 ) NOT NULL AFTER pr";
	$wpdb->query($sql);	
	if($prtools_debug)echo $sql."<br />";
	
	$sql = "ALTER TABLE " . $prtools_url_table. " ADD pr_entries INT( 11 ) NOT NULL AFTER diff_last_pr";
	$wpdb->query($sql);	
	if($prtools_debug)echo $sql."<br />";
		
	$sql = "ALTER TABLE " . $prtools_url_table. " ADD lastcheck INT( 11 ) NOT NULL AFTER lastupdate";
	$wpdb->query($sql);	
	if($prtools_debug)echo $sql."<br />";
		
	$sql = "ALTER TABLE " . $prtools_url_table. " ADD title TEXT NOT NULL AFTER url";
	$wpdb->query($sql);	
	if($prtools_debug)echo $sql."<br />";	
			
	// Creating missing pagerank entries in pagerank table
	$sql = "SELECT * FROM " . $prtools_url_table;
	$url_rows = $wpdb->get_results($sql);
	
	if($prtools_debug)echo $sql."<br />";
	if($prtools_debug)echo "<hr />";

	$updated=false;

	foreach($url_rows AS $url_row){

		// Counting all entries of one url
		$sql = "SELECT * FROM " . $prtools_pr_table . " WHERE url='" . $url_row->url . "' ORDER BY entrydate DESC";
		$pr_rows = $wpdb->get_results($sql);
		$entries=count($pr_rows);
		
		if($prtools_debug)echo "<b>URL: ".$url_row->url."</b><br />";
		if($prtools_debug)echo "URL Table<br /";
		if($prtools_debug)print_r_html($url_row);
		if($prtools_debug)echo "PR Table<br /";
		if($prtools_debug)print_r_html($pr_rows);
						
		// No entry exists
		if($entries==0){
			
			$diff_last_pr = $url_row->pr - (-2);
			
			if($prtools_debug)echo "No entry found<br />Adding first google request log entry with PR (" . $url_row->pr . ")<br />";			
			$wpdb->insert($prtools_pr_table,array('entrydate'=>$url_row->lastupdate,'url'=>$url_row->url,'pr'=>$url_row->pr));
			
			// Adding first creation log entry		
			if($prtools_debug)echo "Adding first creation log entry with timestamp (".$url_row->entrydate.")<br />";
			$wpdb->insert($prtools_pr_table,array('entrydate'=>$url_row->entrydate,'url'=>$url_row->url,'pr'=>-2));				
			
			
			if($prtools_debug)echo "Updating Number of entries (2) and setting diff to (" . $diff_last_pr . ")<br />";
			$wpdb->update($prtools_url_table, array('diff_last_pr'=>$diff_last_pr, 'pr_entries'=>2), array('url'=>$url_row->url));			

		// Entry / entries existing
		}else{			
			// If more than one entry is in pr table -> set data for difference to last pr and num of entries
			if( $entries > 1 ){
				
				if($prtools_debug)echo $entries." entries found<br />";
				// Checking rows for double entries - Only changes on pagerank have to be logged
				
				if($prtools_debug)echo "Checking for double entries<br />";
				$sql = "SELECT * FROM " . $prtools_pr_table . " WHERE url='" . $url_row->url . "' ORDER BY entrydate DESC";
				$pr_rows = $wpdb->get_results($sql);
							
				for( $i=0 ; $i < count($pr_rows); $i++ ) {
					if ( $pr_rows[$i]->pr == $pr_rows[($i+1)]->pr && $pr_rows[($i+1)]->ID !=""){
						// Deleting double entry
						$sql = "DELETE FROM " . $prtools_pr_table . " WHERE ID=" . $pr_rows[$i+1]->ID;
						$wpdb->query( $sql );
						if($prtools_debug)echo "Deleting double entry: " . $pr_rows[$i]->url . " : " . $pr_rows[$i]->ID . "<br />";
					}elseif($pr_rows[($i+1)]->ID !=""){
						if($prtools_debug)echo "Found a change of PR.<br />";
					}
				}
				
				// Getting new resultlist 
				$sql = "SELECT * FROM " . $prtools_pr_table . " WHERE url='" . $url_row->url . "' ORDER BY entrydate desc";
				$pr_rows = $wpdb->get_results($sql);
				$entries=count($pr_rows);

				if( $entries > 1 ){
					if($prtools_debug)echo "More than one entries left<br />";
					$diff_last_pr = $pr_rows[count($entries)-1]->pr - $pr_rows[count($entries)]->pr;

					if($prtools_debug)print_r_html($pr_rows);
					if($prtools_debug)echo $pr_rows[count($entries)-1]->pr . " - " . $pr_rows[count($entries)]->pr . " = " . $diff_last_pr . "<br />";
					
					$pr_arr=array();
					foreach($pr_rows AS $pr_row){
						array_push($pr_arr,$pr_row->pr);
					}
					
					$start_entry=0;
					if(!in_array(-2,$pr_arr)){
						// Adding first creation log entry
						if($prtools_debug)echo "Adding first creation log entry with timestamp (".$url_row->entrydate.")<br />";
						$wpdb->insert($prtools_pr_table,array('entrydate'=>$url_row->entrydate,'url'=>$url_row->url,'pr'=>-2));	
						$start_entry++;			
					}
					
					
					if($prtools_debug)echo "Updating Number of entries (" . ($entries+$start_entry) . "), setting diff to (" . $diff_last_pr . ") and PR (" . $pr_rows[count($entries)-1]->pr . ")<br />";
					$wpdb->update($prtools_url_table, array('diff_last_pr'=>$diff_last_pr, 'pr_entries'=>($entries+$start_entry), 'pr'=>$pr_rows[count($entries)-1]->pr), array('url'=>$url_row->url));
				}else{
					$diff_last_pr = $pr_rows[0]->pr - (-2);

					if($prtools_debug)echo "Only one entry left with PR (" . $pr_rows[0]->pr . ")<br />";

					// Adding first creation log entry		
					if($prtools_debug)echo "Adding first creation log entry with timestamp (".$url_row->entrydate.")<br />";
					$wpdb->insert($prtools_pr_table,array('entrydate'=>$url_row->entrydate,'url'=>$url_row->url,'pr'=>-2));				
					
					
					if($prtools_debug)echo "Updating Number of entries (2), setting diff ("  . $diff_last_pr . ") and PR (" . $pr_rows[count($entries)-1]->pr . ")<br />";
					$wpdb->update($prtools_url_table, array('diff_last_pr'=>$diff_last_pr, 'pr_entries'=>2, 'pr'=>$pr_rows[count($entries)-1]->pr), array('url'=>$url_row->url));
				}			
			}else{
				
				$diff_last_pr = $pr_rows[0]->pr - (-2);
				if($prtools_debug)echo "Only one entry found PR (" . $pr_rows[0]->pr . ")<br />";
				
				// Adding first creation log entry		
				if($prtools_debug)echo "Adding first creation log entry with timestamp (".$url_row->entrydate.")<br />";
				$wpdb->insert($prtools_pr_table,array('entrydate'=>$url_row->entrydate,'url'=>$url_row->url,'pr'=>-2));				
				
				if($prtools_debug)echo "Updating Number of entries (2), setting diff (" . $diff_last_pr . ") and PR (" . $pr_rows[count($entries)-1]->pr . ")<br />";
				$wpdb->update($prtools_url_table, array('diff_last_pr'=>$diff_last_pr, 'pr_entries'=>2, 'pr'=>$pr_rows[count($entries)-1]->pr), array('url'=>$url_row->url));
			}
		}
		
		if($prtools_debug)echo "<hr />";
	}
}

function alter_table_from_02(){
	global $prtools_debug;
	global $wpdb;
	global $prtools_url_table;
	
	$sql="ALTER TABLE `" . $prtools_url_table . "` ADD `queue` INT( 1 ) NOT NULL AFTER `pr_entries`";
	if($prtools_debug)echo "SQL: " . $sql . "<br />";	
	
	$wpdb->query($sql);
}

?>