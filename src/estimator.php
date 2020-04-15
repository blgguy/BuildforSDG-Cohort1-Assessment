<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

function covid19ImpactEstimator($data)
{
	$periodtype 			= $data['periodType'];
	$timetoelapse 			= $data['timeToElapse'];
	$reportedCases 			= $data['reportedCases'];
	$totalHospitalBeds 		= $data['totalHospitalBeds'];
	$avgDailyIncomeInUSD 	= $data['region']['avgDailyIncomeInUSD'];
	$avgDailyIncomeInPopulation = $data['region']['avgDailyIncomePopulation'];

    //impact...
    $currentlyInfected 				= floor($reportedCases * 10);
    $impactInfectionsByRequestedTime = infectionbyrequestedtime($currentlyInfected, $periodtype, $timetoelapse);
    $impactsevereCaseByRequestedTime = (15*$impactInfectionsByRequestedTime) / 100;
    $beds = (35*$totalHospitalBeds) / 100;
    $impacthospitalBedsByRequestedTime = bcdiv($beds - $impactsevereCaseByRequestedTime, 1, 0);
    //$impacthospitalBedsByRequestedTime = round($impacthospitalBedsByRequestedTime);

    $impactcasesForICUByRequestedTime	= floor((5 * $impactInfectionsByRequestedTime) / 100);
    $impactcasesForVentilatorsByRequestedTime = floor((2 * $impactInfectionsByRequestedTime) / 100);
    $impactdollarsInFlight = dollarsInFlight($impactInfectionsByRequestedTime, $periodtype, $timetoelapse, $avgDailyIncomeInUSD, $avgDailyIncomeInPopulation);

    //severeimpact...
    $severeImpactcurrentlyInfected 			= floor($reportedCases * 50);
    $severeImpactInfectionsByRequestedTime 	= infectionbyrequestedtime($severeImpactcurrentlyInfected, $periodtype, $timetoelapse);
    $servereimpactsevereCaseByRequestedTime = (15*$severeImpactInfectionsByRequestedTime) / 100;
    $severebeds = (35*$totalHospitalBeds) / 100;
	$servereimpacthospitalBedsByRequestedTime = bcdiv($severebeds - $servereimpactsevereCaseByRequestedTime, 1, 0);
	//$servereimpacthospitalBedsByRequestedTime = round($servereimpacthospitalBedsByRequestedTime);

	$severeimpactcasesForICUByRequestedTime = floor((5 * $severeImpactInfectionsByRequestedTime) / 100);
	$severeimpactcasesForVentilatorsByRequestedTime = floor((2 * $severeImpactInfectionsByRequestedTime) / 100);
    $severeimpactdollarsInFlight = dollarsInFlight($severeImpactInfectionsByRequestedTime, $periodtype, $timetoelapse, $avgDailyIncomeInUSD, $avgDailyIncomeInPopulation);

    // this is the variable to store the array to output Impact..
	$impact = array(
		'currentlyInfected' 			=> (int)$currentlyInfected,
		'infectionsByRequestedTime' 	=> (int)$impactInfectionsByRequestedTime,
		'severeCasesByRequestedTime' 	=> (int)$impactsevereCaseByRequestedTime,
		'hospitalBedsByRequestedTime' 	=> (int)$impacthospitalBedsByRequestedTime,
		'casesForICUByRequestedTime'	=> (int)$impactcasesForICUByRequestedTime,
		'casesForVentilatorsByRequestedTime' => (int)$impactcasesForVentilatorsByRequestedTime,
		'dollarsInFlight' 				=> (int)$impactdollarsInFlight,
	);

	// this is the variable to store the array to output severeImpact..
	$severeImpact = array(
		'currentlyInfected' 			=> (int)$severeImpactcurrentlyInfected,
		'infectionsByRequestedTime' 	=> (int)$severeImpactInfectionsByRequestedTime,
		'severeCasesByRequestedTime' 	=> (int)$servereimpactsevereCaseByRequestedTime,
		'hospitalBedsByRequestedTime' 	=> (int)$servereimpacthospitalBedsByRequestedTime,
		'casesForICUByRequestedTime'	=> (int)$severeimpactcasesForICUByRequestedTime,
		'casesForVentilatorsByRequestedTime' => (int)$severeimpactcasesForVentilatorsByRequestedTime,
		'dollarsInFlight' 				=> (int)$severeimpactdollarsInFlight,
	);

	//this will return all the Datas...
	
	$data = array(
		'data' => $data,
		'impact' => $impact,
		'severeImpact' => $severeImpact 
	);

  	return $data;
}

function periodT0Days($periodtype, $timeToElapse){
	$day = 0;
	if ($periodtype == 'months') {
		$days = 30 * $timeToElapse;
	}elseif ($periodtype == 'weeks') {
		$days = 7 * $timeToElapse;
	}elseif ($periodtype == 'days') {
		$days = $timeToElapse;
	} else{
		return "use a valid periodtype";
	}
return $days;
}

function infectionbyrequestedtime($currentlyinfected, $periodtype, $timeToElapse){
	$factor = periodT0Days($periodtype, $timeToElapse);
	$factor = floor($factor / 3);
	return $currentlyinfected * (2**$factor);
}

function dollarsInFlight($infectionsByRequestedTime,$periodtype, $timetoelapse,
                         $avgDailyIncomeInUSD, $avgDailyIncomePopulation)
{
    $Totaldays = floor(periodT0Days($periodtype, $timetoelapse));
    $Avaragedollars =  ($infectionsByRequestedTime * $avgDailyIncomeInUSD * $avgDailyIncomePopulation) / $Totaldays;
    return floor($Avaragedollars);
}

 function log(){
	$time2 = microtime(true);
	$file = 'log.txt';
	$logmsg = file_get_contents($file);
	$exe_time = "0".(int)($time2 - $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
 	$logmsg = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". $exe_time."ms";

	$outputMessage =file_put_contents($file, $logmsg."\n", FILE_APPEND | LOCK_EX);

	return $outputMessage;
	}

function XML($xml){
	  
	   	$filePath = 'estimator.xml';
	   	$dom     = new DOMDocument('1.0', 'utf-8'); 
	   	$data      = $dom->createElement('estimator'); 
	   	for($i=0; $i<count($xml); $i++){
	     
	    $estimatorId1        	=  $xml[$i]['id'];  
	    $region_name1      		=   htmlspecialchars($xml[$i]['region_name']);
	    $avgAge1    			=  $xml[$i]['avgAge']; 
	    $avgDailyIncomeInUSD1   =  $xml[$i]['avgDailyIncomeInUSD']; 
	    $avgDailyIncomePopulation1      =  $xml[$i]['avgDailyIncomePopulation']; 
	    $periodType1  			=	$xml[$i]['periodType'];  
	    $timeToElapse1 			=	$xml[$i]['timeToElapse'];
		$reportedCases1			=	$xml[$i]['reportedCases'];
		$population1			=	$xml[$i]['population'];
		$totalHospitalBeds1		=	$xml[$i]['totalHospitalBeds'];
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

?>;
	   }
	 } 

}