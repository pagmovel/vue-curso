<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


//****************************************************************
// LISTAR / RECUPERAR
//****************************************************************
$app->get('/categories', function (Request $request, Response $response) {
	$sql = '';
	$parameters = $request->getQueryParams();
	$start =(int)$parameters['start'];
	$limit =(int)$parameters['limit'];

	$keyword=null;
	if(array_key_exists("q", $parameters)){
		$keyword = $parameters['q'];
	}

	if(!empty($start)&&!empty($limit)){
		$start--;

		$stmt = null;
		if(empty($keyword)){
			$sql = "SELECT id,name FROM categories LIMIT :start, :limit";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':start', $start,PDO::PARAM_INT);
			$stmt->bindParam(':limit', $start,PDO::PARAM_INT);
		} else {
			$keywordLike = "%".$keyword."%";
			$sql = "SELECT id,name FROM categories WHERE name LIKE :keyword LIMIT :start, :limit";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':start', $start,PDO::PARAM_INT);
			$stmt->bindParam(':limit', $start,PDO::PARAM_INT);
			$stmt->bindParam(':keyword', $keywordLike);
		}
		$stmt->execute();

		$sqlCount = null;
		$total = 0;
		if(empty($keyword)){
			$sqlCount = "SELECT count(id) FROM categories";
			$stmtCount = DB::prepare($sqlCount);
			$stmt->execute();
			$total = $stmtCount->fetchColumn();
		} else {
			$keywordLike = "%".$keyword."%";
			$sqlCount = "SELCT count(id) FROM categories WHERE name LIKE :keyword";
			$stmtCount = DB::prepare($sqlCount);
			$stmtCount->bindParam(':keyword', $keywordLike);
			$stmtCount->execute();
			$total = $stmtCount->fetchColumn();
		}

		return $response->withJson($stmt->fetchAll())->withHeader('Access-Control-Expose-Header','x-total-count')->withHeader('x-total-count', $total);
	} else {
		$sql = "SELECT id,name FROM categories";
		$stmt = DB::prepare($sql);
		$stmt->execute();

		return $response->withJson($stmt->fetchAll());
	}
})->add($auth);




//****************************************************************
// REMOVER / DELETAR
//****************************************************************
$app->delete('/category/{id}', function (Request $request, Response $response) {
	try{
		$id = $request->getAttribute('id');
		if(!empty($id)){

			$sql = "DELETE FROM categories WHERE id=:id";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':id', $id,PDO::PARAM_INT);
			$stmt->execute();
			return $response;
		}
	}
	catch(\Exception $e){
		return $response->withStatus(500)->write($e->getMessage());
	}
})->add($auth);




//****************************************************************
// INCLUIR
//****************************************************************
$app->post('/category', function (Request $request, Response $response) {
	try{
		$category = (object)$request->getParsedBody();
		if(!empty($category->id)){
			//update
			$sql = "UPDATE categories SET name:name WHERE id=:id";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':id', $category->id, PDO::PARAM_INT);
			$stmt->execute();
			return $response->withJson($category);
		} else {
			//insert
			$sql = "INSERT INTO categories (name) VALUES (:name)";
			$stmt = DB::prepare($sql);
			$stmt->bindParam(':name', $category->name);
			$stmt->execute();
			$category->id = DB::lastInsertId();
			return $response->withJson($category);
		}
	}
	catch(\Exception $e){
		return $response->withStatus(500)->write($e->getMessage());
	}
})->add($auth);