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

    function Consultar_Cliente($param1)
    {
        // echo json_encode($param1);
        // exit();

        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from usuarios
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
            foreach ($param as $row) {
                $query = $this->db->connect_dobra()->prepare('SELECT distinct  ss.sub_nombre as label, 
                concat(convert(ss.padre_id,varchar(10)),"_",convert(ss.submenu_ID ,varchar(10))) as value,
                acc.menu_ID,
                case
                    when acc.menu_ID  is null then 0 else 1
                end as checked
                from sis_submenu ss
                left join SIS_USUARIO_ACCESOS acc
                on acc.menu_ID  = ss.submenu_ID  and acc.usuario_ID  = "1"
                ');
                $query->bindParam(":menu_id", $row["menu_ID"], PDO::PARAM_STR);
                if($row["vista"] != null )
                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    if (count($result) > 0) {
                        $row["children"] = $result;
                        array_push(
                            $ARRAY,
                            array(
                                "value" => $row["menu_ID"],
                                "label" => $row["Nombre"],
                                "children" => $result,
                            )
                        );
                    } else {
                        array_push(
                            $ARRAY,
                            array(
                                "value" => $row["menu_ID"],
                                "label" => $row["Nombre"],
                            )
                        );
                    }
                } else {
                    $err = $query->errorInfo();
                    echo json_encode($err);
                    exit();
                }
            }
            return $ARRAY;
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }
}
