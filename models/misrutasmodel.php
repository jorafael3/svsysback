<?php


class MisRutasModel extends Model
{


    function Cargar_Mis_Rutas($param)
    {
        try {
            // $USUARIO = $param["USUARIO"];]
            $USUARIO = 7;
            $query = $this->db->connect_dobra()->prepare("SELECT 
            grd.ID,
            date(grd.fecha_creado) as FECHA_RUTA,
            count( grdd.ruta_dia_id) as RUTAS_ASIGNADAS
            from gui_ruta_dia grd 
            left join gui_ruta_dia_detalle grdd 
            on grdd.ruta_dia_id = grd.ID 
            where grd.chofer_id  = :USUARIO
            group by FECHA_RUTA
            order by FECHA_RUTA desc

                ");
            $query->bindParam(":USUARIO", $USUARIO, PDO::PARAM_STR);

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

    function Cargar_Mis_Rutas_Detalle($param)
    {
        try {
            $USUARIO = $param["USUARIO_ID"];
            $RUTA = $param["RUTA"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            grdd.despachado,
            grdd.despachado_fecha,
            grdd.despachado_por,
            grd.ID,
            grdd.ID as RUTA_DETALLE_ID,
            grd.chofer_id as CHOFER_ID,
            uc.PLACA as CHOFER_PLACA,
            uu.Nombre as CHOFER_NOMBRE,
            date(grd.fecha_creado) as FECHA_RUTA,
            grdd.pedido_interno as GUIA,
            grdd.cliente_id as CLIENTE_ID,
            cc.CLIENTE_NOMBRE,
            ccs.sucursal_nombre as CLIENTE_SUCURSAL,
            grdd.cliente_destino_id as CLIENTE_SUCURSAL_ID,
            grdd.producto_id as PRODUCTO_ID,
            ip.Nombre as PRODUCTO_NOMBRE,
            grdd.holcim as HOLCIM,
            grdd.bodega as BODEGA,
            grdd.flete_producto as FLETE_PRODUCTO_ID,
            ip2.Nombre as FLETE_PRODUCTO,
            grdd.flete_cant as FLETE_PRODUCTO_CANT
            from gui_ruta_dia grd 
            left join gui_ruta_dia_detalle grdd 
            on grd.ID = grdd.ruta_dia_id
            left join cli_clientes cc 
            on cc.ID = grdd.cliente_id
            left join cli_clientes_sucursales ccs 
            on ccs.ID = grdd.cliente_destino_id 
            left join inv_productos ip 
            on ip.ID = grdd.producto_id
            left join inv_productos ip2
            on ip2.ID = grdd.flete_producto
            left join us_choferes uc 
            on uc.ID = grd.chofer_id 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            where grd.chofer_id  = :USUARIO
            and grd.ID = :RUTA
                ");
            $query->bindParam(":USUARIO", $USUARIO, PDO::PARAM_STR);
            $query->bindParam(":RUTA", $RUTA, PDO::PARAM_STR);

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

    function Guardar_Documento($param)
    {
        try {

            $IMG = $param["IMG"];
            $IMG_NOMBRE = $param["IMG_NOMBRE"];
            $TIPO = $param["TIPO"];
            $CREADO = $param["CREADO"];
            $ID = $param["ID"];
            $ID_DT = $param["ID_DT"];
            $imageData = base64_decode($IMG);
            $SO = PHP_OS;
            // echo $SO;
            if ($SO  == "Linux") {
                $targetDirectory = "/var/www/html/svsysback/recursos/guias_subidas/";
            } else {
                $targetDirectory = "C:/xampp/htdocs/svsysback/recursos/guias_subidas/";
            }
            $targetFile = $targetDirectory . $IMG_NOMBRE;
            if (file_put_contents($targetFile, $imageData)) {
                $query = $this->db->connect_dobra()->prepare("INSERT into gui_ruta_documentos
                (
                    RUTA_ID,
                    RUTA_ID_DT,
                    nombre,
                    tipo,
                    creado_por,
                    ruta_img
                )VALUES
                (
                    :RUTA_ID,
                    :RUTA_ID_DT,
                    :nombre,
                    :tipo,
                    :creado_por,
                    :ruta_img
                )
                    ");
                $query->bindParam(":RUTA_ID", $ID, PDO::PARAM_STR);
                $query->bindParam(":RUTA_ID_DT", $ID_DT, PDO::PARAM_STR);
                $query->bindParam(":nombre", $IMG_NOMBRE, PDO::PARAM_STR);
                $query->bindParam(":tipo", $TIPO, PDO::PARAM_STR);
                $query->bindParam(":creado_por", $CREADO, PDO::PARAM_STR);
                $query->bindParam(":ruta_img", $targetDirectory, PDO::PARAM_STR);

                if ($query->execute()) {
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode([1, "Imagen Guardada"]);
                    exit();
                } else {
                    $err = $query->errorInfo();
                    echo json_encode([0, $err]);
                    exit();
                }
            } else {
            }

            // echo json_encode($param);
            // exit();

            // // $USUARIO = $param["USUARIO"];]
            // $USUARIO = 7;

        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, $e]);
            exit();
        }
    }

    function Cargar_Documento($param)
    {
        try {

            $ID = $param["ID"];
            $D_ID_DT = $param["D_ID_DT"];

            $query = $this->db->connect_dobra()->prepare("SELECT * FROM gui_ruta_documentos
            WHERE RUTA_ID = :RUTA_ID AND RUTA_ID_DT = :RUTA_ID_DT and estado = 1");
            $query->bindParam(":RUTA_ID", $ID, PDO::PARAM_STR);
            $query->bindParam(":RUTA_ID_DT", $D_ID_DT, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, $result]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
            // echo json_encode($param);
            // exit();

            // // $USUARIO = $param["USUARIO"];]
            // $USUARIO = 7;

        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, $e]);
            exit();
        }
    }

    function Eliminar_Documento($param)
    {
        try {

            $ID = $param["ID"];
            $query = $this->db->connect_dobra()->prepare("UPDATE gui_ruta_documentos
            SET estado = 0
            WHERE ID = :ID");
            $query->bindParam(":ID", $ID, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "Imagen Eliminada"]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, $e]);
            exit();
        }
    }


    function Actualizar_Despacho($param)
    {
        try {

            $ID = $param["ID"];
            $despachado_fecha = date('Y-m-d H:i:s');
            $despachado_por = $param["CREADO"];

            $query = $this->db->connect_dobra()->prepare("UPDATE gui_ruta_dia_detalle
            SET despachado = 1,
                despachado_fecha = :despachado_fecha,
                despachado_por = :despachado_por
            WHERE ID = :ID");
            $query->bindParam(":ID", $ID, PDO::PARAM_STR);
            $query->bindParam(":despachado_fecha", $despachado_fecha, PDO::PARAM_STR);
            $query->bindParam(":despachado_por", $despachado_por, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "Pedido Entregado"]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([0, $e]);
            exit();
        }
    }
}
