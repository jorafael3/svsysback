<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Usuarios extends Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->view->render('principal/index');
        //echo "nuevo controlaodr";
    }

    function Cargar_Usuarios()
    {
        $param1 = $_POST['param'];
        // echo json_encode($param1);
        $Ventas =  $this->model->Consultar_Cliente($param1);
    }

    function Consultar_Accesos()
    {
        $param1 = $_POST['param'];
        // echo json_encode($param1);
        $Ventas =  $this->model->Consultar_Accesos($param1);
    }
}
