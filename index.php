<?php
require_once('./vendor/autoload.php');
require_once('./back-end/controllers/usuarios.controller.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function getValidToken()
{
    $key = $_SERVER['TOKEN_KEY'];
    $headers = apache_request_headers();
    $authorization = $headers["Authorization"];
    $decodedToken =  JWT::decode($authorization, new Key($key, 'HS256'));
    if ($decodedToken) {
        error_log(json_encode($decodedToken));
        return $decodedToken;
    } else {
        return false;
    }
}

Flight::before('start', function () {
    error_log("Settings default headers");

    Flight::response()->header('Access-Control-Allow-Origin', '*');
    Flight::response()->header('Content-Type', 'application/json; charset=UTF-8');
    Flight::response()->header('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS');
    Flight::response()->header('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers,Authorization, x-Requested-With');
});

Flight::route('OPTIONS /*', function () {
    Flight::response()->header('Access-Control-Allow-Origin', '*');
    Flight::response()->header('Content-Type', 'application/json; charset=UTF-8');
    Flight::response()->header('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS');
    Flight::response()->header('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers,Authorization, x-Requested-With');

    echo json_encode(array("status" => "ok"));
});

Flight::route('POST /login', function () {
    $user_controller = new Usuarios_controller();
    $body = Flight::request()->getBody();
    $data = json_decode($body, true);
    $usuario = ($data['usuario'] === null || $data['usuario'] === "") ? null : $data['usuario'];
    $clave = ($data['clave'] === null || $data['clave'] === "") ? null : $data['clave'];
    $respuesta = $user_controller->validateLogin($usuario, $clave);
    if (is_string($respuesta)) {
        $respuesta = json_decode($respuesta, true);
    }
    if (isset($respuesta['data'])) {
        error_log("El objeto 'data' existe");
        $now = strtotime("now");
        $key = $_SERVER['TOKEN_KEY'];
        $payload = [
            'exp' => $now + 3600, // dura 1 hora
            'data' => $respuesta['data']
        ];
        $jwt = JWT::encode($payload, $key, 'HS256');
        $respuesta['data']['token'] = $jwt;
        echo json_encode($respuesta);
    } else {
        error_log("El objeto 'data' no existe");
        echo json_encode($respuesta);
    }
});

Flight::route('POST /registrarUsuario', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera !== false) {
        $user_controller = new Usuarios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $usuario = $data['usuario'] ?? null;
        $clave = $data['clave'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $perfil = $data['perfil'] ?? null;

        $respuesta = $user_controller->insertUser($nombre, $apellido, $perfil, $usuario, $clave);
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});


Flight::route('PUT /actualizaUsuario', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $user_controller = new Usuarios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_usuario = $data['id'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $clave = $data['clave'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $apellido = $data['apellido'] ?? null;
        $perfil = $data['perfil'] ?? null;

        $respuesta = $user_controller->updateUser($id_usuario, $nombre, $apellido, $perfil, $usuario, $clave);
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});


Flight::route('PUT /eliminarUsuario', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $user_controller = new Usuarios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_usuario = $data['id'] ?? null;

        $respuesta = $user_controller->deleteUsuario($id_usuario);
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('GET /consultarUsuarios', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $user_controller = new Usuarios_controller();
        $respuesta = $user_controller->getAllUsers();
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});



Flight::start();
