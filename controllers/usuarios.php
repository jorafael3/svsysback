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

    function Validar_Usuario()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $funcion =  $this->model->Validar_Usuario_movil($param);
        } else {
            die();
        }
    }

    function Cargar_Usuarios()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Consultar_Usuarios($param);
        } else {
            die();
        }
    }

    function Cargar_Departamentos()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Cargar_Departamentos($param);
        } else {
            die();
        }
    }
    function Cargar_Sucursales()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Cargar_Sucursales($param);
        } else {
            die();
        }
    }

    //**** USUARIOS */
    function Nuevo_Usuario()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Nuevo_Usuario($param);
        } else {
            die();
        }
    }

    function Editar_Usuario()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->Editar_Usuario($param);
        } else {
            die();
        }
    }


    function ActivarDesact_Usuario()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            // echo json_encode($param);
            $Ventas =  $this->model->ActivarDesact_Usuario($param);
        } else {
            die();
        }
    }

    // ACCESO
    function Consultar_Accesos()
    {
        $param1 = $_POST['param'];
        // echo json_encode($param1);
        $Ventas =  $this->model->Consultar_Accesos($param1);
    }

    function Guardar_Accesos()
    {
        $param1 = $_POST['param'];
        // echo json_encode($param1);
        $Ventas =  $this->model->Guardar_Accesos($param1);
    }

    //**** MOVIL */
    function Validar_Usuario_movil()
    {
        // try {
        //     $json_data = file_get_contents('php://input');
        //     $data = json_decode($json_data, true);

        //     if (isset($json_data)) {

        //         if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
        //             // $param1 = $data['param1'];
        //             $funcion =  $this->model->Validar_Usuario_movil($data);

        //         } else {
        //             die();
        //         }
        //     } else {
        //         die();
        //     }
        // } catch (Exception $e) {
        //     die();
        // }
        echo json_encode("LLEGO DATO");
        exit();
    }
}
