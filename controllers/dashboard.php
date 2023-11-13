<?php
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Dashboard extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function Cargar_Stats()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Cargar_Stats($param);
        } else {
            die();
        }
    }

}
