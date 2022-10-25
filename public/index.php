<?php
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	header("Content-Type: application/json");

	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Slim\Factory\AppFactory;
	use ReallySimpleJWT\Token;

	require __DIR__ . "/../vendor/autoload.php";
	require "model/shop.php";
	require_once "config/config_category.php";
	require_once "config/config_product.php";
	require_once "config/config.php";

	$app = AppFactory::create();

	/** 
	 * @OA\Info(title="Event API", version="1") 
	 */
	
	function error($message, $code) {
		$error = array("message" => $message);
		echo json_encode($error);

		http_response_code($code);

		die();
	}

	require "controller/commands.php";

	$app->run();
?>