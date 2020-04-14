<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
   $data = array(
         		"region" => array(
					        "name"	        					=>	"Africa",
					        "avgAge" 	    					=>	19.7,
					        "avgDailyIncomeInUSD"				=>	4,
					        "avgDailyIncomeInPopulation" 		=>	0.73,
					    ),
	         	"periodType" 			=>	"days",
		        "timeToElapse"        	=>  38,
		        "reportedCases"         =>  2747,
		        "population"          	=>  92931687,
		        "totalHospitalBeds"     =>  678874,
	    		);

function covid19ImpactEstimator($data)
{
	$periodtype 	= $data['periodType'];
	$timetoelapse 	= (int)$data['timeToElapse'];
	$reportedCases 	= (int)$data['reportedCases'];
	$totalHospitalBeds = (int)$data['totalHospitalBeds'];

    //impact...
    $currentlyInfected = floor($reportedCases * 10);
    $impactInfectionsByRequestedTime = infectionbyrequestedtime($currentlyInfected, $periodtype, $timetoelapse);
    $impactsevereCaseByRequestedTime = floor(15*$impactInfectionsByRequestedTime/100);
    $impacthospitalBedsByRequestedTime = (int)(35*$totalHospitalBeds /100) - ($impactsevereCaseByRequestedTime);
    $impacthospitalBedsByRequestedTime = floor($impacthospitalBedsByRequestedTime);

    //severeimpact...
    $severeImpactcurrentlyInfected = floor($reportedCases * 50);
    $severeImpactInfectionsByRequestedTime = infectionbyrequestedtime($severeImpactcurrentlyInfected, $periodtype, $timetoelapse);
    $servereimpactsevereCaseByRequestedTime = floor(15*$severeImpactInfectionsByRequestedTime/100);
	$servereimpacthospitalBedsByRequestedTime = (int)(35*$totalHospitalBeds /100) - ($servereimpactsevereCaseByRequestedTime);
	$servereimpacthospitalBedsByRequestedTime = floor($servereimpacthospitalBedsByRequestedTime);


    // this is the variable to store the array to output Impact..
	$impact = array(
		'currentlyInfected' => $currentlyInfected,
		'infectionsByRequestedTime' => $impactInfectionsByRequestedTime,
		'severeCasesByRequestedTime' => $impactsevereCaseByRequestedTime,
		'hospitalBedsByRequestedTime' => $impacthospitalBedsByRequestedTime,
	);

	// this is the variable to store the array to output severeImpact..
	$severeImpact = array(
		'currentlyInfected' => $severeImpactcurrentlyInfected,
		'infectionsByRequestedTime' => $severeImpactInfectionsByRequestedTime,
		'severeCasesByRequestedTime' => $servereimpactsevereCaseByRequestedTime,
		'hospitalBedsByRequestedTime' => $servereimpacthospitalBedsByRequestedTime,
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