<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//****************************************************************
// LISTAR
//****************************************************************
$app->get('/suppliers', function (Request $request, Response $response) {

	$sql = "";
	$parameters = $request->getQueryParams();
	$start = (int)$parameters['start'];
	$limit = (int)$parameters['limit'];

	$keyword=null;
	if(array_key_exists("q", $parameters)){
		$keyword = $parameters['q'];
	}

	if(!empty($start)&&!empty($limit)){
		$start--;

		$stmt = null;
		if(empty($keyword)){
			$sql = "SELECT id,name,address FROM suppliers LIMIT :start, :limit";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':start', $start,PDO::PARAM_INT);
			$stmt->bindParam(':limit', $limit,PDO::PARAM_INT);

		} else {
			$keywordLike = "%".$keyword."%";
			$sql = "SELECT id,name,address FROM suppliers WHERE name LIKE :keyword LIMIT :start, $limit";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':start', $start,PDO::PARAM_INT);
			$stmt->bindParam(':limit', $limit,PDO::PARAM_INT);
			$stmt->bindParam(':keyword', $keywordLike);
		}
		$stmt->execute();

		$sqlCount = null;
		$total = 0;
		if (empty($keyword)){
			$sqlCount = "SELECT count(id) FROM suppliers";
			$stmtCount = DB::prepare($sqlCount);
			$stmtCount->execute();
			$total = $stmtCount->fetchColumn();
		} else {
			$keywordLike = "%".$keyword."%";
			$sqlCount = "SELECT count(id) FROM suppliers WHERE name LIKE :keyword";
			$stmtCount = DB::prepare($sqlCount);
			$stmtCount->bindParam(':keyword', $keywordLike);
			$stmtCount->execute();
			$total = $stmtCount->fetchColumn();
		}

		return $response->withJson($stmt->fetchAll())->withHeader('Access-Control-Expose-Headers','x-total-count')-withHeader('x-total-count', $total);
	} else {
		$sql = "SELECT id,name,address FROM suppliers";
		$stmt = DB::prepare($sql);
		$stmt->execute();

		return $response->withJson($stmt->fetchAll());
	}
})->add($auth);


//****************************************************************
// INLUIR / ALTERAR
//****************************************************************
$app->post('/supplier', function (Request $request, Response $response) {
	try{
		$supplier = (object)$request->getParsedBody();
		if (!empty($supplier->id)){
			//update
			$sql = "UPDATE suppliers SET name=:name, address=:address WHERE id=:id";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':name', $supplier->name);
			$stmt->bindParam(':id', $supplier->id,PDO::PARAM_INT);
			$stmt->bindParam(':address', $supplier->address,PDO::PARAM_LOB);
			$stmt->execute();
			return $response->withJson($supplier);
		} else {
			// insert
			$sql = "INSERT INTO suppliers (name,address) VALUES (:name,:address)";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':name', $supplier->name);
			$stmt->bindParam(':address', $supplier->address,PDO::PARAM_LOB);
			$stmt->execute();
			$supplier->id = DB::lastInsertId();
			return $response->withJson($supplier);
		}

	}
	catch(\Exception $e){
		return $response->withStatus(500)->write($e->getMessage());
	}
})->add($auth);


//****************************************************************
// EXCLUIR / APAGAR
//****************************************************************
$app->post('/supplier/{id}', function (Request $request, Response $response) {
	try{
		$id = $request->getAttribute('id');
		if (!empty($id)){

			$sql = "DELETE FROM suppliers WHERE id=:id";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			return $response;
		}
	}
	catch(\Exception $e){
		return $response->withStatus(500)->write($e->getMessage());
	}
})->add($auth);