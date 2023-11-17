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
            $GUIAS_VIGENTES = $this->GUIAS_VIGENTES($param);
            $GUIAS_EN_PROCESO_DESPACHO = $this->GUIAS_EN_PROCESO_DESPACHO($param);
            $A = array(
                "SACOS" => $SACOS,
                "CHOFER" => $CHOFER,
                "GUIAS_DESPACHADAS" => $GUIAS_DESPACHADAS,
                "GUIAS_VIGENTES" => $GUIAS_VIGENTES,
                "GUIAS_EN_PROCESO_DESPACHO" => $GUIAS_EN_PROCESO_DESPACHO,
            );
            echo json_encode($A);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }


    //** POR PRODUCTO */

    function Cantidad_Sacos_Mes($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            $sql = "";
            if ($tipo == "p") {
                $sql = "SELECT
                (
                    select  DESCRIPCION  from guias_detalle gd where CODIGO = :producto
                    limit 1
                ) as DESCRIPCION,
                (
                    select  UNIDAD  from guias_detalle gd where CODIGO = :producto
                    limit 1
                ) as UNIDAD,
                ifnull((
                    select sum(ggdd.CANTIDAD_PARCIAL) + sum(ggdd.CANTIDAD_TOTAL) 
                    from gui_guias_despachadas_dt ggdd
                    left join gui_guias_despachadas_estado ggde 
                    on ggdd.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
                    where ggdd.CODIGO = :producto
                    and date(ggde.FECHA_CREADO) between 
                    DATE_FORMAT(DATE_ADD(:inicio_mes, INTERVAL -1 MONTH), '%Y-%m-01') AND LAST_DAY(DATE_ADD(:fin_mes, INTERVAL -1 MONTH))
                ),0) as CANTIDAD_CEMENTO_MES_ANTERIOR,
                ifnull(sum(ggdd.CANTIDAD_PARCIAL) + sum(ggdd.CANTIDAD_TOTAL),0) as CANTIDAD_CEMENTO_MES_ACTUAL
                from gui_guias_despachadas_dt ggdd 
                left join gui_guias_despachadas_estado ggde 
                on ggdd.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
                where ggdd.CODIGO = :producto
                and date(ggde.FECHA_CREADO) between :inicio_mes and :fin_mes";
            } else if ($tipo == "g") {
                $sql = "SELECT
                (
                    select  DESCRIPCION  from guias_detalle gd where CODIGO = :producto
                    limit 1
                ) as DESCRIPCION,
                (
                    select  UNIDAD  from guias_detalle gd where CODIGO = :producto
                    limit 1
                ) as UNIDAD,
                ifnull((
                    select count(*)  
                    from gui_guias_despachadas_dt ggdd
                    left join gui_guias_despachadas_estado ggde 
                    on ggdd.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
                    where ggdd.CODIGO = :producto
                    and date(ggde.FECHA_CREADO) between 
                    DATE_FORMAT(DATE_ADD(:inicio_mes, INTERVAL -1 MONTH), '%Y-%m-01') AND LAST_DAY(DATE_ADD(:fin_mes, INTERVAL -1 MONTH))
                ),0) as CANTIDAD_CEMENTO_MES_ANTERIOR,
                ifnull(count(*) ,0) as CANTIDAD_CEMENTO_MES_ACTUAL
                from gui_guias_despachadas_dt ggdd 
                left join gui_guias_despachadas_estado ggde 
                on ggdd.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
                where ggdd.CODIGO = :producto
                and date(ggde.FECHA_CREADO) between :inicio_mes and :fin_mes
                and ggde.ESTADO_DESPACHO = 0";
            }

            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);
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
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT
                t1.CODIGO,
                CONCAT(YEAR(t2.FECHA_COMPLETADO), '-', LPAD(MONTH(t2.FECHA_COMPLETADO), 2, '0')) AS AnioMes,
                SUM(t1.CANTIDAD_TOTAL) AS CantidadTotalDespachada
                    FROM
                        gui_guias_despachadas_dt t1
                    left JOIN
                        gui_guias_despachadas_estado t2 ON t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                    WHERE
                        t2.ESTADO_DESPACHO = 0
                        and t1.CODIGO = :producto
                    GROUP BY
                t1.CODIGO,
                YEAR(t2.FECHA_COMPLETADO),
                MONTH(t2.FECHA_COMPLETADO);";
            } else if ($tipo == "g") {
                $sql = "SELECT
                t1.CODIGO,
                CONCAT(YEAR(t2.FECHA_COMPLETADO), '-', LPAD(MONTH(t2.FECHA_COMPLETADO), 2, '0')) AS AnioMes,
                COUNT(t1.CANTIDAD_TOTAL) AS CantidadTotalDespachada
                    FROM
                        gui_guias_despachadas_dt t1
                    left JOIN
                        gui_guias_despachadas_estado t2 ON t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                    WHERE
                        t2.ESTADO_DESPACHO = 0
                        and t1.CODIGO = :producto
                    GROUP BY
                t1.CODIGO,
                YEAR(t2.FECHA_COMPLETADO),
                MONTH(t2.FECHA_COMPLETADO);";
            }

            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);
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
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT 
                uc.usuario_id,
                uu.Nombre,
                ifnull((
                select sum(ggdd.CANTIDAD_PARCIAL) + sum(ggdd.CANTIDAD_TOTAL) as CANT_CEMENTO
                from gui_guias_despachadas_dt ggdd 
                left join gui_guias_despachadas_estado ggde 
                on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO 
                where ggde.CREADO_POR = uu.Usuario_ID  and ggdd.CODIGO =:producto
                and date(ggde.FECHA_CREADO) between :inicio_mes and :fin_mes 
                ),0)as CANT_CEMENTO
                from us_choferes uc 
                left join us_usuarios uu
                on uc.usuario_id = uu.Usuario_ID 
                order by CANT_CEMENTO desc";
            } else if ($tipo == "g") {
                $sql = "SELECT 
                uc.usuario_id,
                uu.Nombre,
                ifnull((
                select count(*) as CANT_CEMENTO
                from gui_guias_despachadas_dt ggdd 
                left join gui_guias_despachadas_estado ggde 
                on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO 
                where ggde.CREADO_POR = uu.Usuario_ID  and ggdd.CODIGO =:producto
                and date(ggde.FECHA_CREADO) between :inicio_mes and :fin_mes 
                ),0)as CANT_CEMENTO
                from us_choferes uc 
                left join us_usuarios uu
                on uc.usuario_id = uu.Usuario_ID 
                order by CANT_CEMENTO desc";
            }

            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $mes = $this->Chofer_Mas_Despachos_Grafico($result[0]["usuario_id"], $param);
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

    function Chofer_Mas_Despachos_Grafico($usuario, $param)
    {
        try {
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT
                    t1.CODIGO,
                    t2.CREADO_POR,
                    concat(year(t2.FECHA_COMPLETADO),'-',lpad(month(t2.FECHA_COMPLETADO),2,'0')) as MES,
                    sum(t1.CANTIDAD_PARCIAL) + sum(t1.CANTIDAD_TOTAL) as CANTIDAD
                    from gui_guias_despachadas_dt t1 
                    join gui_guias_despachadas_estado t2 
                    on t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                    where t1.CODIGO = :producto
                    and t2.CREADO_POR = :usuario
                    group by MES";
            } else if ($tipo == "g") {
                $sql = "SELECT
                t1.CODIGO,
                t2.CREADO_POR,
                concat(year(t2.FECHA_COMPLETADO),'-',lpad(month(t2.FECHA_COMPLETADO),2,'0')) as MES,
                count(t1.CANTIDAD_PARCIAL) as CANTIDAD
                from gui_guias_despachadas_dt t1 
                join gui_guias_despachadas_estado t2 
                on t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                where t1.CODIGO = :producto
                and t2.CREADO_POR = :usuario
                group by MES";
            }

            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":usuario", $usuario, PDO::PARAM_STR);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);

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
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT 
                date(ggde.FECHA_COMPLETADO) as FECHA,
                ggdd.CODIGO,
                '1' as TIPO,
                SUM(ggdd.CANTIDAD_PARCIAL) + SUM(ggdd.CANTIDAD_TOTAL) as cantidad
                from gui_guias_despachadas_estado ggde
                left join gui_guias_despachadas_dt ggdd 
                on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO
                where date(ggde.FECHA_COMPLETADO) between :inicio_mes and :fin_mes
                and CODIGO = :producto
                group by FECHA ";
            } else if ($tipo == "g") {
                $sql = "SELECT 
                date(ggde.FECHA_COMPLETADO) as FECHA,
                ggdd.CODIGO,
                '1' as TIPO,
                count(ggdd.CANTIDAD_PARCIAL) as cantidad
                from gui_guias_despachadas_estado ggde
                left join gui_guias_despachadas_dt ggdd 
                on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO
                where date(ggde.FECHA_COMPLETADO) between :inicio_mes and :fin_mes
                and CODIGO = :producto
                group by FECHA ";
            }
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);

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
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT 
                date(ggde.FECHA_COMPLETADO) as FECHA_ANT,
                ggdd.CODIGO,
                '2' as TIPO,
                SUM(ggdd.CANTIDAD_PARCIAL) + SUM(ggdd.CANTIDAD_TOTAL) as cantidad
                from gui_guias_despachadas_estado ggde
                left join gui_guias_despachadas_dt ggdd 
                on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO
                where date(ggde.FECHA_COMPLETADO) between :inicio_mes and :fin_mes
                and CODIGO = :producto
                group by FECHA_ANT ";
            } else if ($tipo == "g") {
                $sql = "SELECT 
                date(ggde.FECHA_COMPLETADO) as FECHA_ANT,
                ggdd.CODIGO,
                '2' as TIPO,
                COUNT(ggdd.CANTIDAD_PARCIAL)as cantidad
                from gui_guias_despachadas_estado ggde
                left join gui_guias_despachadas_dt ggdd 
                on ggde.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO
                where date(ggde.FECHA_COMPLETADO) between :inicio_mes and :fin_mes
                and CODIGO = :producto
                group by FECHA_ANT ";
            }
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);

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
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT
                t1.CODIGO,
                concat(year(t2.FECHA_CREADO),'-',lpad(month(t2.FECHA_CREADO),2,'0')) as FECHA,
                sum(t1.CANTIDAD_PARCIAL) + sum(t1.CANTIDAD_TOTAL) as cantidad
                from gui_guias_despachadas_dt t1 
                join gui_guias_despachadas_estado t2 
                on t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                where t1.CODIGO = :producto
                group by FECHA";
            } else if ($tipo == "g") {
                $sql = "SELECT
                t1.CODIGO,
                concat(year(t2.FECHA_CREADO),'-',lpad(month(t2.FECHA_CREADO),2,'0')) as FECHA,
                COUNT(t1.CANTIDAD_PARCIAL)as cantidad
                from gui_guias_despachadas_dt t1 
                join gui_guias_despachadas_estado t2 
                on t1.PEDIDO_INTERNO = t2.PEDIDO_INTERNO
                where t1.CODIGO = :producto
                group by FECHA";
            }
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);
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
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT
                gd.CODIGO,
                gd.PEDIDO_INTERNO,
                concat(year(STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y'))) as FECHA,
                sum(gd.POR_DESPACHAR) as cantidad
                from guias_detalle gd 
                join guias g 
                on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO
                where gd.CODIGO = :producto
                group by FECHA";
            } else if ($tipo == "g") {
                $sql = "SELECT
                gd.CODIGO,
                gd.PEDIDO_INTERNO,
                concat(year(STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y'))) as FECHA,
                COUNT(gd.POR_DESPACHAR) as cantidad
                from guias_detalle gd 
                join guias g 
                on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO
                where gd.CODIGO = :producto
                group by FECHA";
            }
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);

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

    function GUIAS_VIGENTES($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            if ($tipo == "p") {
                $sql = "SELECT 
                STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') as FECHA,
                SUM(gd.POR_DESPACHAR) as cantidad
                from guias g
                left join guias_detalle gd 
                on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO 
                where g.PEDIDO_INTERNO not in 
                            (
                                select PEDIDO_INTERNO  from gui_guias_despachadas_estado ggde 
                            )
                and gd.CODIGO = :producto
                and STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') >= :inicio_mes
                and STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') <= :fin_mes
                group by FECHA";
            } else if ($tipo == "g") {
                $sql = "SELECT 
                STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') as FECHA,
                COUNT(gd.POR_DESPACHAR) as cantidad
                from guias g
                left join guias_detalle gd 
                on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO 
                where g.PEDIDO_INTERNO not in 
                            (
                                select PEDIDO_INTERNO  from gui_guias_despachadas_estado ggde 
                            )
                and gd.CODIGO = :producto
                and STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') >= :inicio_mes
                and STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') <= :fin_mes
                group by FECHA";
            }
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);
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

    function GUIAS_EN_PROCESO_DESPACHO($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $producto = $param["producto"];
            $tipo = $param["tipo"];
            $sql = "SELECT 
                g.PEDIDO_INTERNO,
                ggp.FECHA_CREADO,
                ggp.FECHA_SALE_PLANTA,
                gd.CODIGO,
                gd.POR_DESPACHAR 
                from guias g 
                left join gui_guias_placa ggp 
                on g.PEDIDO_INTERNO = ggp.PEDIDO_INTERNO
                left join guias_detalle gd 
                on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO 
                where g.PEDIDO_INTERNO not in 
                            (
                                select PEDIDO_INTERNO  from gui_guias_despachadas_estado ggde 
                            )
                and ggp.pedido_interno is not null
                and gd.CODIGO = :producto
                and date(ggp.FECHA_SALE_PLANTA) = date(curdate())";

            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":producto", $producto, PDO::PARAM_STR);
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
