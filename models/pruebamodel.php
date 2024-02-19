<?php


class PruebaModel extends Model
{

    function Datos_credito($param)
    {
        try {

            $tipo = $param["TIPO"];

            if ($tipo == 1) {
                $sql = "SELECT 
                FechaCorte ,
                Cliente,
                Identificacion,
                ValorAPagar  
            from cli_creditos_mora_2 ccm
            limit 100";
            } else  if ($tipo == 2) {
                $sql = "SELECT 
                FechaCorte ,
                Cliente,
                Identificacion,
                ValorAPagar  
            from cli_creditos_mora_2 ccm
            where ValorAPagar >= 100
            limit 100";
            } else  if ($tipo == 3) {
                $sql = "SELECT 
                FechaCorte ,
                Cliente,
                Identificacion,
                ValorAPagar  
            from cli_creditos_mora_2 ccm
            where ValorAPagar <= 100
            limit 100";
            }




            $query = $this->db->connect_dobra()->prepare($sql);
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
