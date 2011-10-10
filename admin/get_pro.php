<?php
if(!$prtools_extended){
	function prtools_get_pro_tab(){
		echo '<li class="ui-state-default ui-corner-top"><a href="#cap_get_pro">' . __('Get professional version', 'prtools') . '</a></li>';
	}
	add_action( 'prtools_admin_tabs', 'prtools_get_pro_tab' , 10, 0); 
	
	function prtools_get_pro__page(){
		echo '<div id="cap_get_pro" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">';
		echo '<h2>' . __('Get professional version of Pagerank tools', 'prtools') . '</h2>';
		echo '<p>'.__('The professional version of the Pagerank tools have many more functions than the free versions. Better and nicer overview, filter functions, be informed on pagerank changes by email ... Just take a look to the further informations to get an impression. You can purchase pagerank professional <a href="#">here</a>. ', 'prtools').'</p>';
		
		echo '<p><a href="#" class="prtools_button">' . __('Purchase professional version &gt;', 'prtools') . '</a></p>';
		
		echo '<h3>' . __('Pageranks tab', 'prtools') . '</h3>';
		echo '<img src="' . $prtools_plugin_path . '/images/screenshot_pageranks.jpg" />';
		echo '<h4>' . __('Additional functions', 'prtools') . '</h4>';
		echo '<ul>';
		echo '<li>'.__('- Graphical statistics of all pageranks', 'prtools').'</li>';
		echo '<li>'.__('- Filter functions (Pagerank and number of show enries)', 'prtools').'</li>';			
		echo '<li>'.__('- Export pageranks to CVS (MS-Excel compatible)', 'prtools').'</li>';
		echo '<li>'.__('- Adding urls to update queue for an immediate update', 'prtools').'</li>';				
		echo '<li>'.__('- Order your urls by click on tablehead caption', 'prtools').'</li>';	
		echo '<li>'.__('- Getting titles of urls', 'prtools').'</li>';		
		echo '<li>'.__('- Graphical pagerank history of urls', 'prtools').'</li>';
		echo '<li>'.__('- Old pagerank data and difference to old pagerank', 'prtools').'</li>';		
		echo '<li>'.__('- Date for last and next pagerank check', 'prtools').'</li>';		
		echo '<li>'.__('- Graphical pagerank', 'prtools').'</li>';		
		echo '<li>'.__('- Navigation thru entries', 'prtools').'</li>';		
		echo '</ul>';
		
		echo '<p><a href="#" class="prtools_button">' . __('Purchase professional version &gt;', 'prtools') . '</a></p>';
		
		echo '<h3>' . __('Setings tab', 'prtools') . '</h3>';
		echo '<img src="' . $prtools_plugin_path . '/images/screenshot_settings.jpg" />';
		echo '<h4>' . __('Additional functions', 'prtools') . '</h4>';
		echo '<ul>';
		echo '<li>'.__('- Email notifications: Stay informed if pagerank changes.', 'prtools').'</li>';
		echo '</ul>';	
		
		echo '<p><a href="#" class="prtools_button">' . __('Purchase professional version &gt;', 'prtools') . '</a></p>';
		
		echo '<h3>' . __('Info tab', 'prtools') . '</h3>';
		echo '<img src="' . $prtools_plugin_path . '/images/screenshot_info.jpg" />';
		echo '<h4>' . __('Additional functions', 'prtools') . '</h4>';
		echo '<ul>';
		echo '<li>'.__('- Information about professional version configuration problems.', 'prtools').'</li>';
		echo '</ul>';			
		
		echo '<p><a href="#" class="prtools_button">' . __('Purchase professional version &gt;', 'prtools') . '</a></p>';
		
		echo '</div>';	
	}
	add_action( 'prtools_admin_pages', 'prtools_get_pro__page' , 10, 0);
}
?>