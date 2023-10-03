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

            $this->db->connect_dobra()->beginTransaction();

            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $CLIENTE_ENTREGA_ID = $param["CLIENTE_ENTREGA_ID"];
            $SERVICIO_ID = $param["SERVICIO_ID"];
            $DESTINO_ID = $param["DESTINO_ID"];
            $CREADO_POR = $param["CREADO_POR"];
            $PARCIAL = $param["PARCIAL"] == 0 ? 0 : 1;
            $PLACA_CAMBIADA = $param["PLACA_CAMBIADA"];
            $PLACA_CAMBIADA_NUMERO = $param["PLACA_CAMBIADA_NUMERO"];
            $despacho_ID =  date('YmdHis');
            $DETALLE = $param["DETALLE"];


            $VAL_ESTADO = $this->Validar_Estado($PEDIDO_INTERNO);
            if ($VAL_ESTADO == 1) {
                $INSERT_ESTADO = $this->Insert_Estado($param);
                $query = $this->db->connect_dobra()->prepare('INSERT INTO svsys.gui_guias_despachadas 
                (
                    PEDIDO_INTERNO,
                    CLIENTE_ENTREGA_ID,
                    SERVICIO_ID, 
                    DESTINO_ID,
                    CREADO_POR, 
                    PARCIAL, 
                    PLACA_CAMBIADA, 
                    PLACA_CAMBIADA_NUMERO,
                    despacho_ID
                ) VALUES(
                    :PEDIDO_INTERNO,
                    :CLIENTE_ENTREGA_ID,
                    :SERVICIO_ID, 
                    :DESTINO_ID,
                    :CREADO_POR, 
                    :PARCIAL, 
                    :PLACA_CAMBIADA, 
                    :PLACA_CAMBIADA_NUMERO,
                    :despacho_ID
                    
                    );
                ');
                $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_ENTREGA_ID", $CLIENTE_ENTREGA_ID, PDO::PARAM_STR);
                $query->bindParam(":SERVICIO_ID", $SERVICIO_ID, PDO::PARAM_STR);
                $query->bindParam(":DESTINO_ID", $DESTINO_ID, PDO::PARAM_STR);
                $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);
                $query->bindParam(":PARCIAL", $PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":PLACA_CAMBIADA", $PLACA_CAMBIADA, PDO::PARAM_STR);
                $query->bindParam(":PLACA_CAMBIADA_NUMERO", $PLACA_CAMBIADA_NUMERO, PDO::PARAM_STR);
                $query->bindParam(":despacho_ID", $despacho_ID, PDO::PARAM_STR);
                $mensaje = 0;
                if ($query->execute()) {
                    $CAB = array("GUARDADO" => 1, "MENSAJE" => "CABECERA GUARDADA");
                    $DET = $this->Guardar_Guias_despacho_dt($DETALLE, $despacho_ID, $PEDIDO_INTERNO);
                    if ($DET["GUARDADO"] == 1) {
                        $this->db->connect_dobra()->commit();
                    } else {
                        $this->db->connect_dobra()->rollback();
                    }
                    $mensaje = [$CAB, $DET, $INSERT_ESTADO, $VAL_ESTADO];
                } else {
                    $err = $query->errorInfo();
                    $CAB = array("GUARDADO" => 0, "MENSAJE" => $err);
                    $mensaje = [$CAB, 0, $INSERT_ESTADO];
                    $this->db->connect_dobra()->rollback();
                }
            } else {
                $INSERT_ESTADO = array("GUARDADO" => 2, "MENSAJE" => "PEDIDO YA GUARDADO");
                $mensaje = [$INSERT_ESTADO];
            }


            // $query = $this->db->connect_dobra()->prepare('CALL svsys.GUI_GUIAS_DESPACHO_INSERT (
            //     ?,?,?,?,?
            //     ?,?,?,?,?
            // )');


            echo json_encode($mensaje);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    function Validar_Estado($NUMERO_PEDIDO)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT PEDIDO_INTERNO
            from GUI_GUIAS_DESPACHADAS_ESTADO
            where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $NUMERO_PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) == 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $err = $query->errorInfo();
                // $this->db->connect_dobra()->rollback();
                return -1;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            // $this->db->connect_dobra()->rollback();
            return -1;
        }
    }

    function Insert_Estado($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $PARCIAL = $param["PARCIAL"] == 0 ? 0 : 1;
            $ESTATO_DESPACHO_TEXTO = $param["PARCIAL"] == 0 ? "TOTAL" : "PARCIAL";
            $CREADO_POR = $param["CREADO_POR"];

            $query = $this->db->connect_dobra()->prepare('INSERT INTO svsys.gui_guias_despachadas_estado
             (
                PEDIDO_INTERNO, 
                ESTADO_DESPACHO, 
                ESTATO_DESPACHO_TEXTO, 
                CREADO_POR
                ) VALUES(
                    :PEDIDO_INTERNO, 
                    :ESTADO_DESPACHO, 
                    :ESTATO_DESPACHO_TEXTO, 
                    :CREADO_POR
                )
            ');
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(":ESTADO_DESPACHO", $PARCIAL, PDO::PARAM_STR);
            $query->bindParam(":ESTATO_DESPACHO_TEXTO", $ESTATO_DESPACHO_TEXTO, PDO::PARAM_STR);
            $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);
            if ($query->execute()) {
                return array("GUARDADO" => 1, "MENSAJE" => "ESTADO GUARDADO");
            } else {
                $err = $query->errorInfo();
                // $this->db->connect_dobra()->rollback();
                return array("GUARDADO" => 0, "MENSAJE" => $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            // $this->db->connect_dobra()->rollback();
            return array("GUARDADO" => 0, "MENSAJE" => $e);
        }
    }

    function Guardar_Guias_despacho_dt($DETALLE, $DESPACHO_ID, $PEDIDO_INTERNO)
    {
        try {

            $val = 0;
            $err = 0;
            for ($i = 0; $i < count($DETALLE); $i++) {
                $PEDIDO_INTERNO  = $PEDIDO_INTERNO;
                $CODIGO = $DETALLE[$i]["CODIGO"];
                $PARCIAL = $DETALLE[$i]["PARCIAL"];
                $CANTIDAD_PARCIAL = $PARCIAL == 1 ? $DETALLE[$i]["CANT_PARCIAL"] : 0;
                $CANTIDAD_TOTAL = $PARCIAL == 1 ? 0 : $DETALLE[$i]["POR_DESPACHAR"];
                $Despacho_ID = $DESPACHO_ID;

                $query = $this->db->connect_dobra()->prepare('INSERT INTO svsys.gui_guias_despachadas_dt
                (
                   PEDIDO_INTERNO, 
                   CODIGO, 
                   PARCIAL, 
                   CANTIDAD_PARCIAL,
                   CANTIDAD_TOTAL,
                   Despacho_ID
               ) VALUES(
                   :PEDIDO_INTERNO, 
                   :CODIGO, 
                   :PARCIAL, 
                   :CANTIDAD_PARCIAL,
                   :CANTIDAD_TOTAL,
                   :Despacho_ID
               );
            ');
                $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
                $query->bindParam(":CODIGO", $CODIGO, PDO::PARAM_STR);
                $query->bindParam(":PARCIAL", $PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":CANTIDAD_PARCIAL", $CANTIDAD_PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":CANTIDAD_TOTAL", $CANTIDAD_TOTAL, PDO::PARAM_STR);
                $query->bindParam(":Despacho_ID", $Despacho_ID, PDO::PARAM_STR);
                if ($query->execute()) {
                    $val++;
                } else {
                    $err = $query->errorInfo();
                }
            }
            if ($val == count($DETALLE)) {
                return array("GUARDADO" => 1, "MENSAJE" => "DETALLE GUARDADO");
            } else {
                return array("GUARDADO" => 0, "MENSAJE" =>  $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
