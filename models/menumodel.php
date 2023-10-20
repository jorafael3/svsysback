<?php


class MenuModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function Cargar_Menu($param)
    {
        try {
            $USUARIO_ID = $param["Usuario_ID"];

            $query = $this->db->connect_dobra()->prepare("SELECT us.Nombre,sm.Nombre,ss.sub_nombre,
            sm.ruta,
            sm.vista,
            ss.ruta as sub_ruta,
            ss.vista as sub_vista,
            ss.variable as sub_variable,
            sm.variable as menu_variable,
            sm.icono as icono_menu,
            case
                when sm.vista is not null  or sm.vista != '' then 1 else 0 
            end as Ismenu,
            case
	            when (sm.vista is null  or sm.vista = '') and (ss.ruta is null or ss.ruta = '')  then 1 else 0 
                end as Ismenu_Drop,
            case
                when ss.sub_nombre is null or ss.sub_nombre = '' then 0 else 1 
            end as IsSubmenu,
            acc.*
            from SIS_USUARIO_ACCESOS acc
            left join us_USUARIOS us on us.usuario_ID = acc.usuario_ID
            left join sis_menu sm on sm.menu_ID = acc.menu_ID 
            left join sis_submenu ss on ss.submenu_ID = acc.submenu_ID 
            where us.usuario_ID = :USUARIO_ID
                ");

            $query->bindParam(":USUARIO_ID", $USUARIO_ID, PDO::PARAM_STR);

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
