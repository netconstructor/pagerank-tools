<?php

function prtools_info_tab(){
	echo '<li class="ui-state-default ui-corner-top"><a href="#cap_info">' . __('Info', 'prtools') . '</a></li>';
}
add_action( 'prtools_admin_tabs', 'prtools_info_tab' , 10, 0); 

function prtools_info_page(){
	echo '<div id="cap_info" class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide">';
	echo '<h2>' . __('Information', 'prtools') . '</h2>';
	
	echo '<p>' . __('This site just informs you if your webspace is set up correctly. If you want to change something, please inform your webhoster.', 'prtools') . '</p>';
	
	echo '<h3>' . __('PHP settings', 'prtools') . '</h3>';
	echo '<table class="widefat">';

	echo '<thead>';	
	echo '<tr>';
	echo '<th width="200">' . __('Name', 'prtools') . '</th>';
	echo '<th width="100">' . __('Value', 'prtools') . '</th>';
	echo '<th width="400">' . __('Status', 'prtools') . '</th>';	
	echo '</tr>';	
	echo '</thead>';	

	echo '<tbody>';
	
	$fp = @fsockopen("toolbarqueries.google.com", 80, $errno, $errstr, 30);
	if (!$fp) {
		echo '<tr>';	
		echo '<td>fsockopen</td>';
		echo '<td>false</td>';
		echo '<td><b>Error:</b> Google toolbar query server can´t be reached. It´s not possible to request pagerank with these webspace settings. Maybe fsockopen is disabled in php.ini.</td>';
		echo '</tr>';
	}else{
		echo '<tr>';	
		echo '<td>fsockopen</td>';
		echo '<td>true</td>';
		echo '<td>OK!</td>';
		echo '</tr>';
	}
	
	// PHP INI - allow_url_fopen 
	echo '<tr>';	
	echo '<td>allow_url_fopen</td>';
	echo '<td>' . ini_get('allow_url_fopen') . '</td>';
	if(ini_get('allow_url_fopen')==1){
		echo '<td>OK!</td>';
	}else{
		echo '<td><b>Error:</b> Value have to be 1! Otherwise title fetching will not work. Please ask your webhoster to set it to 1 for your webspace. Titles will not be fetched or shown.</td>';
	}
	echo '</tr>';
	
	// PHP INI - output_buffering
	echo '<tr>';	
	echo '<td>output_buffering</td>';
	
	if(ini_get('output_buffering')==1){
		echo '<td>1</td>';
		echo '<td>OK!</td>';
	}else{
		echo '<td>' . ini_get('output_buffering') . '</td>';
		echo '<td><b>Warning:</b> Value should to be 1, because performance of your site will be better if page requests where made.</td>';
	}
	echo '</tr>';	
	
	// Checking if libxml exists
	echo '<tr>';	
	echo '<td>libxml support</td>';
	
	if(class_exists('DOMDocument')){
		echo '<td>Installed</td>';	
		echo '<td>OK!</td>';
	}else{
		echo '<td>Not installed</td>';	
		echo '<td><b>Warning:</b> LIBXML is not installed. DOMDocument Class is not existing. "preg_match" function will be used instead to get title. This is less effective than DOMDocument functions. Maybe you will get problems with getting titles of urls.</td>';
	}	
	echo '</tr>';	
	
	

	echo '</tbody>';	
	echo '</table>';	
	
    echo '</div>';	
}
add_action( 'prtools_admin_pages', 'prtools_info_page' , 10, 0);

?>