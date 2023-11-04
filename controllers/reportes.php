<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Reportes extends Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->view->render('principal/index');
        //echo "nuevo controlaodr";
    }


    function Reporte_Clientes_General()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Reporte_Clientes_General($param);
        } else {
            die();
        }
    }

    function Reporte_Chofer_General()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Reporte_Chofer_General($param);
        } else {
            die();
        }
    }
}
