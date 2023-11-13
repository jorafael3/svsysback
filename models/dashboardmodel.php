<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');

class dashboardmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Cargar_Stats($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT
            (
                select  DESCRIPCION  from guias_detalle gd where CODIGO = '10016416'
                limit 1
            ) as DESCRIPCION,
            (
                select  UNIDAD  from guias_detalle gd where CODIGO = '10016416'
                limit 1
            ) as UNIDAD,
            (
                select sum(ggdd.CANTIDAD_PARCIAL) + sum(ggdd.CANTIDAD_TOTAL) 
                from gui_guias_despachadas_dt ggdd
                left join gui_guias_despachadas_estado ggde 
                on ggdd.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
                where ggdd.CODIGO = '10016416'
                and date(ggde.FECHA_CREADO) between 
                DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL -1 MONTH), '%Y-%m-01') AND LAST_DAY(DATE_ADD(CURDATE(), INTERVAL -1 MONTH))
            ) as CANTIDAD_CEMENTO_MES_ANTERIOR,
            sum(ggdd.CANTIDAD_PARCIAL) + sum(ggdd.CANTIDAD_TOTAL) as CANTIDAD_CEMENTO_MES_ACTUAL
            from gui_guias_despachadas_dt ggdd 
            left join gui_guias_despachadas_estado ggde 
            on ggdd.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
            where ggdd.CODIGO = '10016416'
            and date(ggde.FECHA_CREADO) between :inicio_mes and :fin_mes ");
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $mes = $this->Cargar_Stats_grafico($param);
                echo json_encode([$result,$mes]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Stats_grafico($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare("SELECT
            t1.CODIGO,
            CONCAT(YEAR(t2.FECHA_COMPLETADO), '-', LPAD(MONTH(t2.FECHA_COMPLETADO), 2, '0')) AS AnioMes,
            SUM(t1.CANTIDAD_TOTAL) AS CantidadTotalDespachada
                FROM
                    gui_guias_despachadas_dt t1
                left JOIN
                    gui_guias_despachadas_estado t2 ON t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                WHERE
                    t2.ESTADO_DESPACHO = 0
                    and t1.CODIGO = '10016416'
                GROUP BY
            t1.CODIGO,
            YEAR(t2.FECHA_COMPLETADO),
            MONTH(t2.FECHA_COMPLETADO);
           ");
            // $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return $err;

            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }
}
