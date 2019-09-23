<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['determineRouteBeforeAppMiddleware'] = true;


$app = new \Slim\App(['settings' => $config]);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

require '../include/dbHandler.php';

$app->get('/test', function(Request $request, Response $response) {
    $username = "test";
    $password = password_hash("holden", PASSWORD_DEFAULT);
    echo $password;
    die;
});

// login
$app->post('/login', function(Request $request, Response $response) {

    $request_data = $request->getParsedBody();

    $username = $request_data['username'];

    $password = $request_data['password'];

    $db = new dbHandler();

    $user = $db->getUser($username);


    // User Exist
    if ($user == null) {
        $message = array();
        $message['error'] = true;
        $message['message'] = 'User no exist.';
        $response->write(json_encode($message));
        return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(500);
    } else if (!$db->validatePassword($password, $user->password)) {
        $message = array();
        $message['error'] = true;
        $message['message'] = 'Password Incorrect';
        $response->write(json_encode($message));
        return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(500);
    }


    $token = bin2hex(openssl_random_pseudo_bytes(8));
    $token_expire = date('Y-m-d H:i:s', strtotime('+6 hour'));

    $db->updateUser($user->_id, $token, $token_expire);

    $message = array();
    $message['error'] = false;
    $message['message'] = 'Login successfully';
    $message['user'] = array('username' => $user->username, 'token' => $token);
    $response->write(json_encode($message));
    return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
});



// GET ALL
$app->get('/widgets', function(Request $request, Response $response) {

    $db = new dbHandler();
    $cur = $db->getAllWidgets();
    //Variable to store result
    $result = array();



    //Do itteration for all document in a collection
    foreach ($cur as $doc) {
        $tmp = array();
        //Set key and get value from document and store to temporary array
        $tmp["id"] = (string) $doc->_id;
        $tmp["title"] = $doc->title;
        $tmp["color"] = $doc->color;
        $tmp["width"] = $doc->width;
        $tmp["height"] = $doc->height;
        //push temporary array to $result
        array_push($result, $tmp);
    }

    $response->write(json_encode($result));
    return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
});


// Create Widget
$app->post('/widgets', function(Request $request, Response $response) {


    $request_data = $request->getParsedBody();

    $title = $request_data['title'];
    $color = $request_data['color'];
    $width = $request_data['width'];
    $height = $request_data['height'];


    $db = new dbHandler();
    $cur = $db->insertWidget($title, $color, $width, $height);

    if ($cur) {
        $message = array();
        $message['error'] = false;
        $message['message'] = 'User created successfully';
        $message['widget'] = array($cur, $title, $color, $width, $height);
        $response->write(json_encode($message));
        return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(200);
    } else {

        $message = array();
        $message['error'] = true;
        $message['message'] = 'INSERT_FAILED';

        $response->write(json_encode($message));
        return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(500);
    }
});


// Create Widget
$app->put('/widgets', function(Request $request, Response $response, array $args) {

    $request_data = $request->getParsedBody();

    $id = $request_data['id'];
    $title = $request_data['title'];
    $color = $request_data['color'];
    $width = $request_data['width'];
    $height = $request_data['height'];


    $db = new dbHandler();
    $cur = $db->updateWidget($id, $title, $color, $width, $height);

    if ($cur) {
        $message = array();
        $message['error'] = false;
        $message['message'] = 'Update successfully';
        $message['widget'] = array('id' => $id, 'title' => $title, 'color' => $color, 'width' => $width, 'height' => $height);
        $response->write(json_encode($message));
        return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(200);
    } else {

        $message = array();
        $message['error'] = true;
        $message['message'] = 'Update failed';
        $response->write(json_encode($message));
        return $response
                        ->withHeader('Access-Control-Allow-Origin', '*')
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(500);
    }
});


$app->run();
