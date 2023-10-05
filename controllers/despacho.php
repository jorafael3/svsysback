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





    //***************** MOVIL */

    function Cargar_Guia_p()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        // $param1 = $data['param1'];
        $Ventas =  $this->model->Cargar_Guia($data);
        echo json_encode($Ventas);
        exit();
    }

    function Cargar_Gui_Servicios()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Cargar_Gui_Servicios($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }

    function Cargar_Gui_Destinos()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Cargar_Gui_Destinos($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }

    function Guardar_Guias_despacho()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Guardar_Guias_despacho($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }

    //************** MIS GUIAS */

    function Guias_Usuario()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Guias_Usuario($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }

    function Consultar_guia_despachadas()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Consultar_guia_despachadas($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }

    function Consultar_guia_despachadas_dt()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Consultar_guia_despachadas_dt($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }

    function Consultar_guia_despachadas_cabecera()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Consultar_guia_despachadas_cabecera($data);
                } else {
                    die();
                }
            } else {
                die();
            }
        } catch (Exception $e) {
            die();
        }
    }
}
