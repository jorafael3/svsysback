<?php


class ReportesModel extends Model
{

    //******************************************************** */
    //***************  CLIENTES   **************************** */


    function Reporte_Clientes_General()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT
            cc.ID as CLIENTE_ID,
            cc.CLIENTE_NOMBRE,
            (select count(*) from gui_guias_despachadas ggd2 where ggd2.CLIENTE_ENTREGA_ID = cc.ID  ) as CANTIDAD_GUIAS_ENTREGADAS,
            sum(ggf.factura_total) as FACTURADO,
            count(ggf.factura_total) as FACTURADO_CANTIDAD
            from 
            cli_clientes cc 
            left join gui_guias_despachadas ggd 
            on ggd.CLIENTE_ENTREGA_ID = cc.ID
            left join gui_guias_facturas ggf 
            on ggf.pedido_interno = ggd.PEDIDO_INTERNO and ggf.CLIENTE_ID = ggd.CLIENTE_ENTREGA_ID 
            group by 
            cc.ID 
            ');
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $ser = $this->Reporte_Clientes_General_servicios();
                echo json_encode([$result, $ser]);
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

    function Reporte_Clientes_General_servicios()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT
            CLIENTE_ENTREGA_ID,
            SUM(CASE WHEN ID = 1 THEN 1 ELSE 0 END) AS CARGAS,
            SUM(CASE WHEN ID = 2 THEN 1 ELSE 0 END) AS FLETES
            FROM
                (SELECT ggd.CLIENTE_ENTREGA_ID, gs.ID
                FROM gui_servicios gs
                LEFT JOIN gui_guias_despachadas ggd ON ggd.SERVICIO_ID = gs.ID) AS subquery
            GROUP BY CLIENTE_ENTREGA_ID;
            ');
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
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

    //******************************************************** */
    //***************  CHOFERES   **************************** */

    function Reporte_Chofer_General($param)
    {
        try {

            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];

            $query = $this->db->connect_dobra()->prepare('SELECT 
            uc.usuario_id  as CHOFER_ID, 
            uu.Nombre as CHOFER_NOMBRE,
            uc.PLACA,
            (select count(*) from gui_guias_despachadas ggd where ggd.CREADO_POR = uu.usuario_id and ggd.PARCIAL  = 0) as GUIAS_COMPLETAS,
            (select count(*) from gui_guias_despachadas ggd where ggd.CREADO_POR = uu.usuario_id and ggd.PARCIAL  = 1) as GUIAS_PARCIALES,
            (select count(*) from gui_guias_despachadas_estado ggd where ggd.CREADO_POR = uu.usuario_id) as GUIAS_TOTALES,
            ifnull( (
                select
                AVG(TIMESTAMPDIFF(SECOND, FECHA_CREADO, FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                FROM gui_guias_despachadas_estado ggde 
                WHERE ggde.FECHA_COMPLETADO IS NOT null and CREADO_POR = uc.usuario_id 
                GROUP BY CREADO_POR
            ),null) as PROMEDIO_DEMORA_HORAS_TOTAL,
            ifnull( (
                select
                AVG(TIMESTAMPDIFF(SECOND, FECHA_CREADO, FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                FROM gui_guias_despachadas_estado ggde 
                WHERE ggde.FECHA_COMPLETADO IS NOT null and CREADO_POR = uc.usuario_id
                and date(FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                GROUP BY CREADO_POR
            ),null) as PROMEDIO_DEMORA_HORAS_TOTAL_MES,
            (
                select count(*)  from gui_guias_placa ggp 
                left join guias g 
                on g.PEDIDO_INTERNO = ggp.pedido_interno 
                where STR_TO_DATE(g.FECHA_DE_EMISION  , "%d.%m.%Y") between :FECHA_INI and :FECHA_FIN
                and ggp.placa = uc.PLACA
            ) as GUIAS_ASIGNADAS_PERIODO,
            (
                select count(*)  from gui_guias_placa ggp 
                left join guias g 
                on g.PEDIDO_INTERNO = ggp.pedido_interno 
                where ggp.placa = uc.PLACA
            ) as GUIAS_ASIGNADAS_TOTAL
            from us_choferes uc 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            left join gui_guias_despachadas_estado ggde2 
            on ggde2.CREADO_POR = uu.Usuario_ID 
            where date(ggde2.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
            group by 
            uc.usuario_id 
            ');
            $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                // $tiempo = $this->Reporte_Chofer_Tiempos();
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

    function Reporte_Chofer_Tiempos()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT
            ggde.CREADO_POR ,
            AVG(TIMESTAMPDIFF(SECOND, FECHA_CREADO, FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
            FROM gui_guias_despachadas_estado ggde 
            WHERE ggde.FECHA_COMPLETADO IS NOT NULL
            GROUP BY CREADO_POR;
            ');
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
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
}
