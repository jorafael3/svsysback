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


    //********** GUIARDAR GUIAS */

    function Guardar_Guias_despacho($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $CLIENTE_ENTREGA_ID = $param["CLIENTE_ENTREGA_ID"];
            $SERVICIO_ID = $param["SERVICIO"];
            $DESTINO_ID = $param["ENTREGA"];
            $CREADO_POR = $param["USUARIO_ID"];
            $ESTADO = "1";
            $ESTADO_DESPACHO = $param["PARCIAL"] == 0 ? 0 : 1;
            $PARCIAL = $param["PARCIAL"] == 0 ? 0 : 1;
            $PLACA_CAMBIADA = $param["PLACA_CAMBIADA"];
            $PLACA_CAMBIADA_NUMERO = $param["PLACA_CAMBIADA_NUMERO"];
            $query = $this->db->connect_dobra()->prepare('CALL svsys.GUI_GUIAS_DESPACHADAS_INSERT (
                ?,?,?,?,?
                ?,?,?,?,?
            )');
            $query->bindParam(1, $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(2, $CLIENTE_ENTREGA_ID, PDO::PARAM_STR);
            $query->bindParam(3, $SERVICIO_ID, PDO::PARAM_STR);
            $query->bindParam(4, $DESTINO_ID, PDO::PARAM_STR);
            $query->bindParam(5, $CREADO_POR, PDO::PARAM_STR);
            $query->bindParam(6, $ESTADO, PDO::PARAM_STR);
            $query->bindParam(7, $ESTADO_DESPACHO, PDO::PARAM_STR);
            $query->bindParam(8, $PARCIAL, PDO::PARAM_STR);
            $query->bindParam(9, $PLACA_CAMBIADA, PDO::PARAM_STR);
            $query->bindParam(10, $PLACA_CAMBIADA_NUMERO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "DATOS GUARDARDOS"]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
