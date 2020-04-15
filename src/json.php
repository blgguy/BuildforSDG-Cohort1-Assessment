<?php
require('estimator.php');

function jsonFormat($data){
            return json_encode(covid19ImpactEstimator($data));
            log();
    }

	echo jsonFormat();

?>