<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');

class ChoferesModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Cargar_Choferes()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT
            ch.ID,
            ch.Estado,
            ch.placa,
            ch.FECHA_CREADO,
            us.Nombre
            FROM
            us_choferes ch
            LEFT JOIN us_usuarios us
            on us.Usuario_ID = ch.usuario_id
            ');
            // $query->bindParam(":CLIENTE_RUC", $CLI_RUC, PDO::PARAM_STR);
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

    function Cargar_Usuarios()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT
            Usuario_ID as value, Nombre as label
            FROM
            us_usuarios
            WHERE Estado = 1
            ');
            // $query->bindParam(":CLIENTE_RUC", $CLI_RUC, PDO::PARAM_STR);
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

    function Nuevo_Chofer($param)
    {
        try {
            $VAL = $this->Validar_Chofer($param);
            if ($VAL == 0) {

                $VALP = $this->Validar_Placa($param);
                if ($VALP == 1) {
                    echo json_encode([0, "PLACA YA REGISTRADA, PERTENECE A OTRO CHOFER"]);
                } else {
                    $usuario_id = $param["ID"];
                    $PLACA = strtoupper($param["PLACA"]);
                    $CREADO_POR = $param["USUARIO"];
                    $query = $this->db->connect_dobra()->prepare('INSERT INTO 
                    us_choferes 
                    (
                        usuario_id,
                        PLACA, 
                        CREADO_POR
                    ) VALUES(
                        :usuario_id,
                        :PLACA, 
                        :CREADO_POR
                    );
        
                        ');
                    $query->bindParam(":usuario_id", $usuario_id, PDO::PARAM_STR);
                    $query->bindParam(":PLACA", $PLACA, PDO::PARAM_STR);
                    $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);

                    if ($query->execute()) {
                        echo json_encode([1, "CHOFER ACTIVADO"]);
                        exit();
                    } else {
                        $err = $query->errorInfo();
                        echo json_encode([0, "ERROR " . $err]);
                        exit();
                    }
                }
            } else {
                echo json_encode([0, "USUARIO YA REGISTRADO COMO CHOFER"]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Validar_Chofer($param)
    {
        try {
            $usuario_id = trim($param["ID"]);
            $query = $this->db->connect_dobra()->prepare('SELECT Usuario_ID from us_choferes
            WHERE
                usuario_id = :USUARIO_ID');
            $query->bindParam(":USUARIO_ID", $usuario_id, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $err = $query->errorInfo();
                return 1;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return 1;
        }
    }

    function Validar_Placa($param)
    {
        try {
            $PLACA = $param["PLACA"];
            $query = $this->db->connect_dobra()->prepare('SELECT * from us_choferes
            WHERE
                PLACA = :PLACA');
            $query->bindParam(":PLACA", $PLACA, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $err = $query->errorInfo();
                return 1;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            return 1;
        }
    }


    function Actualizar_Chofer($param)
    {
        try {

            $VALP = $this->Validar_Placa($param);
            if ($VALP == 1) {
                echo json_encode([0, "PLACA YA REGISTRADA, PERTENECE A OTRO CHOFER"]);
            } else {
                $usuario_id = $param["ID"];
                $PLACA = strtoupper($param["PLACA"]);
                $query = $this->db->connect_dobra()->prepare('UPDATE 
                us_choferes 
                SET
                    PLACA =:PLACA
                WHERE
                    ID = :ID
    
                    ');
                $query->bindParam(":PLACA", $PLACA, PDO::PARAM_STR);
                $query->bindParam(":ID", $usuario_id, PDO::PARAM_STR);

                if ($query->execute()) {
                    echo json_encode([1, "DATOS ACTUALIZADOS"]);
                    exit();
                } else {
                    $err = $query->errorInfo();
                    echo json_encode([0, "ERROR " . $err]);
                    exit();
                }
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function ActivarDesact_Chofer($param)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $CLI_ID = $param["ID"];
            $OPERACION = $param["OPERACION"];

            $query = $this->db->connect_dobra()->prepare('UPDATE us_choferes 
            SET 
                ESTADO = :estado
            WHERE ID = :ID
            
                ');
            $query->bindParam(":estado", $OPERACION, PDO::PARAM_STR);
            $query->bindParam(":ID", $CLI_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                if ($OPERACION == 1) {
                    echo json_encode([1, "CHOFER ACTIVADO"]);
                } else {
                    echo json_encode([1, "CHOFER DESACTIVADO"]);
                }
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, "ERROR " . $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
