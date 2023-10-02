<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');

class DespachoModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //****** SERVICIOS */

    function Cargar_Gui_Servicios($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from svsys.GUI_SERVICIOS
                where estado = 1');
            // $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([$result, 1]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    
    //****** DESTINOS */

    function Cargar_Gui_Destinos($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from svsys.GUI_DESTINOS
                where estado = 1');
            // $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([$result, 1]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //********* GUIAS */

    function Cargar_Guia($param)
    {
        try {
            $PEDIDO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT * from guias
                where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $DET = $this->Cargar_Guia_detalle($param);
                echo json_encode([$result, $DET, 1]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }


    function Cargar_Guia_detalle($param)
    {
        try {
            $PEDIDO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT * from guias_detalle
                where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
