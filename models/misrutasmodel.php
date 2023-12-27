<?php


class MisRutasModel extends Model
{


    function Cargar_Mis_Rutas($param)
    {
        try {
            // $USUARIO = $param["USUARIO"];]
            $USUARIO = 7;
            $query = $this->db->connect_dobra()->prepare("SELECT 
            grd.ID,
            date(grd.fecha_creado) as FECHA_RUTA,
            count( grdd.ruta_dia_id) as RUTAS_ASIGNADAS
            from gui_ruta_dia grd 
            left join gui_ruta_dia_detalle grdd 
            on grdd.ruta_dia_id = grd.ID 
            where grd.chofer_id  = :USUARIO
            group by FECHA_RUTA
            order by FECHA_RUTA desc

                ");
            $query->bindParam(":USUARIO", $USUARIO, PDO::PARAM_STR);

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

    function Cargar_Mis_Rutas_Detalle($param)
    {
        try {
            $USUARIO = $param["USUARIO_ID"];
            $RUTA = $param["RUTA"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            grd.ID,
            grd.chofer_id as CHOFER_ID,
            uc.PLACA as CHOFER_PLACA,
            uu.Nombre as CHOFER_NOMBRE,
            date(grd.fecha_creado) as FECHA_RUTA,
            grdd.pedido_interno as GUIA,
            grdd.cliente_id as CLIENTE_ID,
            cc.CLIENTE_NOMBRE,
            ccs.sucursal_nombre as CLIENTE_SUCURSAL,
            grdd.cliente_destino_id as CLIENTE_SUCURSAL_ID,
            grdd.producto_id as PRODUCTO_ID,
            ip.Nombre as PRODUCTO_NOMBRE,
            grdd.holcim as HOLCIM,
            grdd.flete_producto as FLETE_PRODUCTO_ID,
            ip2.Nombre as FLETE_PRODUCTO,
            grdd.flete_cant as FLETE_PRODUCTO_CANT
            from gui_ruta_dia grd 
            left join gui_ruta_dia_detalle grdd 
            on grd.ID = grdd.ruta_dia_id
            left join cli_clientes cc 
            on cc.ID = grdd.cliente_id
            left join cli_clientes_sucursales ccs 
            on ccs.ID = grdd.cliente_destino_id 
            left join inv_productos ip 
            on ip.ID = grdd.producto_id
            left join inv_productos ip2
            on ip2.ID = grdd.flete_producto
            left join us_choferes uc 
            on uc.ID = grd.chofer_id 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            where grd.chofer_id  = :USUARIO
            and grd.ID = :RUTA
                ");
            $query->bindParam(":USUARIO", $USUARIO, PDO::PARAM_STR);
            $query->bindParam(":RUTA", $RUTA, PDO::PARAM_STR);

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
