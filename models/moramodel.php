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
            }

            $query = $this->db->connect_dobra()->prepare("SELECT count(*) as cant FROM 
            cli_creditos_mora -- AS cr order by FechaCorte limit 0, 10");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $CANTIDAD_BASE = $result[0]["cant"];
            // echo $line_count;

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
                    echo json_encode($result);
                    exit();
                } else {
                    $err = $query->errorInfo();
                    echo json_encode($$err);
                    exit();
                }
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    function Evolucion_Morocidad_Grafico()
    {

        try {
            $query = $this->db->connect_dobra()->prepare("
            SELECT
        Date(FechaCorte) AS ReportDate,
        SUM(Saldo) as Saldo ,
        SUM(CASE WHEN Atraso > 30 THEN 1 ELSE 0 END) / COUNT(*) * 100 AS Atraso30
        FROM
        cli_creditos_mora
        WHERE
            OrigenCredito NOT IN ('REFINANCIAMIENTO', 'REPRESTAMO', 'REESTRUCTURA')
            AND EstadoCredito <> 'CANCELADO'
        GROUP BY
        Date(FechaCorte)");
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return $err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

    function Evolucion_Morocidad_Tabla()
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

            $sql2 = 'SELECT * FROM cli_creditos_mora AS cr order by Cliente';
            $query = $this->db->connect_dobra()->prepare($sql2);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return $err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

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

    function Colocacion_por_Monto($param)
    {

        try {

            $fecha_ini = $param["FECHA_INI"];
            $fecha_fin = $param["FECHA_FIN"];
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
                $sql = "WITH RankedData AS (
                    SELECT
                        cr.*,
                        ROW_NUMBER() OVER (PARTITION BY cr.Identificacion ORDER BY cr.FechaCorte DESC) AS RowNum
                    FROM
                        cli_creditos_mora cr
                    WHERE
                        date(cr.FechaCorte) BETWEEN :FECHA_INI AND :FECHA_FIN
                        " . $rango . "
                )
                SELECT
                    *
                FROM
                    RankedData r1
                WHERE
                    RowNum = 1
                   AND NOT EXISTS (
                        SELECT 1
                        FROM RankedData r2
                        WHERE r1.Identificacion = r2.Identificacion
                          AND r2.RowNum > r1.RowNum
                          AND r2.EstadoCredito = 'CANCELADO'
                    )
                    AND r1.EstadoCredito = 'VIGENTE'";
                $query = $this->db->connect_dobra()->prepare($sql);
                $query->bindParam(":FECHA_INI", $fecha_ini, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN", $fecha_fin, PDO::PARAM_STR);
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    array_push($ARREGLO_DATOS, $result);
                } else {
                    $err = $query->errorInfo();
                    // return $err;
                }
            }
            return $ARREGLO_DATOS;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

    function Colocacion_por_Plazo($param)
    {
        try {

            $fecha_ini = $param["FECHA_INI"];
            $fecha_fin = $param["FECHA_FIN"];
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
                $sql = "WITH RankedData AS (
                    SELECT
                        cr.*,
                        ROW_NUMBER() OVER (PARTITION BY cr.Identificacion ORDER BY cr.FechaCorte DESC) AS RowNum
                    FROM
                        cli_creditos_mora cr
                    WHERE
                        date(cr.FechaCorte) BETWEEN :FECHA_INI AND :FECHA_FIN
                        " . $rango . "
                )
                SELECT
                    *
                FROM
                    RankedData r1
                WHERE
                    RowNum = 1
                   AND NOT EXISTS (
                        SELECT 1
                        FROM RankedData r2
                        WHERE r1.Identificacion = r2.Identificacion
                          AND r2.RowNum > r1.RowNum
                          AND r2.EstadoCredito = 'CANCELADO'
                    )
                    AND r1.EstadoCredito = 'VIGENTE'";
                $query = $this->db->connect_dobra()->prepare($sql);
                $query->bindParam(":FECHA_INI", $fecha_ini, PDO::PARAM_STR);
                $query->bindParam(":FECHA_FIN", $fecha_fin, PDO::PARAM_STR);
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    array_push($ARREGLO_DATOS, $result);
                } else {
                    $err = $query->errorInfo();
                    // return $err;
                }
            }
            return $ARREGLO_DATOS;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return $e;
        }
    }

    function Cargar_Datos_Cliente($param)
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
}
