<?php 

$wordpress_path="../../../..";

$pr_url=$_GET['url'];

require( $wordpress_path . '/wp-load.php' );

// Standard inclusions     
include("res/pChart/pChart/pData.class"); 
include("res/pChart/pChart/pChart.class");

$table_name = $wpdb->prefix . "prtools_pr";

$sql = "SELECT * FROM ".$table_name." WHERE url='" . $pr_url . "' ORDER by entrydate";

// echo $sql."<br />";

$prtools_rows = $wpdb->get_results( $sql );

$i=count($prtools_rows);

// echo "<pre>";
// print_r($prtools_rows);
// echo "</pre>";

$pr_serie=array();
$date_serie=array();

foreach ( $prtools_rows as $row ) {
	$pr=$row->pr;
	//if($row->pr!=-2){
		if($row->pr==-1 || $row->pr==-2)$pr=0;
		array_push($pr_serie,$pr);
		array_push($date_serie,date("d.m.y",$row->entrydate));	
	//}
}
// echo "<pre>";
// print_r($pr_serie);
// echo "</pre>";

// echo "<pre>";
// print_r($date_serie);
// echo "</pre>";

// Dataset definition   
$DataSet = new pData;  
$DataSet->AddPoint($pr_serie); 
$DataSet->AddAllSeries();  

$DataSet->SetAbsciseLabelSerie("Pageranks");  
$DataSet->AddPoint($date_serie,"Date"); 

// Initialise the graph  
$Test = new pChart(700,230);
$Test->setFontProperties("res/pChart/Fonts/tahoma.ttf",10);

$Test->setGraphArea(50,30,680,200);  // Area
$Test->drawFilledRoundedRectangle(7,7,693,223,5,254,254,254); // Whole background 
$Test->drawRoundedRectangle(5,5,695,225,5,230,230,230); // Border
$Test->drawGraphArea(255,255,255,TRUE); // Background of graph

$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE); // Scales
$Test->drawGrid(3,TRUE,230,230,230,50); // Grid behind graphs

// Draw the line graph  
$Test->setFontProperties("res/pChart/Fonts/tahoma.ttf",10);
$Test->writeValues($DataSet->GetData(),$DataSet->GetDataDescription(),"Sites");
// $Test->setColorPalette(0,255,255,0);
# // Draw the line graph  
$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());  
$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);  

// Finish the graph  
$Test->setFontProperties("res/pChart/Fonts/tahoma.ttf",8);  

// $Test->drawLegend(45,35,$DataSet->GetDataDescription(),255,255,255);  
$Test->setFontProperties("res/pChart/Fonts/tahoma.ttf",10);  
$Test->drawTitle(60,22,"Pagerank statistics of ".$pr_url,50,50,50,585);  
$Test->Stroke();  

?>