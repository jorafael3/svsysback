<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");

// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function prueba_datos()
{
    // $data = json_decode(file_get_contents('php://input'), true);
    // $respuesta = array('mensaje' => 'Hola desde PHP');
    // // $param1 = $_POST['param'];
    // $param2 = isset($_GET['param1']) == true ? $_GET['param1'] : false;
    echo json_encode("hola");
    exit();
}
prueba_datos();
