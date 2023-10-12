<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');
use LDAP\Result;

class Usuariosmodel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Consultar_Usuarios($param1)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $query = $this->db->connect_dobra()->prepare('SELECT
            us.Nombre,
            us.Usuario,
            us.Usuario_ID,
            us.Estado,
            us.email,
            dp.nombre as departamento,
            dp.ID as departamento_id,
            su.Nombre as sucursal,
            su.ID as sucursal_id
             
            from 
            us_usuarios us
            left join us_departamentos dp
            on dp.ID = us.departamento_id
            left join sis_sucursales su
            on su.ID = us.sucursal_id
                ');
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

    function Cargar_Departamentos($param1)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $query = $this->db->connect_dobra()->prepare('SELECT ID as value, nombre as label from us_departamentos
                WHERE estado = 1
                ');
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

    function Cargar_Sucursales($param1)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT 
                ID as value, Nombre as label 
                from sis_sucursales
                WHERE estado = 1
                ');
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

    //*** USUARIOS */


    function Nuevo_Usuario($param)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $US_USUARIO = $param["US_USUARIO"];
            $US_NOMBRE = $param["US_NOMBRE"];
            $US_EMAIL = $param["US_EMAIL"];
            $US_PASS = $param["US_PASS"];
            $US_DEPT = $param["US_DEPT"];
            $US_SUCURSAL = $param["US_SUCURSAL"];


            $query = $this->db->connect_dobra()->prepare('INSERT INTO svsys.us_usuarios 
            (
                Usuario, 
                Nombre, 
                password, 
                email, 
                departamento_id, 
                sucursal_id 
            ) VALUES(
                :Usuario, 
                :Nombre, 
                :password, 
                :email, 
                :departamento_id, 
                :sucursal_id 
            );
                ');
            $query->bindParam(":Usuario", $US_USUARIO, PDO::PARAM_STR);
            $query->bindParam(":Nombre", $US_NOMBRE, PDO::PARAM_STR);
            $query->bindParam(":password", $US_PASS, PDO::PARAM_STR);
            $query->bindParam(":email", $US_EMAIL, PDO::PARAM_STR);
            $query->bindParam(":departamento_id", $US_DEPT, PDO::PARAM_STR);
            $query->bindParam(":sucursal_id", $US_SUCURSAL, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "DATOS GUARDADOS"]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function ActivarDesact_Usuario($param)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $US_ID = $param["Usuario_ID"];
            $OPERACION = $param["OPERACION"];

            $query = $this->db->connect_dobra()->prepare('UPDATE us_usuarios 
            SET 
                Estado = :estado
            WHERE Usuario_ID = :Usuario_ID
            
                ');
            $query->bindParam(":estado", $OPERACION, PDO::PARAM_STR);
            $query->bindParam(":Usuario_ID", $US_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                if ($OPERACION == 1) {
                    echo json_encode([1, "USUARIO ACTIVADO"]);
                } else {
                    echo json_encode([1, "USUARIO DESACTIVADO"]);
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


    // ACCESOS
    function Consultar_Accesos($param1)
    {
        // echo json_encode($param1);
        // exit();
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT sm.menu_ID , sm.Nombre,sm.vista ,
            case
                when sm.vista is null  then 1 else 0 
            end as hasSubmenu,
            case
                when acc.usuario_ID  is null then 0 else 1
            end as checked
            from sis_menu sm
            left join SIS_USUARIO_ACCESOS acc
            on sm.menu_ID = acc.menu_ID and acc.submenu_ID is null and acc.usuario_ID  = "1"
                ');
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $SUBMENU = $this->Consultar_Submenu($result);
                echo json_encode([$SUBMENU, $result]);
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

    function Consultar_Submenu($param)
    {
        // echo json_encode($param1);
        // exit();
        try {

            $ARRAY = [];
            $ARRAY_ACTIVOS = [];
            $ARRAY_EXPANDIDOS = [];
            foreach ($param as $row) {

                if ($row["vista"] != null) {
                    array_push(
                        $ARRAY,
                        array(
                            "value" => $row["menu_ID"],
                            "label" => $row["Nombre"],
                            "checked" => $row["checked"],
                        )
                    );
                    array_push($ARRAY_EXPANDIDOS, $row["menu_ID"]);
                    if ($row["checked"] == 1) {
                        array_push($ARRAY_ACTIVOS, $row["menu_ID"]);
                    }
                } else {
                    $query = $this->db->connect_dobra()->prepare('SELECT distinct  ss.sub_nombre as label, 
                    concat(convert(ss.padre_id,varchar(10)),"_",convert(ss.submenu_ID ,varchar(10))) as value,
                    acc.menu_ID,ss.padre_id ,
                    case
                        when acc.menu_ID  is null then 0 else 1
                    end as checked
                    from sis_submenu ss
                    left join SIS_USUARIO_ACCESOS acc
                    on acc.menu_ID  = ss.submenu_ID  and acc.usuario_ID  = "1"
                    ');
                    $query->bindParam(":menu_id", $row["menu_ID"], PDO::PARAM_STR);
                    if ($query->execute()) {
                        $MENU_ID = $row["menu_ID"];
                        array_push($ARRAY_EXPANDIDOS, $row["menu_ID"]);
                        // if ($row["checked"] == 1) {
                        //     array_push($ARRAY_ACTIVOS, $row["menu_ID"]);
                        // }

                        $result2 = $query->fetchAll(PDO::FETCH_ASSOC);
                        if (count($result2) > 0) {
                            $ARRAY_FILTRADO = [];
                            foreach ($result2 as $row2) {
                                if ($row2["padre_id"] ===  $MENU_ID) {

                                    array_push($ARRAY_FILTRADO, $row2);
                                    if ($row2["checked"] == 1) {
                                        array_push($ARRAY_ACTIVOS, $row2["value"]);
                                    }
                                }
                            }
                            // $row["children"] = $filtered_arr;
                            array_push(
                                $ARRAY,
                                array(
                                    "value" => $row["menu_ID"],
                                    "label" => $row["Nombre"],
                                    "checked" => $row["checked"],
                                    "children" => $ARRAY_FILTRADO,
                                )
                            );
                        }
                    } else {
                        $err = $query->errorInfo();
                        echo json_encode($err);
                        exit();
                    }
                }
            }
            return [$ARRAY, $ARRAY_EXPANDIDOS, $ARRAY_ACTIVOS];
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Guardar_Accesos($param)
    {
        try {
            $USUARIO_ID = $param["USUARIO_ID"];
            $ACCESOS = $param["ACCESOS"];


            $query = $this->db->connect_dobra()->prepare('DELETE FROM SIS_USUARIO_ACCESOS
            Where usuario_ID = :usuario');
            $query->bindParam(":usuario", $USUARIO_ID, PDO::PARAM_STR);
            if ($query->execute()) {
                if ($ACCESOS != 0) {
                    foreach ($ACCESOS as $menu) {
                        $m = explode("_", $menu);
                        if (count($m) > 1) {
                            $mp = $m[0];
                            $sm = $m[1];
                            $VAL_M = $this->Vaerificar_Menu($USUARIO_ID, $mp);
                            if ($VAL_M == 0) {
                                $this->Insertar_Acceso($USUARIO_ID, $mp, null);
                            }
                            $this->Insertar_Acceso($USUARIO_ID, $mp, $sm);

                            // $m = 123;
                        } else {
                            $m = $m[0];
                            $this->Insertar_Acceso($USUARIO_ID, $m, null);
                        }
                    }
                    echo json_encode(true);
                    exit();
                } else {
                    echo json_encode((true));
                    exit();
                }
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

    function Insertar_Acceso($USUARIO_ID, $MENU_ID, $SUBMENU_ID)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('INSERT
            into SIS_USUARIO_ACCESOS 
            (
                usuario_ID,
                menu_ID,
                submenu_ID
            )VALUES(
                :usuario_ID,
                :menu_ID,
                :submenu_ID
            )');
            $query->bindParam(":usuario_ID", $USUARIO_ID, PDO::PARAM_STR);
            $query->bindParam(":menu_ID", $MENU_ID, PDO::PARAM_STR);
            $query->bindParam(":submenu_ID", $SUBMENU_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return true;
            } else {
                $err = $query->errorInfo();
                return err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Vaerificar_Menu($USUARIO_ID, $MENU_ID)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * FROM
            SIS_USUARIO_ACCESOS
            where usuario_ID = :USUARIO
            and menu_ID = :menu and submenu_ID is null');
            $query->bindParam(":USUARIO", $USUARIO_ID, PDO::PARAM_STR);
            $query->bindParam(":menu", $MENU_ID, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return count($result);
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

    // VALIDAR SESION WEB MOVIL
    function Validar_Usuario_movil($param)
    {
        try {
            $USUARIO = $param["USUARIO"];
            $PASS = $param["PASS"];
            $query = $this->db->connect_dobra()->prepare('SELECT
                us.Usuario,
                us.Usuario_ID,
                dp.nombre as departamento,
                su.nombre as sucursal,
                su.ID as sucursal_id
            FROM us_usuarios us
            LEFT JOIN us_departamentos dp
            on dp.ID = us.departamento_id
            LEFT JOIN sis_sucursales su
            on su.ID = us.sucursal_id
            WHERE
                us.Usuario = :usuario
                and us.password = :pass
                and us.Estado = 1
            ');
            $query->bindParam(":usuario", $USUARIO, PDO::PARAM_STR);
            $query->bindParam(":pass", $PASS, PDO::PARAM_STR);
            // $query->bindParam(":submenu_ID", $SUBMENU_ID, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    echo json_encode([true, $result]);
                    exit();
                } else {
                    echo json_encode([false, "CREDENCIALES INCORRECTAS"]);
                    exit();
                }
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
