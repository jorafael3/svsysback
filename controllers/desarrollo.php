<?php
header("Access-Control-Allow-Origin: *");
// Permitir los métodos HTTP que deseas utilizar (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados personalizados, si es necesario
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class Desarrollo extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function scrapy_guias()
    {
        $param1 = $_POST['param'];
        // $Ventas =  $this->model->scrapy_guias($param1);
        try {
            // ob_start();
            // passthru('python3 C:\xampp\htdocs\svsysback\scrapy\disensa.p');
            // $output = ob_get_clean();
            // $message = exec("python C:/xampp/htdocs/svsysback/scrapy/disensa.py");
            $message = exec("python C:/xampp/htdocs/svsysback/scrapy/disensa.py");
            // Lee el contenido del archivo en una cadena
            echo json_encode($message);
            exit();

        } catch (Exception $e) {
            echo json_encode($e->getMessage());
            exit();
        }
    }

    function scrapy_guias_insert()
    {
        $param1 = $_POST['param'];
        // $Ventas =  $this->model->scrapy_guias($param1);
        try {
            $message = exec("python C:/xampp/htdocs/svsysback/scrapy/scrapy_ingresar_datos.py");
            // Lee el contenido del archivo en una cadena
            echo json_encode($message);
            exit();

        } catch (Exception $e) {
            echo json_encode($e->getMessage());
            exit();
        }
    }
  
    function scrapy_guias_log()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {


            $archivo = "C:/xampp/htdocs/svsysback/scrapy/datos.txt";
            // $message = exec("python C:/xampp/htdocs/svsysback/scrapy/correo.py");
            // Lee el contenido del archivo en una cadena
            $contenido = file_get_contents($archivo);
            echo json_encode($contenido);
            exit();
        } else {
            die();
        }
    }

    function correos()
    {
        $param = $_POST['param'];
        if ($param["TOKEN"] == constant("TOKEN_WEB")) {


            $archivo = "C:/xampp/htdocs/svsysback/scrapy/datos_correos.txt";
            $message = exec("python C:/xampp/htdocs/svsysback/scrapy/correo.py");
            // Lee el contenido del archivo en una cadena
            $contenido = file_get_contents($archivo);
            echo json_encode($contenido);
            exit();
        } else {
            die();
        }
    }
}
