<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

header("Pragma: no-cache");

$app->get('/', function (Request $request, Response $response) {
	$response->getBody()->write("Hello World");
	return $response;
})->add($auth);

$app->get('/databases', function (Request $request, Response $response) {
	$dbs = DB::query( 'SHOW DATABASES' );
	$response->getBody()->write("Hello World<br>");
	while( ( $db = $dbs->fetchColumn( 0 ) ) !== false )
	{
		$response->getBody()->write($db . ", ");
	}
	header("Pragma: no-cache");
	//$response->getBody()->write();
	print_r($dbs);

	return $response;
});