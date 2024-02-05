<?php


class RutasModel extends Model
{
    function Cargar_Chofer()
    {
        try {
            $query = $this->db->connect_dobra()->prepare("SELECT
            uc.usuario_id AS value,
            CONCAT(uc.PLACA, '-', uu.Nombre) AS label
            FROM
                us_choferes uc
            LEFT JOIN
                us_usuarios uu ON uu.Usuario_ID = uc.usuario_id
            where uc.ESTADO = 1
                ");
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

    function Cargar_Clientes()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT 
            ID as value,CLIENTE_NOMBRE as label from cli_clientes
            where estado = 1
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
    function Cargar_Clientes_Sucursales($param)
    {
        try {
            $cliente_id = $param["CLIENTE"];
            $query = $this->db->connect_dobra()->prepare('SELECT 
            ID as value,sucursal_nombre as label from cli_clientes_sucursales
            where estado = 1 and cliente_id = :cliente_id
            ');
            $query->bindParam(":cliente_id", $cliente_id, PDO::PARAM_STR);
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

    function Cargar_Productos()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT 
            ID as value,Nombre as label from inv_productos
            order by Nombre
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

    function Cargar_Guias($param)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];

            $query = $this->db->connect_dobra()->prepare('SELECT 
            g.FECHA_DE_EMISION,
            g.PEDIDO_INTERNO,
            gd.DESCRIPCION,
            gd.POR_DESPACHAR,
            gd.CODIGO,
            ip.ID 
            from guias g
            left join guias_detalle gd 
            on gd.PEDIDO_INTERNO  = g.PEDIDO_INTERNO
            left join inv_productos ip 
            on ip.codigo = gd.CODIGO 
            where STR_TO_DATE(g.FECHA_DE_EMISION, "%d.%m.%Y") between :fecha_ini and :fecha_fin
            and gd.CODIGO = "10016416"
            group by gd.PEDIDO_INTERNO
            order by  STR_TO_DATE(g.FECHA_DE_EMISION, "%d.%m.%Y") desc
            ');
            $query->bindParam(":fecha_ini", $FECHA_INI, PDO::PARAM_STR);
            $query->bindParam(":fecha_fin", $FECHA_FIN, PDO::PARAM_STR);

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

    function Cargar_Rutas()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT 
            date(gr.fecha_ruta) as fecha,
            count(distinct grd.chofer_id) as cant_choferes, 
            count(distinct grdd.cliente_id) as cant_clientes,
            gr.ID
            from gui_rutas gr
            left join gui_ruta_dia grd
            on gr.ID  = grd.ruta_id
            left join gui_ruta_dia_detalle grdd 
            on grdd.ruta_dia_id = grd.ID
            group by fecha
            order by fecha desc
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

    function Cargar_Rutas_dia($param)
    {
        try {
            $ID = $param["ID"];
            $query = $this->db->connect_dobra()->prepare("SELECT
            grd.ID,
            grdd.despachado,
            grdd.despachado_fecha,
            grdd.despachado_por,
            grdd.ID as RUTA_DET_ID,
            grd.chofer_id,
            uu.Nombre as Chofer_nombre,
            uc.PLACA,
            concat(uc.PLACA,' ',UPPER(uu.Nombre),'/',grd.ID) as CHOFER,
            cc.CLIENTE_NOMBRE,
            grdd.cliente_id,
            grdd.producto_id,
            ip.Nombre  as producto_nombre,
            grdd.pedido_interno,
            grdd.cliente_destino_id,
            ccs.sucursal_nombre  as destino_nombre,
            ggp.FECHA_SALE_PLANTA,
            ggp.placa as PLACA_RETIRO,
            grdd.factura,
            grdd.holcim,
            grdd.bodega,
            grdd.flete_cant,
            grdd.flete_producto,
            ip2.Nombre as flete_producto_nombre
            from gui_ruta_dia grd 
            left join us_choferes uc 
            on uc.usuario_id = grd.chofer_id 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id
            left join gui_ruta_dia_detalle grdd 
            on grdd.ruta_dia_id = grd.ID 
            left join cli_clientes cc 
            on cc.ID = grdd.cliente_id
            left join guias g 
            on g.PEDIDO_INTERNO = grdd.pedido_interno
            left join gui_guias_placa ggp 
            on ggp.pedido_interno = grdd.pedido_interno 
            left join gui_rutas gr 
            on gr.ID = grd.ruta_id
            left join inv_productos ip 
            on ip.ID  = grdd.producto_id
            left join inv_productos ip2 
            on ip2.ID  = grdd.flete_producto
            left join cli_clientes_sucursales ccs 
            on ccs.cliente_id = cc.ID and grdd.cliente_destino_id = ccs.ID 
            where gr.ID = :ID
            order by grd.fecha_creado desc
            ");
            $query->bindParam(":ID", $ID, PDO::PARAM_STR);

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

    function Nueva_Ruta($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $Fecha_Hoy = date("Y-m-d");
            $CREADO_POR = $param["CREADO_POR"];
            $FECHA_NUEVA_RUTA = $param["FECHA_NUEVA_RUTA"];
            $query = $this->db->connect_dobra()->prepare('SELECT date(fecha_ruta)
            FROM gui_rutas
            where date(fecha_ruta) = :fecha');
            $query->bindParam(":fecha", $FECHA_NUEVA_RUTA, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    echo json_encode([0, "La ruta de esta fecha ya esta creada"]);
                    exit();
                } else {
                    $query2 = $this->db->connect_dobra()->prepare('INSERT into
                        gui_rutas (creado_por,fecha_ruta)values(:creado,:fecha_ruta)');
                    $query2->bindParam(":creado", $CREADO_POR, PDO::PARAM_STR);
                    $query2->bindParam(":fecha_ruta", $FECHA_NUEVA_RUTA, PDO::PARAM_STR);
                    if ($query2->execute()) {
                        echo json_encode([1, "Ruta Creada"]);
                        exit();
                    } else {
                        echo json_encode([0, "Error al Guardar"]);
                        exit();
                    }
                }
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

    function Nueva_Ruta_Dia($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $Fecha_Hoy = date("Y-m-d");
            $CHOFER = $param["CHOFER"];
            $RUTA_ID = $param["RUTA_ID"];
            $query = $this->db->connect_dobra()->prepare('SELECT chofer_id
            FROM gui_ruta_dia
            where ruta_id = :ruta_id and chofer_id = :chofer_id');
            $query->bindParam(":ruta_id", $RUTA_ID, PDO::PARAM_STR);
            $query->bindParam(":chofer_id", $CHOFER, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    echo json_encode([0, "Chofer ya tiene ruta creada"]);
                    exit();
                } else {
                    $query2 = $this->db->connect_dobra()->prepare('INSERT into
                        gui_ruta_dia (ruta_id,chofer_id)values(:ruta_id,:chofer_id)');
                    $query2->bindParam(":ruta_id", $RUTA_ID, PDO::PARAM_STR);
                    $query2->bindParam(":chofer_id", $CHOFER, PDO::PARAM_STR);
                    if ($query2->execute()) {
                        echo json_encode([1, "Chofer Agregado"]);
                        exit();
                    } else {
                        echo json_encode([0, "Error al Guardar"]);
                        exit();
                    }
                }
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

    function Nueva_Ruta_Dia_detalle($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $Fecha_Hoy = date("Y-m-d");
            $ruta_dia_id = $param["RUTA_DIA_ID"];
            $cliente_id = $param["CLIENTE"];
            $cliente_destino_id = $param["CLIENTE_DESTINO"];
            $producto_id = $param["PRODUCTO"];
            $factura = $param["FACTURA"];
            $holcim = $param["HOLCIM"];
            $bodega = $param["BODEGA"];
            $flete_cant = $param["FLETE_CANT"];
            $flete_producto = $param["FLETE_PROD"];
            $pedido_interno = $param["GUIA"];
            $query = $this->db->connect_dobra()->prepare('INSERT INTO gui_ruta_dia_detalle 
            (
                ruta_dia_id, 
                cliente_id, 
                cliente_destino_id, 
                producto_id, 
                factura, 
                holcim, 
                bodega, 
                flete_cant, 
                flete_producto,
                pedido_interno

            )VALUES(
                :ruta_dia_id, 
                :cliente_id, 
                :cliente_destino_id, 
                :producto_id, 
                :factura, 
                :holcim, 
                :bodega, 
                :flete_cant, 
                :flete_producto,
                :pedido_interno
            );
            ');
            $query->bindParam(":ruta_dia_id", $ruta_dia_id, PDO::PARAM_STR);
            $query->bindParam(":cliente_id", $cliente_id, PDO::PARAM_STR);
            $query->bindParam(":cliente_destino_id", $cliente_destino_id, PDO::PARAM_STR);
            $query->bindParam(":producto_id", $producto_id, PDO::PARAM_STR);
            $query->bindParam(":factura", $factura, PDO::PARAM_STR);
            $query->bindParam(":holcim", $holcim, PDO::PARAM_STR);
            $query->bindParam(":bodega", $bodega, PDO::PARAM_STR);
            $query->bindParam(":flete_cant", $flete_cant, PDO::PARAM_STR);
            $query->bindParam(":flete_producto", $flete_producto, PDO::PARAM_STR);
            $query->bindParam(":pedido_interno", $pedido_interno, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "Datos Agregados"]);
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

    function Actualizar_Ruta_Dia_detalle($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $Fecha_Hoy = date("Y-m-d");
            $RUTA_DIA_ID = $param["RUTA_DIA_ID"];
            $cliente_id = $param["CLIENTE"];
            $cliente_destino_id = $param["CLIENTE_DESTINO"];
            $producto_id = $param["PRODUCTO"];
            $factura = $param["FACTURA"];
            $holcim = $param["HOLCIM"];
            $bodega = $param["BODEGA"];
            $flete_cant = $param["FLETE_CANT"];
            $flete_producto = $param["FLETE_PROD"];
            $pedido_interno = $param["GUIA"];
            $query = $this->db->connect_dobra()->prepare('UPDATE gui_ruta_dia_detalle 
            SET 
                cliente_id=:cliente_id, 
                cliente_destino_id=:cliente_destino_id, 
                producto_id=:producto_id, 
                factura=:factura, 
                holcim=:holcim, 
                bodega=:bodega, 
                flete_cant=:flete_cant, 
                flete_producto=:flete_producto ,
                pedido_interno=:pedido_interno
            WHERE ID=:ID
            ');
            $query->bindParam(":cliente_id", $cliente_id, PDO::PARAM_STR);
            $query->bindParam(":cliente_destino_id", $cliente_destino_id, PDO::PARAM_STR);
            $query->bindParam(":producto_id", $producto_id, PDO::PARAM_STR);
            $query->bindParam(":factura", $factura, PDO::PARAM_STR);
            $query->bindParam(":holcim", $holcim, PDO::PARAM_STR);
            $query->bindParam(":bodega", $bodega, PDO::PARAM_STR);
            $query->bindParam(":flete_cant", $flete_cant, PDO::PARAM_STR);
            $query->bindParam(":flete_producto", $flete_producto, PDO::PARAM_STR);
            $query->bindParam(":pedido_interno", $pedido_interno, PDO::PARAM_STR);
            $query->bindParam(":ID", $RUTA_DIA_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "Datos Actualizados"]);
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

    function Eliminar_Ruta_Dia_detalle($param)
    {
        try {
            $RUTA_DIA_ID = $param["RUTA_DIA_ID"];
            $query = $this->db->connect_dobra()->prepare('DELETE FROM gui_ruta_dia_detalle 
            WHERE ID=:ID
            ');
            $query->bindParam(":ID", $RUTA_DIA_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "Datos Eliminados"]);
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
