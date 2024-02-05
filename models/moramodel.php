<?php


class MoraModel extends Model
{

    function Cargar_Dashboard()
    {

        try {
            $file_path = "resultado.txt";

            // Verifica si el archivo existe
            if (file_exists($file_path)) {
                // Lee el contenido del archivo y cuenta las líneas
                $line_count = count(file($file_path, FILE_SKIP_EMPTY_LINES));
                // echo "El archivo existe y tiene $line_count líneas.\n";
            } else {
                echo json_encode("ERROR");
            }

            $query = $this->db->connect_dobra()->prepare("SELECT count(*) as cant FROM 
            cli_creditos_mora -- AS cr order by FechaCorte limit 0, 10");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $CANTIDAD_BASE = $result[0]["cant"];
            // echo json_encode($line_count);

            if ($CANTIDAD_BASE - $line_count == 0) {
                $file_contents = file_get_contents($file_path);
                $file_lines = explode(PHP_EOL, $file_contents);
                $file_lines = array_filter($file_lines); // Elimina líneas vacías

                $result = [];
                foreach ($file_lines as $line) {
                    $result[] = json_decode($line, true);
                }
                echo json_encode($result);
                exit();
            } else {
                // $line_count = $line_count + 1;
                $query = $this->db->connect_dobra()->prepare("
                SELECT * FROM 
                cli_creditos_mora AS cr order by FechaCorte 
                limit " . $line_count . ", " . $CANTIDAD_BASE . "
                ");
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    $file = fopen("resultado.txt", "a");

                    // Itera sobre el resultado y escribe cada línea en el archivo
                    foreach ($result as $row) {
                        fwrite($file, json_encode($row) . PHP_EOL);
                    }

                    // Cierra el archivo
                    fclose($file);

                    $file_contents = file_get_contents($file_path);
                    $file_lines = explode(PHP_EOL, $file_contents);
                    $file_lines = array_filter($file_lines); // Elimina líneas vacías

                    $res = [];
                    foreach ($file_lines as $line) {
                        $res[] = json_decode($line, true);
                    }

                    echo json_encode($res);
                    exit();
                } else {
                    $err = $query->errorInfo();
                    echo json_encode($err);
                    exit();
                }
            }


            // $Evolucion_Morocidad_Tabla = $this->Evolucion_Morocidad_Tabla();
            // $A = array(
            //     "Evolucion_Morocidad_Tabla" => $Evolucion_Morocidad_Tabla,
            // );
            // echo json_encode($Evolucion_Morocidad_Tabla);
            // exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function OBTENER_ULTIMO_FECHA_CORTE()
    {
        try {

            $query = $this->db->connect_dobra()->prepare("SELECT max(FechaCorte) as fecha from cli_creditos_mora ccm ");
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $fecha = $result[0]["fecha"];
                return $fecha;
            } else {
                $err = $query->errorInfo();
                return $err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

    // **** EVOLUCION MOROSIDAD

    function CARGAR_EVOLUCION_MOROSIDAD_GRAFICO()
    {

        try {
            $query = $this->db->connect_dobra()->prepare("SELECT
                Date(FechaCorte) AS ReportDate,
                SUM(Saldo) as Saldo ,
                SUM(CASE WHEN Atraso > 30 THEN 1 ELSE 0 END) / COUNT(*) * 100 AS Atraso30
                FROM
                cli_creditos_mora
                WHERE
                    -- OrigenCredito NOT IN ('REFINANCIAMIENTO', 'REPRESTAMO', 'REESTRUCTURA')
                    EstadoCredito = 'VENCIDO'
                GROUP BY
                Date(FechaCorte)
                order by date(FechaCorte)");
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
            return $e;
        }
    }

    function CARGAR_EVOLUCION_MOROSIDAD_TABLA()
    {

        try {
            $ARREGLO_DATOS = [];
            $sql = 'WITH RankedData AS (
                SELECT
                    cr.*,
                    ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
                FROM
                    cli_creditos_mora cr                     
                )
                SELECT
                DATEDIFF(date(FechaVencimiento), date(FechaCorte))as rango_dias,
                case 
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 1
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=8
                    then "DE 1 A 8 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 9
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=15
                    then "DE 8 A 15 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 16
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=30
                    then "DE 15 A 30 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 31
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=45
                    then "DE 30 A 45 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 46
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=70
                    then "DE 45 A 70 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 71
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=90
                    then "DE 70 A 90 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 91
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=120
                    then "DE 90 A 120 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 121
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=150
                    then "DE 120 A 150 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 151
                        and DATEDIFF(date(FechaVencimiento), date(FechaCorte)) <=180
                    then "DE 150 A 180 DIAS"
                    when DATEDIFF(date(FechaVencimiento), date(FechaCorte)) >= 181
                    then "DE 180 DIAS"
                end as Rango,
                r1.*
                FROM
                    RankedData r1
                WHERE
                    RowNum = 1
                AND NOT EXISTS (
                        SELECT 1
                        FROM RankedData r2
                        WHERE r1.Identificacion = r2.Identificacion
                        AND r2.RowNum > r1.RowNum
                        AND r2.EstadoCredito = "CANCELADO"
                    )
                    AND r1.EstadoCredito = "VIGENTE"
                    
                ';

            $sql2 = 'SELECT * FROM cli_creditos_mora AS cr 
            order by Cliente
            limit 10000';


            $FECHA_CORTE = $this->OBTENER_ULTIMO_FECHA_CORTE();

            $SQL3 = "SELECT
            DATEDIFF(date(FechaVencimiento), date(FechaCorte)) AS rango_dias,
            CASE 
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 1 AND 8 THEN 'DE 1 A 8 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 9 AND 15 THEN 'DE 8 A 15 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 16 AND 30 THEN 'DE 15 A 30 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 31 AND 45 THEN 'DE 30 A 45 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 46 AND 70 THEN 'DE 45 A 70 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 71 AND 90 THEN 'DE 70 A 90 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 91 AND 120 THEN 'DE 90 A 120 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 121 AND 150 THEN 'DE 120 A 150 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) BETWEEN 151 AND 180 THEN 'DE 150 A 180 DIAS'
                WHEN DATEDIFF(date(FechaVencimiento), date(FechaCorte)) > 180 THEN 'DE 180 DIAS'
            END AS Rango,
            cr.*
        FROM
            cli_creditos_mora cr
        where 
        	DATE(FechaCorte) = :FechaCorte
        	and EstadoCredito = 'VIGENTE'";
            $query = $this->db->connect_dobra()->prepare($SQL3);
            $query->bindParam(":FechaCorte", $FECHA_CORTE, PDO::PARAM_STR);

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
            return $e;
        }
    }

    //*************************************************** */

    function CARTERA_POR_ESTADO()
    {

        try {
            $query = $this->db->connect_dobra()->prepare("WITH RankedData AS (
                SELECT
                   cr.*,
                    ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
                FROM
                    cli_creditos_mora cr
            )
            SELECT
                EstadoCredito,
                count(*) 
            FROM
                RankedData r1
            where
            RowNum = 1
            and	NOT EXISTS (
                    SELECT 1
                    FROM RankedData r2
                    WHERE r1.Identificacion = r2.Identificacion
                      AND r2.RowNum > r1.RowNum
                       AND r2.EstadoCredito = 'CANCELADO'
                )
                -- AND r1.EstadoCredito = 'VIGENTE'
                group  by 
                EstadoCredito");
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

    //** DESCRIPCION COLOCACION */

    function Descripcion_Colocacion($param)
    {

        $Colocacion_por_Monto = $this->Colocacion_por_Monto($param);
        $Colocacion_por_Plazo = $this->Colocacion_por_Plazo($param);
        $A = array(
            "DESCRIPCION_POR_MONTO" => $Colocacion_por_Monto,
            "DESCRIPCION_POR_PLAZO" => $Colocacion_por_Plazo,
        );
        echo json_encode($A);
        exit();
    }

    function CARGAR_POR_MONTO($param)
    {

        try {

            $FECHA_CORTE = $this->OBTENER_ULTIMO_FECHA_CORTE();
            $rango = "and MontoOriginal  < 1000";

            $ARREGLO_DATOS = [];
            for ($i = 0; $i <= 4; $i++) {
                if ($i == 1) {
                    $rango = "and MontoOriginal  >= 1000 and MontoOriginal  < 1200";
                } else if ($i == 2) {
                    $rango = "and MontoOriginal  >= 1200 and MontoOriginal  < 1500";
                } else if ($i == 3) {
                    $rango = "and MontoOriginal  >= 1500 and MontoOriginal  < 4000";
                } else if ($i == 4) {
                    $rango = "and MontoOriginal  > 4000";
                }
                $sql = "SELECT
                        cr.*
                    FROM
                        cli_creditos_mora cr
                    WHERE
                        date(cr.FechaCorte) = :FECHA_CORTE
                        " . $rango . "
                        and EstadoCredito = 'VIGENTE'
                ";
                $query = $this->db->connect_dobra()->prepare($sql);
                $query->bindParam(":FECHA_CORTE", $FECHA_CORTE, PDO::PARAM_STR);
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    array_push($ARREGLO_DATOS, $result);
                } else {
                    $err = $query->errorInfo();
                    // return $err;
                }
            }
            echo json_encode($ARREGLO_DATOS);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

    function CARGAR_POR_PLAZO($param)
    {
        try {

            $FECHA_CORTE = $this->OBTENER_ULTIMO_FECHA_CORTE();

            $rango = "and PlazoOriginal  >= 0 and PlazoOriginal  <= 2";

            $ARREGLO_DATOS = [];
            for ($i = 0; $i <= 8; $i++) {
                if ($i == 1) {
                    $rango = "and PlazoOriginal  >= 3 and PlazoOriginal  <= 5";
                } else if ($i == 2) {
                    $rango = "and PlazoOriginal  >= 6 and PlazoOriginal  <= 8";
                } else if ($i == 3) {
                    $rango = "and PlazoOriginal  >= 9 and PlazoOriginal  <= 11";
                } else if ($i == 4) {
                    $rango = "and PlazoOriginal  >= 12 and PlazoOriginal  <= 14";
                } else if ($i == 5) {
                    $rango = "and PlazoOriginal  >= 15 and PlazoOriginal  <= 17";
                } else if ($i == 6) {
                    $rango = "and PlazoOriginal  >= 18 and PlazoOriginal  <= 20";
                } else if ($i == 7) {
                    $rango = "and PlazoOriginal  >= 21 and PlazoOriginal  <= 23";
                } else if ($i == 8) {
                    $rango = "and cr.PlazoOriginal  >= 24 and cr.PlazoOriginal  <= 26";
                }
                $sql = "SELECT
                        cr.*
                    FROM
                        cli_creditos_mora cr
                    WHERE
                        date(cr.FechaCorte) = :FECHA_CORTE
                        " . $rango . "
                        and EstadoCredito = 'VIGENTE' 
                ";
                $query = $this->db->connect_dobra()->prepare($sql);
                $query->bindParam(":FECHA_CORTE", $FECHA_CORTE, PDO::PARAM_STR);
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    array_push($ARREGLO_DATOS, $result);
                } else {
                    $err = $query->errorInfo();
                    // return $err;
                }
            }
            echo json_encode($ARREGLO_DATOS);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

    //****************************************************** */

    function CARGAR_CLIENTES($param)
    {
        try {
            $RUC = $param["RUC"];
            $RUC2 = "%" . $param["RUC"] . "%";
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];

            $query = $this->db->connect_dobra()->prepare("SELECT  * 
            from cli_creditos_mora ccm 
            where Identificacion = :RUC
            or Cliente like :RUC2
            and date(FechaCorte) between :fecha_ini and :fecha_fin
            order by Cliente,FechaCorte desc
            ");

            $query->bindParam(":RUC", $RUC, PDO::PARAM_STR);
            $query->bindParam(":RUC2", $RUC2, PDO::PARAM_STR);
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

    function Cargar_Creditos_Cancelados($param)
    {
        try {

            $pdo = $this->db->connect_dobra();
            $pdo->exec("SET @row_number = 0;");
            $pdo->exec("SET @id = '';");

            $query2 = $pdo->query("
                    SELECT
                        *,
                        IFNULL(TipoCancelacion, '') as TipoCancelacion,
                        case 
                            	when MontoOriginal between 0 and 500 then '1-500'
                            	when MontoOriginal between 501 and 1000 then '500-1000'
                            	when MontoOriginal between 1001 and 1500 then '1000-1500'
                            	when MontoOriginal between 1501 and 2000 then '1500-2000'
                            	when MontoOriginal > 2000 then '2000 >'
                        end as RANGO
                    FROM (
                        SELECT
                            *,
                            @row_number := IF(@id = Identificacion, @row_number + 1, 1) AS RowNum,
                            @id := Identificacion AS dummy
                        FROM cli_creditos_mora
                        WHERE EstadoCredito = 'CANCELADO' OR CuotasRestantes = 1
                        ORDER BY Identificacion, FechaCorte DESC
                    ) ranked
                    WHERE RowNum = 1;
                ");

            $result2 = $query2->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result2);
            exit();
            // $query1 = $this->db->connect_dobra()->prepare("SELECT MAX(FechaCorte) AS fecha FROM cli_creditos_mora ccm");

            // if ($query1->execute()) {
            //     $result1 = $query1->fetchAll(PDO::FETCH_ASSOC);
            //     $fecha = $result1[0]["fecha"];


            // } else {
            //     $err = $query1->errorInfo();
            //     echo json_encode($err);
            //     exit();
            // }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    //* MOROSIDAD

    function MOROSIDAD_POR_DIA($param)
    {

        try {

            $tipo = $param["tipo"];

            if ($tipo == 1) {
                $slq = "SELECT 
                FechaCorte,
                SUM(CASE WHEN EstadoCredito = 'VIGENTE' THEN 1 ELSE 0 END) AS VIGENTE,
                SUM(CASE WHEN EstadoCredito = 'CANCELADO' THEN 1 ELSE 0 END) AS CANCELADO,
                SUM(CASE WHEN EstadoCredito = 'VENCIDO' THEN 1 ELSE 0 END) AS VENCIDO,
                count(*) as  TOTAL
                from cli_creditos_mora ccm
                group by FechaCorte
                order by date(FechaCorte) desc";
            } else if ($tipo == 2) {
                $slq = "			SELECT 
                DATE_FORMAT(FechaCorte, '%Y-%m') AS FechaCorte,
                SUM(CASE WHEN EstadoCredito = 'VIGENTE' THEN 1 ELSE 0 END) AS VIGENTE,
                SUM(CASE WHEN EstadoCredito = 'CANCELADO' THEN 1 ELSE 0 END) AS CANCELADO,
                SUM(CASE WHEN EstadoCredito = 'VENCIDO' THEN 1 ELSE 0 END) AS VENCIDO,
                count(*) as  TOTAL
                from cli_creditos_mora ccm
                group by month(date(FechaCorte))
                ORDER BY MIN(FechaCorte) DESC;";
            } else if ($tipo == 3) {
                $slq = "SELECT 
                DATE_FORMAT(FechaCorte, '%Y') AS FechaCorte,
                SUM(CASE WHEN EstadoCredito = 'VIGENTE' THEN 1 ELSE 0 END) AS VIGENTE,
                SUM(CASE WHEN EstadoCredito = 'CANCELADO' THEN 1 ELSE 0 END) AS CANCELADO,
                SUM(CASE WHEN EstadoCredito = 'VENCIDO' THEN 1 ELSE 0 END) AS VENCIDO,
                COUNT(*) AS TOTAL
            FROM cli_creditos_mora ccm
            GROUP BY year(FechaCorte)
            ORDER BY FechaCorte DESC;";
            }
            $query = $this->db->connect_dobra()->prepare($slq);
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
            return $e;
        }
    }


    function MOROSIDAD_CARTERA($param)
    {

        try {
            $tipo = $param["tipo"];

            if ($tipo == 1) {
                $slq = "SELECT
                FechaCorte,
                SUM(CASE WHEN TipoCartera = 'CARTERA-BANCO' THEN 1 ELSE 0 END) AS CARTERABANCO,
                SUM(CASE WHEN TipoCartera = 'FONDO-DE-GARANTIA' THEN 1 ELSE 0 END) AS FONDODEGARANTIA,
                sum(Saldo) as SALDO
                from cli_creditos_mora ccm
                group by FechaCorte
                order by date(FechaCorte) desc";
            } else if ($tipo == 2) {
                $slq = "SELECT
                DATE_FORMAT(FechaCorte, '%Y-%m') AS FechaCorte,
                SUM(CASE WHEN TipoCartera = 'CARTERA-BANCO' THEN 1 ELSE 0 END) AS CARTERABANCO,
                SUM(CASE WHEN TipoCartera = 'FONDO-DE-GARANTIA' THEN 1 ELSE 0 END) AS FONDODEGARANTIA,
                sum(Saldo) as SALDO
                from cli_creditos_mora ccm
                group by month(FechaCorte)
                order by MIN(date(FechaCorte)) desc";
            } else if ($tipo == 3) {
                $slq = "SELECT
                DATE_FORMAT(FechaCorte, '%Y') AS FechaCorte,
                SUM(CASE WHEN TipoCartera = 'CARTERA-BANCO' THEN 1 ELSE 0 END) AS CARTERABANCO,
                SUM(CASE WHEN TipoCartera = 'FONDO-DE-GARANTIA' THEN 1 ELSE 0 END) AS FONDODEGARANTIA,
                sum(Saldo) as SALDO
                from cli_creditos_mora ccm
                group by year(FechaCorte)
                order by year(date(FechaCorte)) desc";
            }
            $query = $this->db->connect_dobra()->prepare($slq);
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
            return $e;
        }
    }

    //*** FONDO DE GARANTIA */ */

    function CLIENTES_FONDO_GARANTIA($param)
    {
        try {
            $fecha = $this->OBTENER_ULTIMO_FECHA_CORTE();
            $slq = "SELECT * from cli_creditos_mora ccm 
            where TipoCartera = 'FONDO-DE-GARANTIA'
            and FechaCorte = :FechaCorte";
            $query = $this->db->connect_dobra()->prepare($slq);
            $query->bindParam(":FechaCorte", $fecha, PDO::PARAM_STR);
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
            return $e;
        }
    }

    //* COMPROTAMIOENTO

    function COMPORTAMIENTO($param)
    {

        try {
            $tipo = $param["tipo"];

            if ($tipo == 1) {
                $slq = "SELECT
                FechaCorte,
                SUM(CASE WHEN OrigenCredito = 'NUEVO' and EstadoCredito = 'VIGENTE' and CuotaImpaga = 1 THEN 1 ELSE 0 END) AS NUEVO,
                SUM(CASE WHEN OrigenCredito = 'REPRESTAMO' THEN 1 ELSE 0 END) AS REPRESTAMO,
                SUM(CASE WHEN OrigenCredito = 'REESTRUCTURA' THEN 1 ELSE 0 END) AS REESTRUCTURA,
                SUM(CASE WHEN OrigenCredito = 'REFINANCIAMIENTO' THEN 1 ELSE 0 END) AS REFINANCIAMIENTO
                from cli_creditos_mora ccm
                group by date(FechaCorte)
                order by date(FechaCorte) desc";
            } else if ($tipo == 2) {
                $slq = "SELECT
                DATE_FORMAT(FechaCorte, '%Y-%m') AS FechaCorte,
                SUM(CASE WHEN OrigenCredito = 'NUEVO' and EstadoCredito = 'VIGENTE' and CuotaImpaga = 1 THEN 1 ELSE 0 END) AS NUEVO,
                SUM(CASE WHEN OrigenCredito = 'REPRESTAMO' THEN 1 ELSE 0 END) AS REPRESTAMO,
                SUM(CASE WHEN OrigenCredito = 'REESTRUCTURA' THEN 1 ELSE 0 END) AS REESTRUCTURA,
                SUM(CASE WHEN OrigenCredito = 'REFINANCIAMIENTO' THEN 1 ELSE 0 END) AS REFINANCIAMIENTO
                from cli_creditos_mora ccm
                group by month(FechaCorte)
                order by MIN(date(FechaCorte)) desc";
            } else if ($tipo == 3) {
                $slq = "SELECT
                DATE_FORMAT(FechaCorte, '%Y') AS FechaCorte,
                SUM(CASE WHEN OrigenCredito = 'NUEVO' and EstadoCredito = 'VIGENTE' and CuotaImpaga = 1 THEN 1 ELSE 0 END) AS NUEVO,
                SUM(CASE WHEN OrigenCredito = 'REPRESTAMO' THEN 1 ELSE 0 END) AS REPRESTAMO,
                SUM(CASE WHEN OrigenCredito = 'REESTRUCTURA' THEN 1 ELSE 0 END) AS REESTRUCTURA,
                SUM(CASE WHEN OrigenCredito = 'REFINANCIAMIENTO' THEN 1 ELSE 0 END) AS REFINANCIAMIENTO
                from cli_creditos_mora ccm
                group by year(FechaCorte)
                order by year(date(FechaCorte)) desc";
            }
            $query = $this->db->connect_dobra()->prepare($slq);
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
            return $e;
        }
    }
}
