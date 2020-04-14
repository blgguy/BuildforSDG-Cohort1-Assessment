<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

function covid19ImpactEstimator($data)
{
	$periodtype 			= $data['periodType'];
	$timetoelapse 			= (int)$data['timeToElapse'];
	$reportedCases 			= (int)$data['reportedCases'];
	$totalHospitalBeds 		= (int)$data['totalHospitalBeds'];
	$avgDailyIncomeInUSD 	= (int)['avgDailyIncomeInUSD']
	$avgDailyIncomeInPopulation = (int)['avgDailyIncomeInPopulation'];

    //impact...
    $currentlyInfected 				= floor($reportedCases * 10);
    $impactInfectionsByRequestedTime = infectionbyrequestedtime($currentlyInfected, $periodtype, $timetoelapse);
    $impactsevereCaseByRequestedTime = (15*$impactInfectionsByRequestedTime) / 100;
    $beds = (35*$totalHospitalBeds) / 100;
    $impacthospitalBedsByRequestedTime = bcdiv($beds - $impactsevereCaseByRequestedTime, 1, 0);
    //$impacthospitalBedsByRequestedTime = round($impacthospitalBedsByRequestedTime);
    $impactcasesForVentilatorsByRequestedTime = floor((5 * $infectionsByRequestedTime) / 100);
    $impactdollarsInFlight = dollarsInFlight($impactInfectionsByRequestedTime, $periodtype, $timetoelapse, $avgDailyIncomeInUSD, $avgDailyIncomeInPopulation);;

    //severeimpact...
    $severeImpactcurrentlyInfected 			= floor($reportedCases * 50);
    $severeImpactInfectionsByRequestedTime 	= infectionbyrequestedtime($severeImpactcurrentlyInfected, $periodtype, $timetoelapse);
    $servereimpactsevereCaseByRequestedTime = (15*$severeImpactInfectionsByRequestedTime) / 100;
    $severebeds = (35*$totalHospitalBeds) / 100;
	$servereimpacthospitalBedsByRequestedTime = bcdiv($severebeds - $servereimpactsevereCaseByRequestedTime, 1, 0);
	//$servereimpacthospitalBedsByRequestedTime = round($servereimpacthospitalBedsByRequestedTime);
	$severeimpactcasesForVentilatorsByRequestedTime = floor((2 * $infectionsByRequestedTime) / 100);
    $severeimpactdollarsInFlight = dollarsInFlight($severeImpactInfectionsByRequestedTime, $periodtype, $timetoelapse, $avgDailyIncomeInUSD, $avgDailyIncomeInPopulation);

    // this is the variable to store the array to output Impact..
	$impact = array(
		'currentlyInfected' 			=> $currentlyInfected,
		'infectionsByRequestedTime' 	=> $impactInfectionsByRequestedTime,
		'severeCasesByRequestedTime' 	=> $impactsevereCaseByRequestedTime,
		'hospitalBedsByRequestedTime' 	=> $impacthospitalBedsByRequestedTime,
		'casesForVentilatorsByRequestedTime' => $impactcasesForVentilatorsByRequestedTime,
		'dollarsInFlight' 				=> $impactdollarsInFlight
	);

	// this is the variable to store the array to output severeImpact..
	$severeImpact = array(
		'currentlyInfected' 			=> $severeImpactcurrentlyInfected,
		'infectionsByRequestedTime' 	=> $severeImpactInfectionsByRequestedTime,
		'severeCasesByRequestedTime' 	=> $servereimpactsevereCaseByRequestedTime,
		'hospitalBedsByRequestedTime' 	=> $servereimpacthospitalBedsByRequestedTime,
		'casesForVentilatorsByRequestedTime' => $severeimpactcasesForVentilatorsByRequestedTime,
		'dollarsInFlight' 				=> $severeimpactdollarsInFlight
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

function dollarsInFlight($infectionsByRequestedTime,$periodType, $timeToElapse,
                         $avgDailyIncomeInUSD, $avgDailyIncomePopulation)
{
    $Totaldays = floor(periodT0Days($periodType, $timeToElapse));
    $Avaragedollars =  ($infectionsByRequestedTime * $avgDailyIncomeInUSD * $avgDailyIncomePopulation) / $Totaldays;
    return floor($Avaragedollars);
}