<?php
//error_reporting(0);
class Database{
    private $host;
    private $db;
    private $user;
    private $password;
    private $charset;
    private $pdo;

    public function __construct(){
        
        $this->host = constant('HOST');
        $this->db = constant('DB');
        $this->user = constant('USER');
        $this->password = constant('PASSWORD');
        $this->charset = constant('CHARSET');
    }

    function connect_dobra(){
        try{
            //$connection = "sqlsrv:host=".$this->host.";dbname=".$this->db.";charset=".$this->charset;
            if($this->pdo){
                return $this->pdo;
            }else{

                try{
                    $this->pdo = new PDO("sqlsrv:Server=".$this->host.";Database=".$this->db."",$this->user,$this->password); 
                    $options =[
                        PDO::ATTR_ERRMODE       =>PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES  =>true,
                        PDO::ATTR_AUTOCOMMIT => true
                    ];
                    return $this->pdo;
                }catch(PDOException $e){
                    include_once 'views/errores/500.php';
                }
            }
            
            //$pdo = new PDO($connection,$this->user,$this->password,$options);
            //return $pdo;
          
        }catch(PDOException $e){
            //print_r('Error de conexion: '.$e->getMessage());
            print_r('Error de conexion: ');

        }
    }

    function connect_dobra_Computron(){
        try{
            //$connection = "sqlsrv:host=".$this->host.";dbname=".$this->db.";charset=".$this->charset;
            if($this->pdo){
                return $this->pdo;
            }else{

                try{
                    $this->pdo = new PDO("sqlsrv:Server=10.5.1.86;Database=COMPUTRONSA",$this->user,$this->password); 
                    $options =[
                        PDO::ATTR_ERRMODE       =>PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES  =>true,
                        PDO::ATTR_AUTOCOMMIT => true
                    ];
                    return $this->pdo;
                }catch(PDOException $e){
                    include_once 'views/errores/500.php';
                }
            }
            
            //$pdo = new PDO($connection,$this->user,$this->password,$options);
            //return $pdo;
          
        }catch(PDOException $e){
            //print_r('Error de conexion: '.$e->getMessage());
            print_r('Error de conexion: ');

        }
    }

}
