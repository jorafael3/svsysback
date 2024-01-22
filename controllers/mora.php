<?php
// Permitir solicitudes desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Mora extends Controller
{


    function __construct()
    {
        parent::__construct();
    }

    function Cargar_Dashboard()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Dashboard($param);
        } else {
            die();
        }
    }

    ///***  EVOLUCION_MOROSIDAD


    function CARGAR_EVOLUCION_MOROSIDAD_TABLA()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->CARGAR_EVOLUCION_MOROSIDAD_TABLA($param);
        } else {
            die();
        }
    }

    function CARGAR_EVOLUCION_MOROSIDAD_GRAFICO()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->CARGAR_EVOLUCION_MOROSIDAD_GRAFICO($param);
        } else {
            die();
        }
    }

    //**   Descripcion_Colocacion/

    function Descripcion_Colocacion()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Descripcion_Colocacion($param);
        } else {
            die();
        }
    }


    function CARGAR_POR_PLAZO()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->CARGAR_POR_PLAZO($param);
        } else {
            die();
        }
    }

    function CARGAR_POR_MONTO()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->CARGAR_POR_MONTO($param);
        } else {
            die();
        }
    }

    function Cargar_Creditos_Cancelados()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Creditos_Cancelados($param);
        } else {
            die();
        }
    }


    //************************************************* */
    //************************************************* */
    //************************************************* */
    //************ POR CLIENTE */

    function CARGAR_CLIENTES()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->CARGAR_CLIENTES($param);
        } else {
            die();
        }
    }

    //************************************************* */
    //*MOROSIDAD

    function MOROSIDAD_POR_DIA()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->MOROSIDAD_POR_DIA($param);
        } else {
            die();
        }
    }

    function MOROSIDAD_CARTERA()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->MOROSIDAD_CARTERA($param);
        } else {
            die();
        }
    }

    //* COMPROTAMIOENTO


    function COMPORTAMIENTO()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->COMPORTAMIENTO($param);
        } else {
            die();
        }
    }
}
