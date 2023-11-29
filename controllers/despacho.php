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

    function Cargar_Guias_Sin_Despachar()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Guias_Sin_Despachar($param);
        } else {
            die();
        }
    }

    function Cargar_Guias_Sin_Despachar_detalle()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Guias_Sin_Despachar_detalle($param);
        } else {
            die();
        }
    }

    function Reasignar_Nueva_placa()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Reasignar_Nueva_placa($param);
        } else {
            die();
        }
    }

    //****** GUIAS DESPACHADAS */

    function Guias_Despachadas_General()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Guias_Despachadas_General($param);
        } else {
            die();
        }
    }

    function Guias_Despachadas_General_detalle()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Guias_Despachadas_General_detalle($param);
        } else {
            die();
        }
    }

    function Guias_Despachadas_Historial()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Guias_Despachadas_Historial($param);
        } else {
            die();
        }
    }

    function Guias_Despachadas_Historial_detalle()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Guias_Despachadas_Historial_detalle($param);
        } else {
            die();
        }
    }

    //*** GUIAS EN PROCESO DESP */

    
    function Guias_En_Proceso_Despacho()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Guias_En_Proceso_Despacho($param);
        } else {
            die();
        }
    }

    ///***** FACTURA */

    function Obtener_Parametros()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Obtener_Parametros($param);
        } else {
            die();
        }
    }

    function Guardar_Factura()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Guardar_Factura($param);
        } else {
            die();
        }
    }

    function Cargar_facturas_Pedido()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_facturas_Pedido($param);
        } else {
            die();
        }
    }

    //****************************************************
    //**** GUIAS RETIRADAS

    function Cargar_Guias_retiradas_Vigentes()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Guias_retiradas_Vigentes($param);
        } else {
            die();
        }
    }

    function Cargar_Guias_retiradas_Entregas()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Guias_retiradas_Entregas($param);
        } else {
            die();
        }
    }
    
    function Cargar_Guias_retiradas_No_Ingresadas()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {
            $Ventas =  $this->model->Cargar_Guias_retiradas_No_Ingresadas($param);
        } else {
            die();
        }
    }
    

    //*********************************************** */
    //*********************************************** */
    //*********************************************** */
    //*********************************************** */
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

    function Guardar_Imagenes()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        // $data = json_decode($json_data, true);
        $imageData = base64_decode($data["image"]);
        $targetDirectory = "C:/xampp/htdocs/svsysback/recursos/guias_subidas/";
        $fileName = "imagen.jpg";
        $targetFile = $targetDirectory . $fileName;
        if (file_put_contents($targetFile, $imageData)) {
            echo "La imagen se ha guardado correctamente en: " . $targetFile;
        } else {
            echo "Error al guardar la imagen.";
        }

        // echo json_encode($data->image);
        // exit();
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

    function Guardar_Imagen_guia_despachada()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Guardar_Imagen_guia_despachada($data);
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


    //******** GUIAS PARCIAL DESPACHO */

    function Cargar_Guia_parcial()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Cargar_Guia_parcial($data);
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

    function Guardar_Guias_despacho_parcial()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Guardar_Guias_despacho_parcial($data);
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


    //********* GUIAS ASIGNADAS */

    function Cargar_guias_asignadas()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Cargar_guias_asignadas($data);
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

    function Cargar_guias_asignadas_detalle()
    {
        try {
            $json_data = file_get_contents('php://input');
            $data = json_decode($json_data, true);

            if (isset($json_data)) {

                if ($data["TOKEN"] == constant("TOKEN_MOVIL")) {
                    // $param1 = $data['param1'];
                    $funcion =  $this->model->Cargar_guias_asignadas_detalle($data);
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
