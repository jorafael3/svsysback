<?php
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Choferes extends Controller
{


    function __construct()
    {
        parent::__construct();
    }

    function Cargar_Choferes()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Cargar_Choferes($param);
        } else {
            die();
        }
    }

    function Cargar_Usuarios()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Cargar_Usuarios($param);
        } else {
            die();
        }
    }

    function Actualizar_Chofer()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Actualizar_Chofer($param);
        } else {
            die();
        }
    }

    function Nuevo_Chofer()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Nuevo_Chofer($param);
        } else {
            die();
        }
    }

    function ActivarDesact_Chofer()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->ActivarDesact_Chofer($param);
        } else {
            die();
        }
    }
}
