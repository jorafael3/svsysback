<?php
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Clientes extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function Cargar_Clientes()
    {
        $param1 = $_POST['param'];
        $Ventas =  $this->model->Cargar_Clientes();
    }
    function Cargar_Clientes_m()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        $Ventas =  $this->model->Cargar_Clientes();
    }

    function Nuevo_Cliente()
    {
        $param1 = $_POST['param'];
        $Ventas =  $this->model->Nuevo_Cliente($param1);
    }
}
