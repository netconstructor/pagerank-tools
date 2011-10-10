<?php function prtools_settings(){ ?>
<!-- Head of entry //-->
<div class="tab-head">
	<h2><?php _e('Settings','prtools'); ?> <?php echo $_GET['url']; ?></h2>
</div>

<?php 

$prtools_settings=get_option('pagerank_tools_settings');
	
?>
<div class="tab-content">
	<h3>Google PR fetching options</h3>
     <p><?php _e('Requests to google where made in moment of visitors request of a site. There is no cron option, cause of better random requests. Here you can define further rules for requests to google.<br /><strong>Be careful with these settings, otherwise google maybe ban your IP from further requests.</strong>','prtools'); ?></p>
    <form name="prtoolssettings" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>#cap_settings" method="post">
        <p><?php _e('Minimum distance between pagerank requests to google:','prtools'); ?></p>
        <input type="text" name="fetch_interval" id="fetch_interval" value="<?php echo $prtools_settings['fetch_interval']; ?>" size="3" /> <?php _e('minutes (0  no distance)','prtools'); ?>
        
        <p><?php _e('How much requests should be made at once?','prtools'); ?></p>
        <input type="text" name="fetch_num" id="fetch_num" value="<?php echo $prtools_settings['fetch_num']; ?>" size="3" /> <?php _e('requests','prtools'); ?>
        
        <p><?php _e('Minimum distance between updates for an URL:','prtools'); ?></p>
        <input type="text" name="fetch_url_interval" id="fetch_url_interval" value="<?php echo $prtools_settings['fetch_url_interval']; ?>" size="3" /> <?php _e('days','prtools'); ?>
        
        <p><?php _e('Minimum distance between updates for an URL without pagerank (new URLs):','prtools'); ?></p>
        <input type="text" name="fetch_url_interval_new" id="fetch_url_interval_new" value="<?php echo $prtools_settings['fetch_url_interval_new']; ?>" size="3" /> <?php _e('days','prtools'); ?>
        
        <?php do_action( 'prtools_settings_page'); ?>
        
        <br /><br />
    
        <input class="button-secondary action" type="submit" name="savesettings" value="<?php _e('Save settings','prtools'); ?>" />
    </form>
</div>
<?php } ?>