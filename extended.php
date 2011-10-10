<?php

$prtools_extended=true;

global $prtools_name;
$prtools_name=__('Pagerank tools','prtools');

$prtools_titles=false;

// include( $prtools_absolute_path_absolute . '/extended/pageranks_listing.inc.php' );
include($prtools_absolute_path_absolute."/extended/main.php");
include($prtools_absolute_path_absolute.'/extended/pagerank_url.php');
include($prtools_absolute_path_absolute."/extended/settings.php");
include($prtools_absolute_path_absolute."/extended/functions.inc.php");
include($prtools_absolute_path_absolute."/extended/info.php");

if(ini_get('allow_url_fopen')==1 && $_SERVER['REMOTE_ADDR']!=gethostbyname($_SERVER['HTTP_HOST']) && gethostbyname($_SERVER['HTTP_HOST'])!=""){
	add_action('wp_footer','fetch_titles');
}

// display the data

?>