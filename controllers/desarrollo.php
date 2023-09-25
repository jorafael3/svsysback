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


            // $archivo = fopen('C:\xampp\htdocs\svsysback\scrapy\datos.txt','r');
            $lineas = [];
            // $linea = fgets($archivo);
            $fp = fopen("C:/xampp/htdocs/svsysback/scrapy/datos.txt", "r");
            $lineas = array();
            
            while (($linea = fgets($fp)) !== false) {
                $lineas[] = $linea;
            }
            
            fclose($fp);
            
            echo json_encode($lineas);
            exit();
            // $archivo = file('C:\xampp\htdocs\svsysback\scrapy\datos.txt');
            // print_r($archivo);

        } catch (Exception $e) {
            echo json_encode($e->getMessage());
            exit();
        }
    }
}
