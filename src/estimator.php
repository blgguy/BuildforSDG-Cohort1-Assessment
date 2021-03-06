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