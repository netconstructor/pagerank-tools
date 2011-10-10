<?php
function prtools_url(){
	
	global $wpdb;
	global $prtools_url_table;
	global $prtools_pr_table;
	global $prtools_plugin_path;	
	
	$prtools_rows = $wpdb->get_results( "SELECT * FROM ".$prtools_pr_table." WHERE url='". $_GET['url']."' ORDER BY ".$prtools_pr_table.".entrydate DESC");

?>

<!-- Head of entry //-->
<div class="tab-head">
	<h2><?php _e('Pagerank History','prtools'); ?></h2>
    
</div>

<div class="spacer"></div>

<div class="tab-menue">
	<input class="button-secondary action" type="button" value="Back to overview" onClick="history.back(-1);" />
</div>

<div class="statistics">
	<div class="diagram" style="width:700px;margin:0.83em 0;">
    <img src="<?php echo $prtools_plugin_path; ?>/extended/stat_graph_url.php?url=<?php echo $_GET['url']; ?>" alt="Pagerank overview" width="700" height="230" />
	</div>
    <p><a href="<?php echo $_GET['url']; ?>" target="_blank"><?php echo $_GET['url']; ?></a></p>
</div>

<!-- Listing pagerank  history of site //-->
<table class="widefat">
	<thead>
		<tr>
        	<th scope='col'><?php _e('PR','prtools'); ?></th>
            <th scope='col'><?php _e('URL','prtools'); ?></a></th>
            <th scope='col'><?php _e('Update','prtools'); ?></th></tr>
	</thead>
     
    <tbody>
    	<?php $i=0; ?>
		<?php foreach ( $prtools_rows as $row ) { ?>
        <?php $i++; ?>
        <tr>
            <td scope='row'><?php echo prtools_html_pr($row->pr); ?></td>
            <td scope='row'><a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&url=<?php echo $row->url; ?>"><?php echo $row->url; ?></a></td>
            <td scope='row'><?php echo date("d.m.Y",$row->entrydate); ?></td>
        </tr>
        <?php  } ?>
        <?php  if($i==0){ ?>
        <tr>
            <td scope='row' colspan='5'>There could be no pagerank data fetched yet.</td>
        </tr>
        <?php  } ?>
    </tbody>

</table>

<?php 
}
?>