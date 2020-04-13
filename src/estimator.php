<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
  
function covid19ImpactEstimator($data)
{
	$periodtype 	= $data['periodType'];
	$timetoelapse 	= $data['timeToElapse'];
	$reportedCases 	= $data["reportedCases"];

    //impact...
    $currentlyInfected = floor($reportedCases * 10);
    $impactInfectionsByRequestedTime = infectionbyrequestedtime($currentlyInfected, $periodtype, $timetoelapse);

    //severeimpact...
    $severeImpactcurrentlyInfected = floor($reportedCases * 50);
    $severeImpactInfectionsByRequestedTime = infectionbyrequestedtime($severeImpactcurrentlyInfected, $periodtype, $timetoelapse);

    // this is the variable to store the array to output Impact..
	$impact = array(
		'currentlyInfected' => $currentlyInfected,
		'infectionsByRequestedTime' => $impactInfectionsByRequestedTime,
	);

	// this is the variable to store the array to output severeImpact..
	$severeImpact = array(
		'currentlyInfected' => $severeImpactcurrentlyInfected,
		'infectionsByRequestedTime' => $severeImpactInfectionsByRequestedTime,
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
