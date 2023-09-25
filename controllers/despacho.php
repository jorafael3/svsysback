<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Despacho extends Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->view->render('principal/index');
        //echo "nuevo controlaodr";
    }

    function Cargar_Guia()
    {
        $param1 = $_POST['param'];
        $Ventas =  $this->model->Cargar_Guia($param1);
    }

    function Cargar_Guia_p()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        // $param1 = $data['param1'];
        $Ventas =  $this->model->Cargar_Guia($data);
        echo json_encode($Ventas);
        exit();
    }
}
