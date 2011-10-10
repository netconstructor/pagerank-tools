<?php 

function page_pageranks(){ 
	global $prtools_extended, $prtools_name , $prtools_absolute_path;

?>
    <!-- Title //-->
  	<h2><b><?php echo $prtools_name; ?></b></h2>

	<!-- Tabs //-->    
    <div id="prtooltabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
            <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#cap_pageranks"><?php _e('Pageranks','prtools'); ?></a></li>
            <li class="ui-state-default ui-corner-top"><a href="#cap_settings"><?php _e ('Settings', 'prtools') ?></a></li>
            <?php do_action('prtools_admin_tabs'); ?>
        </ul>
    
        <!-- Pagerank page //-->
        <div id="cap_pageranks" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
            <?php prtools_overview(); ?>
        </div> 
        
        <!-- Settings page //-->
        <div id="cap_settings" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">
            <?php prtools_settings(); ?>
        </div>
        <?php do_action('prtools_admin_pages'); ?>
    </div>
    <script type="text/javascript" src="/<?php echo $prtools_absolute_path; ?>/scripts.js"></script>
    <script type="text/javascript">
    	jQuery(document).ready(function($){
        	$("#prtooltabs").tabs();
         });
		 <?php if(!$prtools_extended){ ?>
	     jQuery('.get_pro').click(function($) {
     		jQuery('#prtooltabs').tabs('select', 2); // switch to second tab
     		return false;
  		});
		<?php } ?>
	</script>

<?php } ?>