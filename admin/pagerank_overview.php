<?php

function prtools_overview(){
	global $prtools_extended;

				
	update_url_table(false,false);
	update_pr_tools();
	
	if ( isset( $_GET['url'] ) && $prtools_extended ) { prtools_url(); } else { 	
		
		global $wpdb;
		global $prtools_url_table;
		global $prtools_pr_table;
		global $prtools_sum_urls;
		global $prtools_sum_urls_query;
		global $prtools_absolute_path;		
		
		$table_name = $wpdb->prefix . "prtools_url";
		
		$sql = "SELECT count(*) AS count FROM " . $table_name;
		$prtools_stat = $wpdb->get_row( $sql );
		$prtools_sum_urls = $prtools_stat->count;
		
		$sql = "SELECT * FROM " . $table_name . " ORDER by pr DESC";
		$sql = apply_filters( 'prtools_main_sql', $sql );
		$prtools_rows = $wpdb->get_results( $sql );
		
		$prtools_sum_urls_query=count($prtools_rows);
		

				
?>

<!-- Head of entry //-->
<div class="tab-head">
  <h2>
    <?php _e('Pageranks','prtools'); ?>
    <?php if ( isset( $_GET['url'] ) && $prtools_extended ) echo ' ' . $_GET['url']; ?>
  </h2>
</div>

<?php do_action( 'prtools_main_head'); ?>

<!-- Listing pageranks of all sites //-->
<table class="widefat">
<?php 
	
	$tablehead = '<thead><tr>';
	$tablehead.= '<th scope="col">' . __('PR','prtools') . '</th>';
	$tablehead.= '<th scope="col">' . __('URL','prtools') . '</th>';
	$tablehead.= '<th scope="col">&nbsp;</th><th scope="col">' . __('Type','prtools') . '</th>';
	$tablehead.= '<th scope="col">' . __('Update','prtools') . '</th>';
	$tablehead.= '</tr></thead>';
	
	$tablehead=apply_filters( 'prtools_main_tablehead', $tablehead );

	echo $tablehead;
    
    ?>
  	<tbody>
    <?php 
		
		foreach ( $prtools_rows as $row ) {
		
			if($row->lastupdate!=0){
				$row->date=date("d.m.Y",$row->lastupdate);
			}else{
				$row->date="n/a";
			}
			
			if($row->pr==-1){
				$pr="n/a";
			}elseif($row->pr==-2){
				$pr="-";
			}else{
				$pr=$row->pr;
			}
			
			$prtools_row = '<tr>';
			$prtools_row.= '<td scope="row">' . $pr . '</td>';
			$prtools_row.= '<td scope="row"><a href="' . $row->url . '" target="_blank">' . $row->url . '</a></td>';
			$prtools_row.= '<td scope="row">
								<a href="' . $row->url . '" target="_blank">[visit]</a>
								<a href="#" border="0" title="Delete" onclick="question_redirect(\'' . __('Do you really want to delete','prtools') . ' ' . $row->url . ' and all data from Pagerank tools?\', \'' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '&delete_url=' . $row->url . '\' );">[delete]</a>
							</td>';
			
			$prtools_row.= '<td scope="row">' . $row->url_type . '</td>';
			$prtools_row.= '<td scope="row">' . $row->date . '</td>';
			$prtools_row.= '</tr>';
			
			$prtools_row=apply_filters( 'prtools_main_tablerow' , $prtools_row , $row );
			
			echo $prtools_row;
    	} 
		?>
  </tbody>
</table>
<div style="height:20px"></div>
<?php
	}
	do_action( 'prtools_main_bottom' );
}
?>