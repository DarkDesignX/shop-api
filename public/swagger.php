<?php

	/**
	 * The lines of this PHP file were written by our programming teacher Mr. Manuel Sollberger. 
	 * I did not write the lines of code in class when we were shown how to write it.
	 */

	require "../vendor/autoload.php";
	$openapi = \OpenApi\Generator::scan([__DIR__]);
	header('Content-Type: application/x-yaml');
	echo $openapi->toYaml();
?>