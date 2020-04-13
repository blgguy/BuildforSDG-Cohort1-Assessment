<?php
header('Content-Type: application/json');
  
function covid19ImpactEstimator($data)
{
	$periodtype 	= $data['periodType'];
	$timetoelapse 	= $data['timeToElapse'];
	$reportedCases 	= $data["reportedCases"];

    //impact...
    $impactCurrentlyInfected = $reportedCases * 10;
    $impactInfectionsByRequestedTime = infectionbyrequestedtime($impactcurrentlyInfected, $periodtype, $timetoelapse);

    //severeimpact...
    $severeImpactcurrentlyInfected = $reportedCases * 50;
    $severeImpactInfectionsByRequestedTime = infectionbyrequestedtime($currentlyinfected, $periodtype, $timetoelapse);

    // this is the variable to store the array to output Impact..
	$impact = array(
		'CurrentlyInfected' => $impactCurrentlyInfected,
		'InfectionsByRequestedTime' => $impactInfectionsByRequestedTime,
	);

	// this is the variable to store the array to output severeImpact..
	$severeimpact = array(
		'CurrentlyInfected' => $severeImpactcurrentlyInfected,
		'InfectionsByRequestedTime' => $severeImpactInfectionsByRequestedTime,
	);


	//this will return all the arrays...
	$data = array(
		'data' => $data,
		'impact' => $impact,
		'severeimpact' => $severeimpact 
	);


  	return $data;
}

function periodT0Days($periodtype, $timeToElapse){
	if ($periodtype == 'months') {
		$days = 30 * $timeToElapse;
	}elseif ($periodtype == 'weeks') {
		$days = 7 * $timeToElapse;
	}elseif ($periodtype == 'days') {
		$days = $timeToElapse;
	} else{
		echo "use a valid periodtype";
	}
echo $days;
}
function infectionbyrequestedtime($currentlyinfected, $periodtype, $timeToElapse){
	$factor = periodT0Days($periodtype, $timeToElapse);
	$factor = floor($factor / 3);
	echo $currentlyinfected * (2**$factor);
}
