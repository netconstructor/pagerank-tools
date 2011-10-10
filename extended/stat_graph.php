<?php 

//$file_path=dirname(__FILE__);
$wordpress_path="../../../..";

require( $wordpress_path . '/wp-load.php' );

// Standard inclusions     
include("res/pChart/pChart/pData.class"); 
include("res/pChart/pChart/pChart.class");

$table_name = $wpdb->prefix . "prtools_url";
$prtools_rows = $wpdb->get_results( "SELECT * FROM ".$table_name." ORDER by pr DESC" );

$data[-2]=0;
$data[-1]=0;
$data[0]=0;
$data[1]=0;
$data[2]=0;
$data[3]=0;
$data[4]=0;
$data[5]=0;
$data[6]=0;
$data[7]=0;
$data[8]=0;
$data[9]=0;
$data[10]=0;

$i=count($prtools_rows);

foreach ( $prtools_rows as $row ) {
	// if($row->pr!=-2)$data[$row->pr]++;
	$data[$row->pr]++;
}

// Dataset definition   
$DataSet = new pData;  
$DataSet->AddPoint($data,"Sites"); 
$DataSet->AddAllSeries();  

$DataSet->SetAbsciseLabelSerie("Pageranks");  
$DataSet->AddPoint(array("NEW","N/A","PR0","PR1","PR2","PR3","PR4","PR5","PR6","PR7","PR8","PR9","PR10",),"Pageranks"); 

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
$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);

// Finish the graph  
$Test->setFontProperties("res/pChart/Fonts/tahoma.ttf",8);  

// $Test->drawLegend(45,35,$DataSet->GetDataDescription(),255,255,255);  
$Test->setFontProperties("res/pChart/Fonts/tahoma.ttf",10);  
$Test->drawTitle(60,22,"Pagerank statistics of all URLs",50,50,50,585);  
$Test->Stroke();  

?>