<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');
use LDAP\Result;

class principalmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Cargar_Datos($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from SGO_CARGAS
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

    function Cargar_Datos_Llenos($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare("{CALL SGO_CARGAS_SELECT}");
            // $query->bindParam(":pedido", $pedido, PDO::PARAM_STR);
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

    function Nueva_Carga($param)
    {
        try {
            $PROVEEDOR = $param["PROVEEDOR"];
            $DESCRIPCION = $param["DESCRIPCION"];
            $tipo_carga = $param["tipo_carga"];
            $CREADO = $_SESSION["usuario"];
            if ($tipo_carga == 1) {
                $N_WR_NUMBER = $param["N_WR_NUMBER"];
                $N_PC = $param["N_PC"];
                $N_Weight = $param["N_Weight"];
                $N_Volume = $param["N_Volume"];
                $sql  = 'INSERT INTO SGO_CARGAS(
                    proveedor,
                    descripcion,
                    creado_por,
                    tipo_carga,
                    wr_number,
                    pc,
                    weight,
                    volumen
                )VALUES(
                    :proveedor,
                    :descripcion,
                    :creado_por,
                    :tipo_carga,
                    :wr_number,
                    :pc,
                    :weight,
                    :volumen
                )
                    ';
            } else {
                $sql  = 'INSERT INTO SGO_CARGAS(
                    proveedor,
                    descripcion,
                    creado_por,
                    tipo_carga
                )VALUES(
                    :proveedor,
                    :descripcion,
                    :creado_por,
                    :tipo_carga
                )
                    ';
            }
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":proveedor", $PROVEEDOR, PDO::PARAM_STR);
            $query->bindParam(":descripcion", $DESCRIPCION, PDO::PARAM_STR);
            $query->bindParam(":creado_por", $CREADO, PDO::PARAM_STR);
            $query->bindParam(":tipo_carga", $tipo_carga, PDO::PARAM_STR);
            if ($tipo_carga == 1) {
                $query->bindParam(":wr_number", $N_WR_NUMBER, PDO::PARAM_STR);
                $query->bindParam(":pc", $N_PC, PDO::PARAM_STR);
                $query->bindParam(":weight", $N_Weight, PDO::PARAM_STR);
                $query->bindParam(":volumen", $N_Volume, PDO::PARAM_STR);
            }
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode(true);
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

    function Actualizar_Carga($param)
    {
        try {
            $ID = $param["ID"];
            $ID_LIQUIDACION = $param["ID_LIQUIDACION"];
            $query = $this->db->connect_dobra()->prepare('UPDATE SGO_CARGAS SET
                liquidacion_id = :liquidacion_id
                WHERE ID = :ID');

            $query->bindParam(":ID", $ID, PDO::PARAM_STR);
            $query->bindParam(":liquidacion_id", $ID_LIQUIDACION, PDO::PARAM_STR);
            if ($query->execute()) {
                $d = $this->Actualizar_Carga_dobra($param);
                echo json_encode([true, $d]);
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

    function Actualizar_Carga_dobra($param)
    {
        try {
            $ID_LIQUIDACION = $param["ID_LIQUIDACION"];
            $VL_LIQUIDACION = $param["VL_LIQUIDACION"];
            $VL_FECHA_TRANSFERENCIA = $param["VL_FECHA_TRANSFERENCIA"];
            $VL_FECHA_HABIL = $param["VL_FECHA_HABIL"];
            $VL_FECHA_BODEGA = $param["VL_FECHA_BODEGA"];
            $VL_OBSERVACION = $param["VL_OBSERVACION"];
            $VL_ORDEN = $param["VL_ORDEN"];

            $query = $this->db->connect_dobra()->prepare('UPDATE IMP_LIQUIDACION SET
                liquidacion = :liquidacion,
                fecha_trans = :fecha_trans,
                fecha_habil_cas = :fecha_habil_cas,
                orden = :orden,
                fecha_bodega = :fecha_bodega,
                observacion = :observacion
                WHERE ID = :ID');

            $query->bindParam(":ID", $ID_LIQUIDACION, PDO::PARAM_STR);
            $query->bindParam(":liquidacion", $VL_LIQUIDACION, PDO::PARAM_STR);
            $query->bindParam(":fecha_trans", $VL_FECHA_TRANSFERENCIA, PDO::PARAM_STR);
            $query->bindParam(":fecha_habil_cas", $VL_FECHA_HABIL, PDO::PARAM_STR);
            $query->bindParam(":orden", $VL_ORDEN, PDO::PARAM_STR);
            $query->bindParam(":fecha_bodega", $VL_FECHA_BODEGA, PDO::PARAM_STR);
            $query->bindParam(":observacion", $VL_OBSERVACION, PDO::PARAM_STR);

            if ($query->execute()) {
                return true;
            } else {
                $err = $query->errorInfo();
                return $err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    function Buscar_Importacion($param)
    {
        try {
            $referencia = "%" . $param["factura"] . "%";
            $query = $this->db->connect_dobra()->prepare('SELECT
            Valor,PuertoEmbarque,FechaEmbarque,FechaLlegada,TipoImport, * 
            from IMP_PEDIDOS
            where Referencia like :referencia');
            $query->bindParam(":referencia", $referencia, PDO::PARAM_STR);
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

    function Buscar_Liquidacion($param)
    {
        try {
            $liquidacion = $param["liquidacion"];
            // $query = $this->db->connect_dobra()->prepare('SELECT
            //  DUI,Detalle, * from IMP_LIQUIDACION
            // where DUI like :liquidacion
            //     ');



            $query = $this->db->connect_dobra()->prepare('SELECT 
            li.ID,
            li.Detalle,
            li.Valor,
            li.Puerto,
            li.Embarque,
            li.EditadoDate AS fecha_arribo,
            li.DUI,
            li.Tipo,
            li.Fecha as fecha_factura,
            li.Transporte,
            li.CreadoPor,
            li.CreadoDate,
            li.Estado,
            li.agente_carga,
            li.tipo_carga,
            li.liquidacion,
            li.fecha_trans,
            li.fecha_habil_cas,
            li.orden,
            li.observacion,
            li.fecha_bodega
            from IMP_LIQUIDACION li
            where ID = :ID ');

            $query->bindParam(":ID", $liquidacion, PDO::PARAM_STR);
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
