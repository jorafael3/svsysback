<?php


class RutasModel extends Model{


    function Cargar_Rutas()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT 
            date(gr.fecha_ruta) as fecha,
            count(distinct grd.chofer_id) as cant_choferes, 
            count(distinct grdd.cliente_id) as cant_clientes
            from gui_rutas gr
            left join gui_ruta_dia grd
            on gr.ID  = grd.ruta_id
            left join gui_ruta_dia_detalle grdd 
            on grdd.ruta_dia_id = gr.ID 
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

    function Cargar_Rutas_dia()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT 
            grd.chofer_id,
            uu.Nombre as Chofer_nombre,
            uc.PLACA,
            cc.CLIENTE_NOMBRE,
            grdd.cliente_destino_id,
            grdd.producto_id,
            grdd.pedido_interno,
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