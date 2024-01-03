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




    // WITH RankedData AS (
    //     SELECT
    //         *,
    //         ROW_NUMBER() OVER (PARTITION BY Identificacion ORDER BY FechaCorte DESC) AS RowNum
    //     FROM
    //         cli_creditos_mora
    //     WHERE
    //         date(FechaCorte) BETWEEN '20231201' AND '20231231'
    //         and MontoOriginal  < 1000
    //         and Identificacion ='0951821214'
    // )
    // SELECT
    //     *
    // FROM
    //     RankedData r1
    // WHERE
    //     RowNum = 1;
    //    AND NOT EXISTS (
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
