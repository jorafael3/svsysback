<?php


class RutasModel extends Model
{
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
            on grdd.ruta_dia_id = gr.ID 
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
            grd.chofer_id,
            uu.Nombre as Chofer_nombre,
            uc.PLACA,
            concat(uc.PLACA,' ',uu.Nombre) as CHOFER,
            cc.CLIENTE_NOMBRE,
            grdd.cliente_id,
            grdd.producto_id,
            grdd.producto_id as producto_nombre,
            grdd.pedido_interno,
            grdd.destino_id,
            grdd.destino_id as destino_nombre,
            g.FACTURA,
            ggp.FECHA_SALE_PLANTA 
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
            where gr.ID = :ID
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
            $query = $this->db->connect_dobra()->prepare('SELECT date(fecha_ruta)
            FROM gui_rutas
            where date(fecha_ruta) = :fecha');
            $query->bindParam(":fecha", $Fecha_Hoy, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    echo json_encode([0, "Ruta ya creada"]);
                    exit();
                } else {
                    $query2 = $this->db->connect_dobra()->prepare('INSERT into
                        gui_rutas (creado_por)values(:creado)');
                    $query2->bindParam(":creado", $CREADO_POR, PDO::PARAM_STR);
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
}
