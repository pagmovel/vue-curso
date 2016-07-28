<?php
header("Pragma: no-cache");
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Headers: Authorization");

require '../vendor/autoload.php';

require '../config.php';
require '../DB.php';

$app = new \Slim\App;

//Auth Middleware
$auth = function ($request, $response, $next) {
	//Get token from header
	$token = $request->getHeader('x-access-token');
	if (empty($token)){
		return $response->withStatus(401)->write("Unauthorized access - You should login");
	}
	try{
		$decoded = \Firebase\JWT\JWT::decode($token[0], base64_decode(SECRECT_KEY), array('HS256'));
	} catch(\Exception $e){
		return $response->withStatus(401)->write("Unauthorizedaccess - Wrong Token - " . $e->getMessage() . " - " . $token[0]);
	}

	return $next($request, $response);
};


//CORS - inicio
$corsOptions = array(
	"origin" => "*",
	"exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
	"allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
$app->add($cors);
//CORS - fim

require 'tests.php';
require 'login.php';
//require 'employee.php';
//require 'customer.php';
//require 'sales.php';
require 'category.php';
require 'supplier.php';
//require 'product.php';

$app->run();