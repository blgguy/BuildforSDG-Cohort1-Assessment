<?php
require('estimator.php');

echo xmlData();

function xmlData($xml){
    
      $filePath = 'estimator.xml';
      $dom     = new DOMDocument('1.0', 'utf-8'); 
      $data      = $dom->createElement('estimator'); 
      for($i=0; $i<count($xml); $i++){
       
      $estimatorId1         =  $xml[$i]['id'];  
      $region_name1         =   htmlspecialchars($xml[$i]['region_name']);
      $avgAge1          =  $xml[$i]['avgAge']; 
      $avgDailyIncomeInUSD1   =  $xml[$i]['avgDailyIncomeInUSD']; 
      $avgDailyIncomePopulation1      =  $xml[$i]['avgDailyIncomePopulation']; 
      $periodType1        = $xml[$i]['periodType'];  
      $timeToElapse1      = $xml[$i]['timeToElapse'];
    $reportedCases1     = $xml[$i]['reportedCases'];
    $population1      = $xml[$i]['population'];
    $totalHospitalBeds1   = $xml[$i]['totalHospitalBeds'];
       $estimator = $dom->createElement('estimator');
       $estimator->setAttribute('id', $estimatorId1);
       $region_name     = $dom->createElement('region', $region_name1); 
       $estimator->appendChild($region_name); 
       $avgAge   = $dom->createElement('avgAge', $avgAge1); 
       $estimator->appendChild($avgAge); 
       $avgDailyIncomeInUSD    = $dom->createElement('avgDailyIncomeInUSD', $avgDailyIncomeInUSD1); 
       $estimator->appendChild($avgDailyIncomeInUSD); 
       $avgDailyIncomePopulation     = $dom->createElement('avgDailyIncomePopulation', $avgDailyIncomePopulation1); 
       $estimator->appendChild($avgDailyIncomePopulation); 
       
       $periodType = $dom->createElement('periodType', $periodType1); 
       $estimator->appendChild($periodType);
       $timeToElapse = $dom->createElement('timeToElapse', $timeToElapse1); 
       $estimator->appendChild($timeToElapse);
       $reportedCases = $dom->createElement('reportedCases', $reportedCases1); 
       $estimator->appendChild($reportedCases);
       $population = $dom->createElement('population', $population1); 
       $estimator->appendChild($population);
       $totalHospitalBeds = $dom->createElement('totalHospitalBeds', $totalHospitalBeds1); 
       $estimator->appendChild($totalHospitalBeds);
   
       $data->appendChild($estimator);
     }
     $dom->appendChild($data); 
     $p = $dom->save($filePath); 
     if ($p) {
    $file = fopen('estimator.xml', 'r');
    $log = fread($file, filesize('estimator.xml'));
    fclose($file);
    echo $log;
     }
   } 
?>