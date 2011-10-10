<?php 
// Templates
if(!function_exists('alert')){
	function alert($msg){
		echo "<div class=\"updated\"><p>".$msg."</p></div>";
	}
}
if(!function_exists('pr_ajaxui_js')){
	function pr_ajaxui_js(){
		
		if( ! isset( $_GET['page'] ) ) 
			return;

		if( $_GET['page'] == 'page_pageranks' ) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-tabs');
		}
	}
}
if(!function_exists('pr_ajaxui_css')){
	function pr_ajaxui_css()
	{
		if( isset( $_GET['page'] ) ){
			 if( $_GET['page'] == 'page_pageranks' ) {
				 echo '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/base/jquery-ui.css" rel="stylesheet" />';
			 }
		}
	}
}
function prtools_css(){
	echo "<link rel=\"stylesheet\" href=\"" . $prtools_plugin_path . "/ui/styles.css\" type=\"text/css\" media=\"screen\" />";
}
?>