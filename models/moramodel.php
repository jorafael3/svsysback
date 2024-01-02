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
}
