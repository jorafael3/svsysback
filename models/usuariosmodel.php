<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');
use LDAP\Result;

class Usuariosmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Consultar_Cliente($param1)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from usuarios
                ');
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
