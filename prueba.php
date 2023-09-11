<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");

// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Permitir ciertos encabezados personalizados, si es necesario
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function Guardar_datos()
{
    $data = json_decode(file_get_contents('php://input'), true);
    $respuesta = array('mensaje' => 'Hola desde PHP');
    echo json_encode($respuesta);
    // exit();
}
// Guardar_datos();
