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


    //************************************************* */
    //************************************************* */
    //************************************************* */
    //************ POR CLIENTE */

    function Cargar_Datos_Cliente()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Datos_Cliente($param);
        } else {
            die();
        }
    }
}
