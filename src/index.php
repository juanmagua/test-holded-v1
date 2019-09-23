<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../include/dbHandler.php';
require '../vendor/autoload.php';

$app = new \Slim\App;


$app->get('/widgets', function() {
  $db = new dbHandler();
  $cur = $db->getAllWidgets();
  //Variable to store result
  $result = array();
  //Do itteration for all document in a collection
  foreach ($cur as $doc) {
    $tmp = array();
    //Set key and get value from document and store to temporary array
    $tmp["title"] = $doc["title"];
    $tmp["color"] = $doc["color"];
    //push temporary array to $result
    array_push($result,$tmp);
  }
  //show result
  response(200, $result);
});

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});
$app->run();