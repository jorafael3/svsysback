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
            $FECHA_INI_A = $param["FECHA_INI_A"];
            $FECHA_FIN_A = $param["FECHA_FIN_A"];

            $query = $this->db->connect_dobra()->prepare('SELECT 
            uc.usuario_id  as CHOFER_ID, 
            uu.Nombre as CHOFER_NOMBRE,
            uc.PLACA,
            (select count(*) from gui_guias_despachadas ggd where ggd.CREADO_POR = uu.usuario_id and ggd.PARCIAL  = 0) as GUIAS_DESPACHADAS_TOTAL,
            (select count(*) from gui_guias_despachadas ggd where ggd.CREADO_POR = uu.usuario_id and ggd.PARCIAL  = 1) as GUIAS_PARCIALES,
            (select count(*) from gui_guias_despachadas_estado ggd where ggd.CREADO_POR = uu.usuario_id) as GUIAS_TOTALES,
            (
                select count(*) from gui_guias_despachadas_estado ggd where ggd.CREADO_POR = uu.usuario_id
                and date(ggd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
            ) as GUIAS_DESPACHADAS_MES,
            ifnull( (
                select
                AVG(TIMESTAMPDIFF(SECOND, ggp3.FECHA_SALE_PLANTA, ggde.FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                FROM gui_guias_despachadas_estado ggde 
                left join gui_guias_placa ggp3 
                on ggp3.pedido_interno = ggde.PEDIDO_INTERNO
                WHERE ggde.FECHA_COMPLETADO IS NOT null and CREADO_POR = uc.usuario_id 
                GROUP BY CREADO_POR
            ),null) as PROMEDIO_DEMORA_HORAS_TOTAL,
            ifnull( (
                select
                AVG(TIMESTAMPDIFF(SECOND, ggp3.FECHA_SALE_PLANTA, ggde.FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                FROM gui_guias_despachadas_estado ggde 
                left join gui_guias_placa ggp3 
                on ggp3.pedido_interno = ggde.PEDIDO_INTERNO
                WHERE ggde.FECHA_COMPLETADO IS NOT null and ggde.CREADO_POR = uc.usuario_id
                and date(ggde.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                GROUP BY CREADO_POR
            ),null) as PROMEDIO_DEMORA_HORAS_TOTAL_MES,
            (
                select count(*)  from gui_guias_placa ggp 
                left join guias g 
                on g.PEDIDO_INTERNO = ggp.pedido_interno
                where DATE(ggp.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                and ggp.placa = uc.PLACA
            ) as GUIAS_ASIGNADAS_MES,
            (
                select count(*)  from gui_guias_placa ggp 
                left join guias g 
                on g.PEDIDO_INTERNO = ggp.pedido_interno 
                where ggp.placa = uc.PLACA
            ) as GUIAS_ASIGNADAS_TOTAL,
            (
                ((select count(*)  from gui_guias_placa ggp 
                left join guias g 
                on g.PEDIDO_INTERNO = ggp.pedido_interno 
                where ggp.placa = uc.PLACA) /
                (select count(*) from gui_guias_placa ggp2))*100
                
            ) as GUIAS_ASIGNADAS_TOTAL_PORCENTAJE,
            (
                select distinct 
                count(dt.PEDIDO_INTERNO) 
                from  gui_guias_despachadas_dt dt
                left join gui_guias_despachadas_estado ge 
                on ge.PEDIDO_INTERNO = dt.PEDIDO_INTERNO
                where 
                ge.CREADO_POR = uc.usuario_id
                and dt.PARCIAL = 0
                and CODIGO = "10016416"
            ) as CANTIDAD_TOTAL_GUIAS_CEMENTO
            from us_choferes uc 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            left join gui_guias_despachadas_estado ggde2 
            on ggde2.CREADO_POR = uu.Usuario_ID 
            where date(ggde2.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
            group by 
            uc.usuario_id 
            ');
            // $query->bindParam(":FECHA_INI_A", $FECHA_INI_A, PDO::PARAM_STR);
            // $query->bindParam(":FECHA_FIN_F", $FECHA_FIN_A, PDO::PARAM_STR);
            $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);


            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $result = $this->Reporte_Chofer_DESTINOS($param, $result);
                $result = $this->Reporte_Chofer_CLIENTES($param, $result);
                $result = $this->Reporte_Chofer_GRAFICO_ENTREGAS($param, $result);
                $result = $this->Reporte_Chofer_GRAFICO_ENTREGAS_MES_ANTERIOR($param, $result);
                $result = $this->Reporte_Chofer_GRAFICO_ENTREGAS_POR_MES($param, $result);
                $result = $this->Reporte_Chofer_General_Mes_Anterior($param, $result);
                $result = $this->Reporte_Chofer_Codigos_Despachados($param, $result);
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

    function Reporte_Chofer_General_Mes_Anterior($param,$result)
    {
        try {
            $FECHA_INI = $param["FECHA_INI_A"];
            $FECHA_FIN = $param["FECHA_FIN_A"];
            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare('SELECT 
                uc.usuario_id  as CHOFER_ID, 
                (
                    select count(*) from gui_guias_despachadas_estado ggd where ggd.CREADO_POR = uu.usuario_id
                    and date(ggd.FECHA_CREADO) between :FECHA_INI_A and :FECHA_FIN_A
                ) as GUIAS_DESPACHADAS_MES_ANTERIOR,
                ifnull( (
                    select
                    AVG(TIMESTAMPDIFF(SECOND, ggp3.FECHA_SALE_PLANTA, ggde.FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                    FROM gui_guias_despachadas_estado ggde 
                    left join gui_guias_placa ggp3 
                    on ggp3.pedido_interno = ggde.PEDIDO_INTERNO
                    WHERE ggde.FECHA_COMPLETADO IS NOT null and ggde.CREADO_POR = uc.usuario_id
                    and date(ggde.FECHA_CREADO) between :FECHA_INI_A and :FECHA_FIN_A
                    GROUP BY CREADO_POR
                ),null) as PROMEDIO_DEMORA_HORAS_TOTAL_MES_ANTERIOR,
                (
                    select count(*)  from gui_guias_placa ggp 
                    left join guias g 
                    on g.PEDIDO_INTERNO = ggp.pedido_interno
                    where DATE(ggp.FECHA_CREADO) between :FECHA_INI_A and :FECHA_FIN_A
                    and ggp.placa = uc.PLACA
                ) as GUIAS_ASIGNADAS_MES_ANTERIOR,
                (
                    select count(*)  from gui_guias_placa ggp 
                    left join guias g 
                    on g.PEDIDO_INTERNO = ggp.pedido_interno 
                    where ggp.placa = uc.PLACA
                ) as GUIAS_ASIGNADAS_TOTAL,
                (
                    ((select count(*)  from gui_guias_placa ggp 
                    left join guias g 
                    on g.PEDIDO_INTERNO = ggp.pedido_interno 
                    where ggp.placa = uc.PLACA) /
                    (select count(*) from gui_guias_placa ggp2))*100
                ) as GUIAS_ASIGNADAS_TOTAL_PORCENTAJE
                from us_choferes uc 
                left join us_usuarios uu 
                on uu.Usuario_ID = uc.usuario_id 
                left join gui_guias_despachadas_estado ggde2 
                on ggde2.CREADO_POR = uu.Usuario_ID 
                where date(ggde2.FECHA_CREADO) between :FECHA_INI_A and :FECHA_FIN_A
                group by 
                uc.usuario_id 
                ');
                $query->bindParam(":FECHA_INI_A", $FECHA_INI, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN_A", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);
                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_MES_ANTERIOR"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_MES_ANTERIOR"] = [];
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Reporte_Chofer_Codigos_Despachados($param,$result)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];
            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare("SELECT 
                ggdd.CODIGO,
                gd.DESCRIPCION,
                gd.UNIDAD,
                (
                    select count(*) from  gui_guias_despachadas_dt dt
                    left join gui_guias_despachadas_estado ge 
                    on ge.PEDIDO_INTERNO = dt .PEDIDO_INTERNO and gd .CODIGO = dt.CODIGO
                    where dt.PARCIAL = 0
                    and ge.CREADO_POR = ggde.CREADO_POR 
                    and date(ge.FECHA_CREADO) between :FECHA_INI_A and :FECHA_FIN_A
                )as CANTIDAD_DE_DESPACHOS_CODIGO_MES,
                sum(ggdd.CANTIDAD_TOTAL) + sum(ggdd.CANTIDAD_PARCIAL) as DESPACHADO_CODIGO_MES,
                case
                    when ggdd.CODIGO in ('10016416') then 1 else 0
                end as ESCEMENTO,
                (
                    select sum(ggdd.CANTIDAD_TOTAL) + sum(ggdd.CANTIDAD_PARCIAL) 
                    from gui_guias_despachadas_dt ggdd
                    left join gui_guias_despachadas_estado ge 
                    on ge.PEDIDO_INTERNO = ggdd .PEDIDO_INTERNO and ggdd .CODIGO = gd.CODIGO 
                    where ge.CREADO_POR = ggde.CREADO_POR 
                )as DESPACHADO_CODIGO_TOTAL
                from gui_guias_despachadas_dt ggdd
                left join guias_detalle gd 
                on gd.PEDIDO_INTERNO = ggdd .PEDIDO_INTERNO
                left join gui_guias_despachadas_estado ggde 
                on ggde.PEDIDO_INTERNO = ggdd .PEDIDO_INTERNO and ggdd .CODIGO = gd.CODIGO 
                where ggde.CREADO_POR = :USUARIO_ID
                and date(ggde.FECHA_CREADO) between :FECHA_INI_A and :FECHA_FIN_A
                group by gd.CODIGO
                ");
                $query->bindParam(":FECHA_INI_A", $FECHA_INI, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN_A", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);
                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_CODIGOS_DESPACHADOS"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_CODIGOS_DESPACHADOS"] = [];
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Reporte_Chofer_DESTINOS($param, $result)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];
            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare('SELECT DISTINCT
                gd.nombre AS TipoDestino,
                (
                    select count(*) from gui_guias_despachadas ggd where ggd.DESTINO_ID = gd.ID and ggd.CREADO_POR = :USUARIO_ID
                )as CANTIDAD_TOTAL,
                (
                    select count(*) from gui_guias_despachadas ggd where ggd.DESTINO_ID = gd.ID and ggd.CREADO_POR = :USUARIO_ID
                    and date(ggd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                )as CANTIDAD__PARCIAL,
                  (
                    select count(*) from gui_guias_despachadas ggd where ggd.DESTINO_ID = gd.ID 
                    and date(ggd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                )as CANTIDAD_TOTAL_GENERAL_OTROS_CHOFERES,
                (
                    ((select count(*) from gui_guias_despachadas ggd where ggd.DESTINO_ID = gd.ID and ggd.CREADO_POR = :USUARIO_ID
                    and date(ggd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN)/
                    (select count(*) from gui_guias_despachadas ggd where ggd.DESTINO_ID = gd.ID 
                    and date(ggd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN))*100
                ) as CANTIDAD__PARCIAL_PORCENTAJE
                FROM gui_destinos gd
                LEFT JOIN gui_guias_despachadas gg ON gd.ID = gg.DESTINO_ID
                ORDER BY gd.nombre;
                ');
                $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);
                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_DESTINO"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_DESTINO"] = [];
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Reporte_Chofer_CLIENTES($param, $result)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];

            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare('SELECT DISTINCT
                cc.ID as CLIENTE_ID,
                cc.CLIENTE_NOMBRE,
                ggd.CLIENTE_ENTREGA_ID,
                cc.fecha_creado as CLIENTE_FECHA_REGISTRO,
                (
                    select count(*) from gui_guias_despachadas ggd2 where ggd2.CLIENTE_ENTREGA_ID = cc.ID and ggd2.CREADO_POR = :USUARIO_ID
                )as CANTIDAD_ENTREGAS_TOTAL,
                (
                    select count(*) from gui_guias_despachadas ggd2 where ggd2.CLIENTE_ENTREGA_ID = cc.ID and ggd2.CREADO_POR = :USUARIO_ID
                    and date(ggd2.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                )as CANTIDAD_ENTREGAS_PERIODO,
                (
                    select count(*) from gui_guias_despachadas ggd2 where ggd2.CLIENTE_ENTREGA_ID = cc.ID
                    and date(ggd2.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                )as CANTIDAD_ENTREGAS_TOTAL_OTROS_CHOFERES,
                ifnull( (
                    ((select count(*) from gui_guias_despachadas gd where gd.CLIENTE_ENTREGA_ID  = cc.ID and gd.CREADO_POR = :USUARIO_ID
                    and date(gd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN)/
                    (select count(*) from gui_guias_despachadas gd where gd.CLIENTE_ENTREGA_ID = cc.ID 
                    and date(gd.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN))*100
                ),0)as CANTIDAD_PARCIAL_PORCENTAJE,
                ifnull( (
                    select
                    CONCAT(
                        FLOOR(TIMESTAMPDIFF(HOUR, MAX(FECHA_CREADO), NOW()) / 24), 
                        " días ", 
                        MOD(TIMESTAMPDIFF(HOUR, MAX(FECHA_CREADO), NOW()), 24), 
                        " horas"
                    ) AS diferencia_tiempo
                    FROM gui_guias_despachadas ggd3 
                    WHERE ggd3.CREADO_POR = :USUARIO_ID
                    and date(ggd3.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                    and ggd3.CLIENTE_ENTREGA_ID = cc.ID

                ),"N/A") as ULTIMO_DESPACHO,
                IFNULL((
                    select 
                    AVG(TIMESTAMPDIFF(SECOND, ggp.FECHA_SALE_PLANTA, ggde.FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                    from gui_guias_despachadas_estado ggde
                    left join gui_guias_despachadas ggd2 
                    on ggd2.PEDIDO_INTERNO = ggde.PEDIDO_INTERNO
                    left join gui_guias_placa ggp 
                    on ggp.pedido_interno = ggde.PEDIDO_INTERNO 
                    where ggde.CREADO_POR  = :USUARIO_ID
                    and ggd2.PARCIAL = 0
                    and ggd2.CLIENTE_ENTREGA_ID = cc.ID
                    group by ggde.CREADO_POR
                    
                ),0) as TIEMPO_ESTIMADO_DESPACHO_GENERAL,
                (
                    select 
                    AVG(TIMESTAMPDIFF(SECOND, ggp.FECHA_SALE_PLANTA, ggde.FECHA_COMPLETADO)) / 3600 AS Tiempo_Estimado_Entrega_Horas
                    from gui_guias_despachadas_estado ggde
                    left join gui_guias_despachadas ggd2 
                    on ggd2.PEDIDO_INTERNO = ggde.PEDIDO_INTERNO
                    left join gui_guias_placa ggp 
                    on ggp.pedido_interno = ggde.PEDIDO_INTERNO 
                    where ggde.CREADO_POR  = :USUARIO_ID
                    and ggd2.PARCIAL = 0
                    and date(ggd2.FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                    group by ggde.CREADO_POR
                    
                ) as TIEMPO_ESTIMADO_DESPACHO_PERIODO
                from cli_clientes cc 
                left join gui_guias_despachadas ggd 
                on cc.ID = ggd.CLIENTE_ENTREGA_ID                 
                ');
                $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);

                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_CLIENTE"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_CLIENTE"] = $err;
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Reporte_Chofer_GRAFICO_ENTREGAS($param, $result)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];

            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare('SELECT 
                ggd.CREADO_POR, 
                date(ggd.FECHA_COMPLETADO) as fecha, 
                count(*)  as cantidad
                from gui_guias_despachadas_estado   ggd 
                where CREADO_POR = :USUARIO_ID and FECHA_COMPLETADO is not null 
                and date(FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                group  by  date(ggd.FECHA_COMPLETADO)             
                ');
                $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);

                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_GRAFICO"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_GRAFICO"] = [];
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Reporte_Chofer_GRAFICO_ENTREGAS_MES_ANTERIOR($param, $result)
    {
        try {
            $FECHA_INI = $param["FECHA_INI_A"];
            $FECHA_FIN = $param["FECHA_FIN_A"];

            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare('SELECT 
                ggd.CREADO_POR, 
                date(ggd.FECHA_COMPLETADO) as fecha, 
                count(*)  as cantidad
                from gui_guias_despachadas_estado   ggd 
                where CREADO_POR = :USUARIO_ID and FECHA_COMPLETADO is not null 
                and date(FECHA_CREADO) between :FECHA_INI and :FECHA_FIN
                group  by  date(ggd.FECHA_COMPLETADO)             
                ');
                $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);

                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_GRAFICO_MES_ANTERIOR"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_GRAFICO_MES_ANTERIOR"] = [];
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    
    function Reporte_Chofer_GRAFICO_ENTREGAS_POR_MES($param, $result)
    {
        try {
            // $FECHA_INI = $param["FECHA_INI_A"];
            // $FECHA_FIN = $param["FECHA_FIN_A"];

            for ($i = 0; $i < count($result); $i++) {
                $USUARIO_ID = $result[$i]["CHOFER_ID"];
                $query = $this->db->connect_dobra()->prepare('SELECT * from 
                gui_guias_despachadas_estado ggde 
                where CREADO_POR  = :USUARIO_ID
                and ESTADO_DESPACHO = 0
                         
                ');
                // $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
                // $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
                $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);

                if ($query->execute()) {
                    $res = $query->fetchAll(PDO::FETCH_ASSOC);
                    $result[$i]["DATOS_GRAFICO_MES"] = $res;
                } else {
                    $err = $query->errorInfo();
                    $result[$i]["DATOS_GRAFICO_MES"] = [];
                }
            }
            return $result;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
