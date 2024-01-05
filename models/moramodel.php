<?php


class MoraModel extends Model
{

    function Cargar_Dashboard()
    {

        $Evolucion_Morocidad_Grafico = $this->Evolucion_Morocidad_Grafico();
        $A = array(
            "EVOLUCION_MOROSIDAD_GRAFICO" => $Evolucion_Morocidad_Grafico,
        );
        echo json_encode($A);
        exit();
    }

    function Evolucion_Morocidad_Grafico()
    {

        try {
            $query = $this->db->connect_dobra()->prepare("SELECT
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
                        *,
                        ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
                    FROM
                        cli_creditos_mora
                    WHERE
                        date(FechaCorte) BETWEEN :FECHA_INI AND :FECHA_FIN
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
            echo json_encode($e);
            exit();
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
                    $rango = "and PlazoOriginal  >= 24 and PlazoOriginal  <= 26";
                }
                $sql = "WITH RankedData AS (
                    SELECT
                        *,
                        ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
                    FROM
                        cli_creditos_mora
                    WHERE
                        date(FechaCorte) BETWEEN :FECHA_INI AND :FECHA_FIN
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
            echo json_encode($e);
            exit();
        }
    }









    //  WITH RankedData AS (
    //     SELECT
    //         *,
    //         ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
    //     FROM
    //         cli_creditos_mora
    //     WHERE
    //         date(FechaCorte) BETWEEN '20231201' AND '20231231'
    //         and MontoOriginal  >= 1000 and MontoOriginal < 1200
    // )
    // SELECT
    //     *
    // FROM
    //     RankedData r1
    // WHERE
    //     RowNum = 1
    //     AND NOT EXISTS (
    //         SELECT 1
    //         FROM RankedData r2
    //         WHERE r1.Identificacion = r2.Identificacion
    //           AND r2.RowNum > r1.RowNum
    //           AND r2.EstadoCredito = 'CANCELADO'
    //     )
    //     AND r1.EstadoCredito = 'VIGENTE';

    //  WITH RankedData AS (
    //     SELECT
    //         *,
    //         ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
    //     FROM
    //         cli_creditos_mora
    //     WHERE
    //         date(FechaCorte) BETWEEN '20231201' AND '20231231'
    //         and MontoOriginal  >= 1200 and MontoOriginal < 1500
    // )
    // SELECT
    //     *
    // FROM
    //     RankedData r1
    // WHERE
    //     RowNum = 1
    //     AND NOT EXISTS (
    //         SELECT 1
    //         FROM RankedData r2
    //         WHERE r1.Identificacion = r2.Identificacion
    //           AND r2.RowNum > r1.RowNum
    //           AND r2.EstadoCredito = 'CANCELADO'
    //     )
    //     AND r1.EstadoCredito = 'VIGENTE';   



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
