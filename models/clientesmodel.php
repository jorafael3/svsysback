<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');

class ClientesModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Cargar_Clientes()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT  ID as key1 , CLIENTE_NOMBRE as label FROM
            clientes
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


    //*** CLIENTES WEB */

    function Cargar_Clientes_Web()
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * FROM
            cli_clientes
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

    function Nuevo_Cliente($param)
    {
        try {

            $VAL_VLIENTE = $this->Validar_Cliente($param);
            if ($VAL_VLIENTE == 0) {
                $CLI_RUC = $param["CLI_RUC"];
                $CLI_RAZON = $param["CLI_RAZON"];
                $CLI_NOMBRE = $param["CLI_NOMBRE"];
                $CLI_PROVINCIA_ID = $param["CLI_PROVINCIA_ID"];
                $CLI_PROVINCIA = $param["CLI_PROVINCIA"];
                $CLI_CIUDADES = $param["CLI_CIUDADES"];
                $CLI_DIRECCION = $param["CLI_DIRECCION"];
                $CLI_CORREO = $param["CLI_CORREO"];
                $CLI_TELEFONO = $param["CLI_TELEFONO"];

                $query = $this->db->connect_dobra()->prepare('INSERT INTO 
                clientes 
                (
                    CLIENTE_RUC,
                    CLIENTE_NOMBRE, 
                    CLIENTE_RAZON_SOCIAL, 
                    CLIENTE_PROVINCIA_ID, 
                    CLIENTE_PROVINCIA_NOMBRE, 
                    CLIENTE_CIUDAD, 
                    CLIENTE_DIRECCION, 
                    CLIENTE_EMAIL, 
                    CLIENTE_TELEFONO
                ) VALUES
                (
                    :CLIENTE_RUC,
                    :CLIENTE_NOMBRE, 
                    :CLIENTE_RAZON_SOCIAL, 
                    :CLIENTE_PROVINCIA_ID, 
                    :CLIENTE_PROVINCIA_NOMBRE, 
                    :CLIENTE_CIUDAD, 
                    :CLIENTE_DIRECCION, 
                    :CLIENTE_EMAIL, 
                    :CLIENTE_TELEFONO
                );
                ');
                $query->bindParam(":CLIENTE_RUC", $CLI_RUC, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_NOMBRE", $CLI_RAZON, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_RAZON_SOCIAL", $CLI_NOMBRE, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_PROVINCIA_ID", $CLI_PROVINCIA_ID, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_PROVINCIA_NOMBRE", $CLI_PROVINCIA, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_CIUDAD", $CLI_CIUDADES, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_DIRECCION", $CLI_DIRECCION, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_EMAIL", $CLI_CORREO, PDO::PARAM_STR);
                $query->bindParam(":CLIENTE_TELEFONO", $CLI_TELEFONO, PDO::PARAM_STR);


                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode([true, "Datos Guardados", "success"]);
                    exit();
                } else {
                    $err = $query->errorInfo();
                    echo json_encode([false, "Error al guardar " . $err], "error");
                    exit();
                }
            } else {
                echo json_encode([true, "Cédula/ruc ya registrado ", "info"]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Validar_Cliente($param)
    {
        try {
            $CLI_RUC = $param["CLI_RUC"];
            $query = $this->db->connect_dobra()->prepare('SELECT CLIENTE_RUC FROM
            clientes
            WHERE CLIENTE_RUC = :CLIENTE_RUC
            ');
            $query->bindParam(":CLIENTE_RUC", $CLI_RUC, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return count($result);
            } else {
                $err = $query->errorInfo();
                return -1;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function ActivarDesact_Cliente($param)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $CLI_ID = $param["ID"];
            $OPERACION = $param["OPERACION"];

            $query = $this->db->connect_dobra()->prepare('UPDATE cli_clientes 
            SET 
                estado = :estado
            WHERE ID = :ID
            
                ');
            $query->bindParam(":estado", $OPERACION, PDO::PARAM_STR);
            $query->bindParam(":ID", $CLI_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                if ($OPERACION == 1) {
                    echo json_encode([1, "CLIENTE ACTIVADO"]);
                } else {
                    echo json_encode([1, "CLIENTE DESACTIVADO"]);
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
