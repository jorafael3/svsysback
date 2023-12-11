<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Rutas extends Controller
{


    function __construct()
    {
        parent::__construct();
        //$this->view->render('principal/index');
        //echo "nuevo controlaodr";
    }

    function Cargar_Chofer()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Chofer($param);
        } else {
            die();
        }
    }

    function Cargar_Clientes()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Clientes($param);
        } else {
            die();
        }
    }

    function Cargar_Clientes_Sucursales()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Clientes_Sucursales($param);
        } else {
            die();
        }
    }

    function Cargar_Productos()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Productos($param);
        } else {
            die();
        }
    }

    function Cargar_Rutas()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Rutas($param);
        } else {
            die();
        }
    }


    function Cargar_Rutas_dia()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Rutas_dia($param);
        } else {
            die();
        }
    }

    function Nueva_Ruta()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Nueva_Ruta($param);
        } else {
            die();
        }
    }

    function Nueva_Ruta_Dia()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Nueva_Ruta_Dia($param);
        } else {
            die();
        }
    }

    function Nueva_Ruta_Dia_detalle()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Nueva_Ruta_Dia_detalle($param);
        } else {
            die();
        }
    }
    function Actualizar_Ruta_Dia_detalle()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Actualizar_Ruta_Dia_detalle($param);
        } else {
            die();
        }
    }
}
