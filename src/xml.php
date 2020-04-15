<?php
require('estimator.php');


function createXMLfile($data){
  
   $filePath = 'estimator.xml';
   $dom     = new DOMDocument('1.0', 'utf-8'); 
   $root      = $dom->createElement('markers'); 
   for($i=0; $i<count($data); $i++){
     
     $title = 'estimate';
     $id = '';
     $name1                =  $data['region']['name'];
     $avgAge1             =   $data['region']['avgAge'];
     $periodType1         =   $data['periodType'];
     $timeToElapse1       =   $data['timeToElapse'];
     $reportedCases1      =   $data['reportedCases'];
     $population1         =   $data['population'];
     $totalHospitalBeds1  =   $data['totalHospitalBeds'];
     $avgDailyIncomeInUSD1        =  $data['region']['avgDailyIncomeInUSD'];
     $avgDailyIncomePopulation1   =  $data['region']['avgDailyIncomePopulation'];

     $estimatorId        =  $id++;  
     // $markerName      =   htmlspecialchars($markersArray[$i]['name']);
     // $markerAuthor    =  $markersArray[$i]['address']; 
     // $markerPrice     =  $markersArray[$i]['lat']; 
     // $markerISBN      =  $markersArray[$i]['lng']; 
     // $markerCategory  =  $markersArray[$i]['type'];  
     $estimator = $dom->createElement('region');
     $estimator->setAttribute('id', $estimatorId);
     $name     = $dom->createElement('name', $name1); 
     $estimator->appendChild($name); 
     $avgAge   = $dom->createElement('avgAge', $avgAge1); 
     $estimator->appendChild($avgAge); 
     $avgDailyIncomeInUSD    = $dom->createElement('avgDailyIncomeInUSD', $avgDailyIncomeInUSD1); 
     $estimator->appendChild($avgDailyIncomeInUSD); 
     $avgDailyIncomePopulation     = $dom->createElement('avgDailyIncomePopulation', $avgDailyIncomePopulation1); 
     $estimator->appendChild($avgDailyIncomePopulation);

     $periodType      = $dom->createElement('periodType', $periodType1);
     $estimator->appendChild($periodType);      
     $timeToElapse      = $dom->createElement('timeToElapse', $timeToElapse1);
     $estimator->appendChild($timeToElapse);     
     $reportedCases     = $dom->createElement('reportedCases', $reportedCases1);
     $estimator->appendChild($reportedCases);       
     $population      = $dom->createElement('population', $population1);
     $estimator->appendChild($population);    
     $totalHospitalBeds     = $dom->createElement('totalHospitalBeds', $totalHospitalBeds1);
     $estimator->appendChild($totalHospitalBeds); 
 
     $root->appendChild($estimator);
   }
   $dom->appendChild($root); 
   $dom->save($filePath); 
   
 } 


 function xmltoarray(){
  $file = 'estimator.xml';

  $xmlfile = file_get_contents($file);

  $json = json_encode($xmlfile);

  $data = json_decode($json, true);

  print_r($data);
 }
 xmltoarray(covid19ImpactEstimator($data));
  $time2 = microtime(true);
  $file = 'log.txt';
  $logmsg = file_get_contents($file);
  $exe_time = "0".(int)($time2 - $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
  $logmsg = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". $exe_time."ms";

  file_put_contents($file, $logmsg."\n", FILE_APPEND | LOCK_EX);


?>