<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');

class dashboardmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Cargar_Productos()
    {
        try {
            // $inicio_mes = $param["inicio_mes"];
            // $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT distinct  
            CODIGO,DESCRIPCION  from guias_detalle gd 
            order by DESCRIPCION 
           ");
            // $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            // $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Stats($param)
    {
        try {
            $SACOS = $this->Cantidad_Sacos_Mes($param);
            $CHOFER = $this->Chofer_Mas_Despachos($param);
            $GUIAS_DESPACHADAS = array(
                "POR_DIA" => $this->GUIAS_DESPACHADAS_POR_DIA($param),
                "POR_DIA_MES_ANT" => $this->GUIAS_DESPACHADAS_POR_DIA_MES_ANTERIOR($param),
                "POR_MES" => $this->GUIAS_DESPACHADAS_POR_MES($param),
                "POR_ANIO" => $this->GUIAS_DESPACHADAS_POR_ANIO($param),
            );
            $A = array(
                "SACOS" => $SACOS,
                "CHOFER" => $CHOFER,
                "GUIAS_DESPACHADAS" => $GUIAS_DESPACHADAS,

            );
            echo json_encode($A);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }



    function Cantidad_Sacos_Mes($param)
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
                $mes = $this->Cantidad_Sacos_Mes_Grafico($param);
                return array("DATOS" => $result, "GRAFICO" => $mes);
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

    function Cantidad_Sacos_Mes_Grafico($param)
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

    function Chofer_Mas_Despachos($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            uc.usuario_id,
            uu.Nombre,
            ifnull((
            select sum(ggdd.CANTIDAD_PARCIAL) + sum(ggdd.CANTIDAD_TOTAL) as CANT_CEMENTO
            from gui_guias_despachadas_dt ggdd 
            left join gui_guias_despachadas_estado ggde 
            on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO 
            where ggde.CREADO_POR = uu.Usuario_ID  and ggdd.CODIGO ='10016416'
            and date(ggde.FECHA_CREADO) between :inicio_mes and :fin_mes 
            ),0)as CANT_CEMENTO
            from us_choferes uc 
            left join us_usuarios uu
            on uc.usuario_id = uu.Usuario_ID 
            order by CANT_CEMENTO desc
           ");
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $mes = $this->Chofer_Mas_Despachos_Grafico($result[0]["usuario_id"]);
                return array("DATOS" => $result, "GRAFICO" =>  $mes);
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

    function Chofer_Mas_Despachos_Grafico($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare("SELECT
            t1.CODIGO,
            t2.CREADO_POR,
            concat(year(t2.FECHA_COMPLETADO),'-',lpad(month(t2.FECHA_COMPLETADO),2,'0')) as MES,
            sum(t1.CANTIDAD_PARCIAL) + sum(t1.CANTIDAD_TOTAL) as CANTIDAD
            from gui_guias_despachadas_dt t1 
            join gui_guias_despachadas_estado t2 
            on t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
            where t1.CODIGO = '10016416'
            and t2.CREADO_POR = :usuario
            group by MES
           ");
            $query->bindParam(":usuario", $param, PDO::PARAM_STR);
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

    function GUIAS_DESPACHADAS_POR_DIA($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            date(ggde.FECHA_COMPLETADO) as FECHA,
            ggdd.CODIGO,
            '1' as TIPO,
            SUM(ggdd.CANTIDAD_PARCIAL) + SUM(ggdd.CANTIDAD_TOTAL) as cantidad
            from gui_guias_despachadas_estado ggde
            left join gui_guias_despachadas_dt ggdd 
            on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO
            where date(ggde.FECHA_COMPLETADO) between :inicio_mes and :fin_mes
            and CODIGO = '10016416'
            group by FECHA 
           ");
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return array("DATOS" => $result);
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

    function GUIAS_DESPACHADAS_POR_DIA_MES_ANTERIOR($param)
    {
        try {
            $inicio_mes = $param["inicio_mes_a"];
            $fin_mes = $param["fin_mes_a"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            date(ggde.FECHA_COMPLETADO) as FECHA_ANT,
            ggdd.CODIGO,
            '2' as TIPO,
            SUM(ggdd.CANTIDAD_PARCIAL) + SUM(ggdd.CANTIDAD_TOTAL) as cantidad
            from gui_guias_despachadas_estado ggde
            left join gui_guias_despachadas_dt ggdd 
            on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO
            where date(ggde.FECHA_COMPLETADO) between :inicio_mes and :fin_mes
            and CODIGO = '10016416'
            group by FECHA_ANT 
           ");
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return array("DATOS" => $result);
            } else {
                $err = $query->errorInfo();
                return array("DATOS" => $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function GUIAS_DESPACHADAS_POR_MES($param)
    {
        try {
            // $inicio_mes = $param["inicio_mes"];
            // $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT
            gd.CODIGO,
            concat(year(STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y')),'-',lpad(month(STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y')),2,'0')) as FECHA,
            sum(gd.POR_DESPACHAR) AS cantidad
            from guias_detalle gd 
            join guias g 
            on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO
            where gd.CODIGO = '10016416'
            group by FECHA
           ");
            // $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            // $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return array("DATOS" => $result);
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

    function GUIAS_DESPACHADAS_POR_ANIO($param)
    {
        try {
            // $inicio_mes = $param["inicio_mes"];
            // $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT
            gd.CODIGO,
            gd.PEDIDO_INTERNO,
            concat(year(STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y'))) as FECHA,
            sum(gd.POR_DESPACHAR) as cantidad
            from guias_detalle gd 
            join guias g 
            on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO
            where gd.CODIGO = '10016416'
            group by FECHA
           ");
            // $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            // $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return array("DATOS" => $result);
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
