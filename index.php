<?php
require_once('./vendor/autoload.php');
require_once('./back-end/controllers/usuarios.controller.php');
require_once('./back-end/controllers/pedidos.controller.php');
require_once('./back-end/controllers/servicios.controller.php');
require_once('./back-end/controllers/clientes.controller.php');
require_once('./back-end/controllers/descuentos.controller.php');
require_once('./back-end/controllers/material.controllers.php');
require_once('./back-end/controllers/estados.controllers.php');
require_once('./back-end/controllers/recomendacion_lavado.controllers.php');
require_once('./back-end/controllers/asignaciones_empleado.controllers.php');


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function getValidToken()
{
    $respuesta_token = false;
    $os = PHP_OS_FAMILY;

    if ($os === 'Windows') {
        $key = $_SERVER['TOKEN_KEY'];
        $headers = apache_request_headers();
        error_log("------------------------ENCABEZADOS " . implode(",", $headers));
        $authorization = $headers["Authorization"];
        $decodedToken =  JWT::decode($authorization, new Key($key, 'HS256'));
        if ($decodedToken) {
            error_log(json_encode($decodedToken));
            $respuesta_token = $decodedToken;
        } else {
            $respuesta_token = false;
        }
    } else {

        $key = $_SERVER['TOKEN_KEY'];
        $authorization = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        error_log("AUTORIZACIOOOOOOOOOONNNNN " . $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        if ($authorization) {
            error_log(json_encode($authorization));
            $respuesta_token =  $authorization;
        } else {
            $respuesta_token = false;
        }
    }
    return $respuesta_token;
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
            'exp' => $now + 3600000, // dura 1 hora
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


Flight::route('PUT /actualizarUsuario', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $user_controller = new Usuarios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_usuario = $data['id_usuario'] ?? null;
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
// Ruta para consultar todos los servicios (GET)
Flight::route('GET /consultarServicios', function () {
    $tokenDesdeCabecera = getValidToken(); // Verificación del token de autenticación
    if ($tokenDesdeCabecera) {
        $servicios_controller = new Servicios_controller();
        $respuesta = $servicios_controller->getAllServices();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para insertar un nuevo servicio (POST)
Flight::route('POST /registrarServicios', function () {
    $tokenDesdeCabecera = getValidToken(); // Verificación del token de autenticación
    if ($tokenDesdeCabecera == true) {
        $servicios_controller = new Servicios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $descripcion_servicio = $data['descripcion_servicio'] ?? null;
        $costo_unitario = $data['costo_unitario'] ?? null;
        $validar_pesaje = $data['validar_pesaje'] ?? null;

        $respuesta = $servicios_controller->insertService($descripcion_servicio, $costo_unitario, $validar_pesaje);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para actualizar un servicio existente (PUT)
Flight::route('PUT /actualizarServicios', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $servicios_controller = new Servicios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_servicio = $data['id_servicio'] ?? null;
        $descripcion_servicio = $data['descripcion_servicio'] ?? null;
        $costo_unitario = $data['costo_unitario'] ?? null;
        $validar_pesaje = $data['validar_pesaje'] ?? null;

        $respuesta = $servicios_controller->updateService($id_servicio, $descripcion_servicio, $costo_unitario, $validar_pesaje);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para eliminar un servicio (DELETE)
Flight::route('PUT /eliminarServicio', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $servicios_controller = new Servicios_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_servicio = $data['id_servicio'] ?? null;

        $respuesta = $servicios_controller->deleteService($id_servicio);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para buscar un servicio por ID (GET)
Flight::route('GET /buscarServicioPorId/@id_servicio', function ($id_servicio) {
    $tokenDesdeCabecera = getValidToken(); // Verificación del token de autenticación
    if ($tokenDesdeCabecera) {
        $servicios_controller = new Servicios_controller();
        $respuesta = $servicios_controller->findServiceById($id_servicio);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para buscar servicios por descripción (GET)
Flight::route('GET /buscarServicioPorDescripcion', function () {
    $tokenDesdeCabecera = getValidToken(); // Verificación del token de autenticación
    if ($tokenDesdeCabecera) {
        $servicios_controller = new Servicios_controller();
        $data = Flight::request()->query;
        $respuesta = $servicios_controller->findServiceByDescription($data->descripcion_servicio);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('POST /registrarCliente', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $cliente_controller = new Clientes_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $identificacion_cliente = $data['identificacion_cliente'] ?? null;
        $tipo_identificacion_cliente = $data['tipo_identificacion_cliente'] ?? null;
        $nombre_cliente = $data['nombre_cliente'] ?? null;
        $apellido_cliente = $data['apellido_cliente'] ?? null;
        $telefono_cliente = $data['telefono_cliente'] ?? null;
        $correo_cliente = $data['correo_cliente'] ?? null;

        $respuesta = $cliente_controller->insertCliente($identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /actualizarCliente', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $cliente_controller = new Clientes_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_cliente = $data['id_cliente'];
        $identificacion_cliente = $data['identificacion_cliente'] ?? null;
        $tipo_identificacion_cliente = $data['tipo_identificacion_cliente'] ?? null;
        $nombre_cliente = $data['nombre_cliente'] ?? null;
        $apellido_cliente = $data['apellido_cliente'] ?? null;
        $telefono_cliente = $data['telefono_cliente'] ?? null;
        $correo_cliente = $data['correo_cliente'] ?? null;

        $respuesta = $cliente_controller->updateCliente($id_cliente, $identificacion_cliente, $tipo_identificacion_cliente, $nombre_cliente, $apellido_cliente, $telefono_cliente, $correo_cliente);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});


Flight::route('PUT /eliminarCliente', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $cliente_controller = new Clientes_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_cliente = $data['id_cliente'] ?? null;

        $respuesta = $cliente_controller->deleteCliente($id_cliente);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('GET /consultarClientes', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $cliente_controller = new Clientes_controller();
        $respuesta = $cliente_controller->getAllClientes();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para registrar un nuevo descuento
Flight::route('POST /registrarDescuentos', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $descuento_controller = new Descuentos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $tipo_descuento_desc = $data['tipo_descuento_desc'] ?? null;
        $cantidad_descuento = $data['cantidad_descuento'] ?? null;
        $respuesta = $descuento_controller->insertDescuento($tipo_descuento_desc, $cantidad_descuento);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para actualizar un descuento existente
Flight::route('PUT /actualizarDescuentos', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $descuento_controller = new Descuentos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_tipo_descuento = $data['id_tipo_descuento'] ?? null;
        $tipo_descuento_desc = $data['tipo_descuento_desc'] ?? null;
        $cantidad_descuento = $data['cantidad_descuento'] ?? null;

        $respuesta = $descuento_controller->updateDescuento($id_tipo_descuento, $tipo_descuento_desc, $cantidad_descuento);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para eliminar un descuento
Flight::route('PUT /eliminarDescuentos', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $descuento_controller = new Descuentos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_tipo_descuento = $data['id_tipo_descuento'] ?? null;

        $respuesta = $descuento_controller->deleteDescuentos($id_tipo_descuento);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Ruta para consultar todos los descuentos
Flight::route('GET /consultarDescuentos', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $descuento_controller = new Descuentos_controller();
        $respuesta = $descuento_controller->getAllDescuentos();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// materialesssssssssssssssssssssssssssssssssssssssssssss
Flight::route('POST /registrarMaterial', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $materiales_controller = new Materiales_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $descripcion_material = $data['descripcion_material'] ?? null;

        $respuesta = $materiales_controller->insertMaterial($descripcion_material);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /editarMaterial', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $materiales_controller = new Materiales_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_material = $data['id_material'] ?? null;
        $descripcion_material = $data['descripcion_material'] ?? null;

        $respuesta = $materiales_controller->updateMaterial($id_material, $descripcion_material);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});
Flight::route('PUT /eliminarMaterial', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $materiales_controller = new Materiales_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_material = $data['id_material'] ?? null;

        $respuesta = $materiales_controller->deleteMaterial($id_material);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('GET /consultarMateriales', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $materiales_controller = new Materiales_controller();
        $respuesta = $materiales_controller->getAllMaterials();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});
// estadossssssssssssssssssssssssssssssssssssssssssssss
// Registrar un nuevo estado
Flight::route('POST /registrarEstado', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $estado_controller = new Estados_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $descripcion_estado = $data['descripcion_estado'] ?? null;

        $respuesta = $estado_controller->insertState($descripcion_estado);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Actualizar un estado existente
Flight::route('PUT /actualizarEstado', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $estado_controller = new Estados_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_estado = $data['id_estado'] ?? null;
        $descripcion_estado = $data['descripcion_estado'] ?? null;

        $respuesta = $estado_controller->updateState($id_estado, $descripcion_estado);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Eliminar un estado existente
Flight::route('PUT /eliminarEstado', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $estado_controller = new Estados_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_estado = $data['id_estado'] ?? null;

        $respuesta = $estado_controller->deleteState($id_estado);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// Consultar todos los estados
Flight::route('GET /consultarEstados', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $estado_controller = new Estados_controller();
        $respuesta = $estado_controller->getAllStates();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

// RECOMENDACIONESSSSSSSSSSSSSSS
Flight::route('POST /registrarRecomendacion', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $recomendacion_controller = new RecomendacionLavado_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $descripcion_material = $data['descripcion_material'] ?? null;
        $descripcion_servicio = $data['descripcion_servicio'] ?? null;

        $respuesta = $recomendacion_controller->insertRecommendation($descripcion_material, $descripcion_servicio);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /actualizarRecomendacion', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $recomendacion_controller = new RecomendacionLavado_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_recomendacion_lavado = $data['id_recomendacion_lavado'] ?? null;
        $descripcion_material = $data['descripcion_material'] ?? null;
        $descripcion_servicio = $data['descripcion_servicio'] ?? null;

        $respuesta = $recomendacion_controller->updateRecommendation($id_recomendacion_lavado, $descripcion_material, $descripcion_servicio);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /eliminarRecomendacion', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $recomendacion_controller = new RecomendacionLavado_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_recomendacion_lavado = $data['id_recomendacion_lavado'] ?? null;

        $respuesta = $recomendacion_controller->deleteRecommendation($id_recomendacion_lavado);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('GET /consultarRecomendaciones', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $recomendacion_controller = new RecomendacionLavado_controller();
        $respuesta = $recomendacion_controller->getAllRecommendations();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});
// asignacionessssssssssssssssssssssssss
Flight::route('POST /registrarAsignacion', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $asignaciones_controller = new AsignacionesEmpleado_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $usuario = $data['usuario'] ?? null;
        $fecha_inicio = $data['fecha_inicio'] ?? null;
        $fecha_fin = isset($data['fecha_fin']) ? $data['fecha_fin'] : null;
        $id_pedido_cabecera = $data['id_pedido_cabecera'] ?? null;
        $descripcion_estado = $data['descripcion_estado'] ?? null;

        $respuesta = $asignaciones_controller->insertAssignment( $usuario,  $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /actualizarAsignacion', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $asignaciones_controller = new AsignacionesEmpleado_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_asignaciones = $data['id_asignaciones'] ?? null;
        $usuario = $data['usuario'] ?? null;
        $fecha_inicio = $data['fecha_inicio'] ?? null;
        $fecha_fin = $data['fecha_fin'] ?? null;
        $id_pedido_cabecera = $data['id_pedido_cabecera'] ?? null;
        $descripcion_estado = $data['descripcion_estado'] ?? null;

        $respuesta = $asignaciones_controller->updateAssignment($id_asignaciones, $usuario, $fecha_inicio, $fecha_fin, $id_pedido_cabecera, $descripcion_estado);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});
Flight::route('PUT /eliminarAsignacion', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $asignaciones_controller = new AsignacionesEmpleado_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_asignaciones = $data['id_asignaciones'] ?? null;

        $respuesta = $asignaciones_controller->deleteAssignment($id_asignaciones);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});
Flight::route('GET /consultarAsignaciones', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $asignaciones_controller = new AsignacionesEmpleado_controller();
        $respuesta = $asignaciones_controller->getAllAssignments();
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});
Flight::route('GET /consultarPedidos', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $controller = new Pedidos_controller();
        $respuesta = $controller->getAllPedidos();
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('POST /registrarPedido', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $controller = new Pedidos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $fecha_pedido = $data['fecha_pedido'] ?? null;
        $fk_id_usuario = $data['fk_id_usuario'] ?? null;
        $cantidad_articulos = $data['cantidad_articulos'] ?? null;
        $fk_id_cliente = $data['fk_id_cliente'] ?? null;
        $fk_id_descuentos = $data['fk_id_descuentos'] ?? null;
        $pedido_subtotal = $data['pedido_subtotal'] ?? null;
        $estado_pago = $data['estado_pago'] ?? null;
        $valor_pago = $data['valor_pago'] ?? null;

        $fecha_recoleccion_estimada = $data['fecha_recoleccion_estimada'] ?? null;
        $hora_recoleccion_estimada = $data['hora_recoleccion_estimada'] ?? null;
        $fecha_entrega_estimada = $data['fecha_entrega_estimada'] ?? null;
        $hora_entrega_estimada = $data['hora_entrega_estimada'] ?? null;

        $direccion_recoleccion = $data['direccion_recoleccion'] ?? null;
        $direccion_entrega = $data['direccion_entrega'] ?? null;
        $tipo_entrega = $data['tipo_entrega'] ?? null;

        $respuesta = $controller->insertPedidos(
            $fecha_pedido,
            $fk_id_usuario,
            $cantidad_articulos,
            $fk_id_cliente,
            $fk_id_descuentos,
            $pedido_subtotal,
            $estado_pago,
            $valor_pago,
            $fecha_recoleccion_estimada,
            $hora_recoleccion_estimada,
            $direccion_recoleccion,
            $fecha_entrega_estimada,
            $hora_entrega_estimada,
            $direccion_entrega,
            $tipo_entrega
        );
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});


Flight::route('PUT /actualizarPedido', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $controller = new Pedidos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_pedido_cabecera = $data['id_pedido_cabecera'] ?? null;
        $fk_id_usuario = $data['fk_id_usuario'] ?? null;
        $cantidad_articulos = $data['cantidad_articulos'] ?? null;
        $fk_id_cliente = $data['fk_id_cliente'] ?? null;
        $fk_id_descuentos = $data['fk_id_descuentos'] ?? null;
        $pedido_subtotal = $data['pedido_subtotal'] ?? null;
        $estado_pago = $data['estado_pago'] ?? null;
        $valor_pago = $data['valor_pago'] ?? null;
        $fecha_hora_recoleccion_estimada = $data['fecha_hora_recoleccion_estimada'] ?? null;
        $direccion_recoleccion = $data['direccion_recoleccion'] ?? null;
        $fecha_hora_entrega_estimada = $data['fecha_hora_entrega_estimada'] ?? null;
        $direccion_entrega = $data['direccion_entrega'] ?? null;
        $tipo_entrega = $data['tipo_entrega'] ?? null;

        $respuesta = $controller->updatePedidos(
            $id_pedido_cabecera,
            $fk_id_usuario,
            $cantidad_articulos,
            $fk_id_cliente,
            $fk_id_descuentos,
            $pedido_subtotal,
            $estado_pago,
            $valor_pago,
            $fecha_hora_recoleccion_estimada,
            $direccion_recoleccion,
            $fecha_hora_entrega_estimada,
            $direccion_entrega,
            $tipo_entrega
        );
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /eliminarPedido', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $controller = new Pedidos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $idPedidoCabecera = $data['id_pedido_cabecera'] ?? null;

        $respuesta = $controller->deletePedido($idPedidoCabecera);
        echo $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('POST /registrarItemPedido', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $controller = new Pedidos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $fk_id_servicio = $data['fk_id_servicio'] ?? null;
        $libras = $data['libras'] ?? null;
        $precio_servicio = $data['precio_servicio'] ?? null;
        $fk_id_pedido = $data['fk_id_pedido'] ?? null;
        $descripcion_articulo = $data['descripcion_articulo'] ?? null;


        $respuesta = $controller->insertItemsPedidos(
            $fk_id_servicio,
            $libras,
            $precio_servicio,
            $fk_id_pedido,
            $descripcion_articulo
        );
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});

Flight::route('PUT /editarItemPedido', function () {
    $tokenDesdeCabecera = getValidToken();
    if ($tokenDesdeCabecera == true) {
        $controller = new Pedidos_controller();
        $body = Flight::request()->getBody();
        $data = json_decode($body, true);

        $id_pedido_detalle = $data['id_pedido_detalle'] ?? null;
        $fk_id_servicio = $data['fk_id_servicio'] ?? null;
        $libras = $data['libras'] ?? null;
        $precio_servicio = $data['precio_servicio'] ?? null;
        $fk_id_pedido = $data['fk_id_pedido'] ?? null;
        $descripcion_articulo = $data['descripcion_articulo'] ?? null;


        $respuesta = $controller->actualizarItemsPedidos(
            $id_pedido_detalle,
            $fk_id_servicio,
            $libras,
            $precio_servicio,
            $fk_id_pedido,
            $descripcion_articulo
        );
        echo  $respuesta;
    } else {
        echo json_encode(array("respuesta" => "0", "mensaje" => "Petición no autorizada"));
    }
});


Flight::start();
