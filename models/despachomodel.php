<?php

// require_once "models/logmodel.php";
// require('public/fpdf/fpdf.php');

class DespachoModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //****** SERVICIOS */

    function Cargar_Gui_Servicios($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from gui_servicios
                where estado = 1');
            // $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([$result, 1]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }


    //****** DESTINOS */

    function Cargar_Gui_Destinos($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT * from gui_destinos
                where estado = 1');
            // $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([$result, 1]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //********* GUIAS */

    function Cargar_Guia($param)
    {
        try {
            $PEDIDO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('
                SELECT g.*, ggp.placa  from guias g
                left join gui_guias_placa ggp
                on ggp.pedido_interno = g.PEDIDO_INTERNO
                where g.PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $DET = $this->Cargar_Guia_detalle($param);
                $VAL = $this->Validar_Guias_Inicializada($param);
                echo json_encode([$result, $DET, 1, $VAL]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Validar_Guias_Inicializada($param)
    {
        try {
            $PEDIDO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT * from gui_guias_despachadas_estado
                where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    if ($result[0]["ESTADO_DESPACHO"] == 0) {
                        return 2;
                    } else {
                        return 1;
                    }
                } else {
                    return 0;
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Guia_detalle($param)
    {
        try {
            $PEDIDO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT * from guias_detalle
                where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return 0;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    //********** GUIARDAR GUIAS */


    function Guardar_Guias_despacho($param)
    {
        try {

            $this->db->connect_dobra()->beginTransaction();

            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $CLIENTE_ENTREGA_ID = $param["CLIENTE_ENTREGA_ID"];
            $SERVICIO_ID = $param["SERVICIO_ID"];
            $DESTINO_ID = $param["DESTINO_ID"];
            $CREADO_POR = $param["CREADO_POR"];
            $PARCIAL = $param["PARCIAL"] == 0 ? 0 : 1;
            $PLACA_CAMBIADA = $param["PLACA_CAMBIADA"];
            $PLACA_CAMBIADA_NUMERO = $param["PLACA_CAMBIADA_NUMERO"];
            $despacho_ID =  date('YmdHis');
            $DETALLE = $param["DETALLE"];
            $UBICACION = $param["UBICACION"];
            $imagen = $param["IMAGEN"];
            if ($imagen != null) {
                $imageData = base64_decode($imagen["image"]);
                $fileName = $PEDIDO_INTERNO . "_" . $despacho_ID . ".jpg";
            } else {
                $fileName = "";
            }




            $VAL_ESTADO = $this->Validar_Estado($PEDIDO_INTERNO);
            if ($VAL_ESTADO == 1) {
                $INSERT_ESTADO = $this->Insert_Estado($param);
                if ($INSERT_ESTADO["GUARDADO"] == 1) {
                    $query = $this->db->connect_dobra()->prepare('INSERT INTO gui_guias_despachadas 
                    (
                        PEDIDO_INTERNO,
                        CLIENTE_ENTREGA_ID,
                        SERVICIO_ID, 
                        DESTINO_ID,
                        CREADO_POR, 
                        PARCIAL, 
                        PLACA_CAMBIADA, 
                        PLACA_CAMBIADA_NUMERO,
                        despacho_ID,
                        UBICACION,
                        imagen
                    ) VALUES(
                        :PEDIDO_INTERNO,
                        :CLIENTE_ENTREGA_ID,
                        :SERVICIO_ID, 
                        :DESTINO_ID,
                        :CREADO_POR, 
                        :PARCIAL, 
                        :PLACA_CAMBIADA, 
                        :PLACA_CAMBIADA_NUMERO,
                        :despacho_ID,
                        :UBICACION,
                        :imagen
                        );
                    ');
                    $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
                    $query->bindParam(":CLIENTE_ENTREGA_ID", $CLIENTE_ENTREGA_ID, PDO::PARAM_STR);
                    $query->bindParam(":SERVICIO_ID", $SERVICIO_ID, PDO::PARAM_STR);
                    $query->bindParam(":DESTINO_ID", $DESTINO_ID, PDO::PARAM_STR);
                    $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);
                    $query->bindParam(":PARCIAL", $PARCIAL, PDO::PARAM_STR);
                    $query->bindParam(":PLACA_CAMBIADA", $PLACA_CAMBIADA, PDO::PARAM_STR);
                    $query->bindParam(":PLACA_CAMBIADA_NUMERO", $PLACA_CAMBIADA_NUMERO, PDO::PARAM_STR);
                    $query->bindParam(":despacho_ID", $despacho_ID, PDO::PARAM_STR);
                    $query->bindParam(":UBICACION", $UBICACION, PDO::PARAM_STR);
                    $query->bindParam(":imagen", $fileName, PDO::PARAM_STR);

                    $mensaje = 0;
                    if ($query->execute()) {
                        $CAB = array("GUARDADO" => 1, "MENSAJE" => "CABECERA GUARDADA");
                        $DET = $this->Guardar_Guias_despacho_dt($DETALLE, $despacho_ID, $PEDIDO_INTERNO);
                        if ($DET["GUARDADO"] == 1) {
                            if ($imagen != null) {
                                $targetDirectory = "C:/xampp/htdocs/svsysback/recursos/guias_subidas/";
                                $targetFile = $targetDirectory . $fileName;
                                if (file_put_contents($targetFile, $imageData)) {
                                    // echo "La imagen se ha guardado correctamente en: " . $targetFile;
                                }
                            }

                            $this->db->connect_dobra()->commit();
                        } else {
                            $this->db->connect_dobra()->rollback();
                        }
                        $mensaje = [$CAB, $DET, $INSERT_ESTADO, $VAL_ESTADO];
                    } else {
                        $err = $query->errorInfo();
                        $CAB = array("GUARDADO" => 0, "MENSAJE" => $err);
                        $mensaje = [$CAB, 0, $INSERT_ESTADO];
                        $this->db->connect_dobra()->rollback();
                    }
                } else {
                    $mensaje = [0, 0, $INSERT_ESTADO, 0];
                    $this->db->connect_dobra()->rollback();
                }
            } else {
                $INSERT_ESTADO = array("GUARDADO" => 2, "MENSAJE" => "PEDIDO YA GUARDADO");
                $mensaje = [$INSERT_ESTADO];
            }


            // $query = $this->db->connect_dobra()->prepare('CALL svsys.GUI_GUIAS_DESPACHO_INSERT (
            //     ?,?,?,?,?
            //     ?,?,?,?,?
            // )');


            echo json_encode($mensaje);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Validar_Estado($NUMERO_PEDIDO)
    {
        try {
            $query = $this->db->connect_dobra()->prepare('SELECT PEDIDO_INTERNO
            from gui_guias_despachadas_estado
            where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $NUMERO_PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) == 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $err = $query->errorInfo();
                // $this->db->connect_dobra()->rollback();
                return -1;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            // $this->db->connect_dobra()->rollback();
            return -1;
        }
    }

    function Insert_Estado($param)
    {
        try {
            date_default_timezone_set('America/Guayaquil');
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $PARCIAL = $param["PARCIAL"] == 0 ? 0 : 1;
            $ESTATO_DESPACHO_TEXTO = $param["PARCIAL"] == 0 ? "COMPLETO" : "PARCIAL";
            $CREADO_POR = $param["CREADO_POR"];
            $FECHA_COMPLETO = $param["PARCIAL"] == 0 ? date("Y-m-d h:m:s") : "";

            $query = $this->db->connect_dobra()->prepare('INSERT INTO gui_guias_despachadas_estado
             (
                PEDIDO_INTERNO, 
                ESTADO_DESPACHO, 
                ESTADO_DESPACHO_TEXTO, 
                CREADO_POR
                ) VALUES(
                    :PEDIDO_INTERNO, 
                    :ESTADO_DESPACHO, 
                    :ESTATO_DESPACHO_TEXTO, 
                    :CREADO_POR
                )
            ');
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(":ESTADO_DESPACHO", $PARCIAL, PDO::PARAM_STR);
            $query->bindParam(":ESTATO_DESPACHO_TEXTO", $ESTATO_DESPACHO_TEXTO, PDO::PARAM_STR);
            $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);
            // $query->bindParam(":FECHA_COMPLETADO", $FECHA_COMPLETO, PDO::PARAM_STR);
            if ($query->execute()) {
                return array("GUARDADO" => 1, "MENSAJE" => "ESTADO GUARDADO");
            } else {
                $err = $query->errorInfo();
                // $this->db->connect_dobra()->rollback();
                return array("GUARDADO" => 0, "MENSAJE" => $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            // $this->db->connect_dobra()->rollback();
            return array("GUARDADO" => 0, "MENSAJE" => $e);
        }
    }

    function Guardar_Guias_despacho_dt($DETALLE, $DESPACHO_ID, $PEDIDO_INTERNO)
    {
        try {

            $val = 0;
            $err = 0;
            for ($i = 0; $i < count($DETALLE); $i++) {
                $PEDIDO_INTERNO  = $PEDIDO_INTERNO;
                $CODIGO = $DETALLE[$i]["CODIGO"];
                $PARCIAL = $DETALLE[$i]["PARCIAL"];
                $NO_ENTREGAR_CODIGO = $DETALLE[$i]["NO_ENTREGAR_CODIGO"];
                $CANTIDAD_PARCIAL = $PARCIAL == 1 ? $DETALLE[$i]["CANT_PARCIAL"] : 0;
                $CANTIDAD_TOTAL = $PARCIAL == 1 ? 0 : $DETALLE[$i]["POR_DESPACHAR"];
                $Despacho_ID = $DESPACHO_ID;
                if ($NO_ENTREGAR_CODIGO == 1) {
                    $CANTIDAD_PARCIAL = 0;
                    $CANTIDAD_TOTAL = 0;
                }
                $query = $this->db->connect_dobra()->prepare('INSERT INTO gui_guias_despachadas_dt
                (
                   PEDIDO_INTERNO, 
                   CODIGO, 
                   PARCIAL, 
                   CANTIDAD_PARCIAL,
                   CANTIDAD_TOTAL,
                   Despacho_ID,
                   NO_ENTREGADA_DESTINO

               ) VALUES(
                   :PEDIDO_INTERNO, 
                   :CODIGO, 
                   :PARCIAL, 
                   :CANTIDAD_PARCIAL,
                   :CANTIDAD_TOTAL,
                   :Despacho_ID,
                   :NO_ENTREGADA_DESTINO
                   
               );
            ');
                $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
                $query->bindParam(":CODIGO", $CODIGO, PDO::PARAM_STR);
                $query->bindParam(":PARCIAL", $PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":CANTIDAD_PARCIAL", $CANTIDAD_PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":CANTIDAD_TOTAL", $CANTIDAD_TOTAL, PDO::PARAM_STR);
                $query->bindParam(":Despacho_ID", $Despacho_ID, PDO::PARAM_STR);
                $query->bindParam(":NO_ENTREGADA_DESTINO", $NO_ENTREGAR_CODIGO, PDO::PARAM_STR);
                if ($query->execute()) {
                    $val++;
                } else {
                    $err = $query->errorInfo();
                }
            }
            if ($val == count($DETALLE)) {
                return array("GUARDADO" => 1, "MENSAJE" => "DETALLE GUARDADO");
            } else {
                return array("GUARDADO" => 0, "MENSAJE" =>  $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }


    //***** GUIAS USUARIO */
    function Guias_usuario($param)
    {

        try {
            $USUARIO = $param["USUARIO_ID"];
            $ESTADO = $param["ESTADO"];
            $ITEMS_POR_PAGINA = $param["ITEMS_POR_PAGINA"];
            $PAGINA_ACTUAL = $param["PAGINA_ACTUAL"];
            $FECHA_INICIO = $param["FECHA_INICIO"];
            $FECHA_FIN = $param["FECHA_FIN"];
            if ($PAGINA_ACTUAL == 0) {
                $VALOR = 0;
            } else if ($PAGINA_ACTUAL == 1) {
                $VALOR = (int)($ITEMS_POR_PAGINA) * ((int)($PAGINA_ACTUAL));
            } else {
                $VALOR = (int)($ITEMS_POR_PAGINA) * ((int)($PAGINA_ACTUAL) - 1);
            }

            if ($ESTADO == 2) {
                $EST = "(1,0)";
            } else {
                $EST = "(" . $ESTADO . ")";
            }


            $query = $this->db->connect_dobra()->prepare('SELECT 
            ID, 
            PEDIDO_INTERNO, 
            ESTADO_DESPACHO, 
            ESTADO_DESPACHO_TEXTO, 
            FECHA_CREADO, 
            CREADO_POR,
            FECHA_COMPLETADO,
            (select count(*) from gui_guias_despachadas_estado where ggde.CREADO_POR = :USUARIO) as CANTIDAD
            from 
            gui_guias_despachadas_estado ggde   
            where ggde.CREADO_POR = :USUARIO
            AND ggde.ESTADO_DESPACHO in ' . $EST . '
            AND DATE(ggde.FECHA_CREADO) between :FECHA_INICIO and :FECHA_FIN
            ORDER by ID
            LIMIT ' . $ITEMS_POR_PAGINA . ' OFFSET ' . $VALOR . '; 
            ');

            $query->bindParam(":USUARIO", $USUARIO, PDO::PARAM_STR);
            $query->bindParam(":FECHA_INICIO", $FECHA_INICIO, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    echo json_encode([$result, $result[0]["CANTIDAD"]]);
                    exit();
                } else {
                    echo json_encode($result);
                    exit();
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Consultar_guia_despachadas($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $USUARIO = $param["USUARIO"];
            $query = $this->db->connect_dobra()->prepare('SELECT
            ggde.FECHA_CREADO, 
            cl.CLIENTE_NOMBRE,
            ser.nombre as SERVICIO, 
            gd.nombre as DESTINO,
            ggde.despacho_ID,
            ggde.PARCIAL,
            ggde.imagen
            from 
                gui_guias_despachadas ggde
            left join cli_clientes cl
            on cl.ID = ggde.CLIENTE_ENTREGA_ID
            left join gui_servicios ser
            on ser.ID = ggde.SERVICIO_ID
            left join gui_destinos gd 
            on gd.ID = ggde.DESTINO_ID 
            WHERE ggde.PEDIDO_INTERNO = :PEDIDO_INTERNO
            AND ggde.CREADO_POR = :USUARIO
            ');

            $query->bindParam(":USUARIO", $USUARIO, PDO::PARAM_STR);
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Consultar_guia_despachadas_dt($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $DESPACHO_ID = $param["DESPACHO_ID"];
            $query = $this->db->connect_dobra()->prepare('SELECT 
            ggdd .PEDIDO_INTERNO,
            ggdd.CODIGO,
            gd.DESCRIPCION,
            gd.UNIDAD,
            gd.POR_DESPACHAR,
            ggdd.PARCIAL,
            ggdd.CANTIDAD_PARCIAL,
            ggdd.CANTIDAD_TOTAL  
            from gui_guias_despachadas_dt ggdd 
            left join guias_detalle gd 
            on gd.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO and gd.CODIGO = ggdd.CODIGO 
            where ggdd.PEDIDO_INTERNO  = :PEDIDO_INTERNO
            and ggdd.despacho_ID = :DESPACHO_ID
            ');

            $query->bindParam(":DESPACHO_ID", $DESPACHO_ID, PDO::PARAM_STR);
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Consultar_guia_despachadas_cabecera($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            // $DESPACHO_ID = $param["DESPACHO_ID"];
            $query = $this->db->connect_dobra()->prepare('SELECT
            g.FECHA_DE_EMISION,
            g.CLIENTE,
            g.PEDIDO_INTERNO,
            ggde.ESTADO_DESPACHO,
            ggde.ESTADO_DESPACHO_TEXTO 
            from guias g 
            left join gui_guias_despachadas_estado ggde 
            on g.PEDIDO_INTERNO  = ggde.PEDIDO_INTERNO
            where g.PEDIDO_INTERNO  = :PEDIDO_INTERNO
            ');

            // $query->bindParam(":DESPACHO_ID", $DESPACHO_ID, PDO::PARAM_STR);
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Guardar_Imagen_guia_despachada($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $imagen = $param["IMAGEN"];
            $DESPACHO_ID = $param["DESPACHO_ID"];
            if ($imagen != null) {
                $imageData = base64_decode($imagen["image"]);
                $fileName = $PEDIDO_INTERNO . "_" . $DESPACHO_ID . ".jpg";
            } else {
                $fileName = "";
            }
            if ($imagen != null) {
                $targetDirectory = "C:/xampp/htdocs/svsysback/recursos/guias_subidas/";
                $targetFile = $targetDirectory . $fileName;
                if (file_put_contents($targetFile, $imageData)) {

                    $query = $this->db->connect_dobra()->prepare('UPDATE gui_guias_despachadas
                        set imagen = :imagen
                        WHERE PEDIDO_INTERNO = :PEDIDO_INTERNO');
                    // $query->bindParam(":DESPACHO_ID", $DESPACHO_ID, PDO::PARAM_STR);
                    $query->bindParam(":imagen", $fileName, PDO::PARAM_STR);
                    $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
                    if ($query->execute()) {
                        // $result = $query->fetchAll(PDO::FETCH_ASSOC);
                        echo json_encode([1, "IMAGEN GUARDADA"]);
                        exit();
                    } else {
                        $err = $query->errorInfo();
                        echo json_encode([0, $err]);
                        exit();
                    }
                } else {
                    echo json_encode([0, "ERROR AL SUBIR LA IMAGEN"]);
                    exit();
                }
            }


            // $DESPACHO_ID = $param["DESPACHO_ID"];

        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }


    //******** GUIAS PARCIAL DESPACHO */

    function Cargar_Guia_parcial($param)
    {
        try {
            $PEDIDO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT g.*, ggp.placa  from guias g
            left join gui_guias_placa ggp
            on ggp.pedido_interno = g.PEDIDO_INTERNO
            where g.PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $DET = $this->Cargar_Guia_detalle_parcial($param);
                echo json_encode([$result, $DET, 1]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([$err, 0, 0]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Guia_detalle_parcial($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            // $DESPACHO_ID = $param["DESPACHO_ID"];
            $query = $this->db->connect_dobra()->prepare('SELECT 
            gd.PEDIDO_INTERNO,
            gd.ORD,
            gd.CODIGO,
            gd.DESCRIPCION,
            gd.UNIDAD,
            gd.POR_DESPACHAR,
            SUM(ggdd.CANTIDAD_PARCIAL) +  SUM(ggdd.CANTIDAD_TOTAL) AS CANTIDAD_PARCIAL_TOTAL,
            SUM(ggdd.CANTIDAD_TOTAL) AS CANTIDAD_TOTAL,
            gd.POR_DESPACHAR - SUM(ggdd.CANTIDAD_PARCIAL) - SUM(ggdd.CANTIDAD_TOTAL) as RESTANTE
            FROM 
                guias_detalle gd
            LEFT JOIN 
                gui_guias_despachadas_dt ggdd
            ON 
                gd.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO 
                AND gd.CODIGO = ggdd.CODIGO
            WHERE 
                gd.PEDIDO_INTERNO = :PEDIDO_INTERNO
            GROUP BY 
                gd.PEDIDO_INTERNO,
                gd.ORD,
                gd.CODIGO,
                gd.DESCRIPCION,
                gd.UNIDAD;
            ');

            // $query->bindParam(":DESPACHO_ID", $DESPACHO_ID, PDO::PARAM_STR);
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return $err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Guardar_Guias_despacho_parcial($param)
    {
        try {

            $this->db->connect_dobra()->beginTransaction();

            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $CLIENTE_ENTREGA_ID = $param["CLIENTE_ENTREGA_ID"];
            $SERVICIO_ID = $param["SERVICIO_ID"];
            $DESTINO_ID = $param["DESTINO_ID"];
            $CREADO_POR = $param["CREADO_POR"];
            $PARCIAL = $param["PARCIAL"] == 0 ? 0 : 1;
            $PLACA_CAMBIADA = $param["PLACA_CAMBIADA"];
            $PLACA_CAMBIADA_NUMERO = $param["PLACA_CAMBIADA_NUMERO"];
            $despacho_ID =  date('YmdHis');
            $DETALLE = $param["DETALLE"];
            $UBICACION = $param["UBICACION"];
            $imagen = $param["IMAGEN"];
            if ($imagen != null) {
                $imageData = base64_decode($imagen["image"]);
                $fileName = $PEDIDO_INTERNO . "_" . $despacho_ID . ".jpg";
            } else {
                $fileName = "";
            }

            $query = $this->db->connect_dobra()->prepare('INSERT INTO gui_guias_despachadas 
            (
                PEDIDO_INTERNO,
                CLIENTE_ENTREGA_ID,
                SERVICIO_ID, 
                DESTINO_ID,
                CREADO_POR, 
                PARCIAL, 
                PLACA_CAMBIADA, 
                PLACA_CAMBIADA_NUMERO,
                despacho_ID,
                UBICACION,
                imagen
            ) VALUES(
                :PEDIDO_INTERNO,
                :CLIENTE_ENTREGA_ID,
                :SERVICIO_ID, 
                :DESTINO_ID,
                :CREADO_POR, 
                :PARCIAL, 
                :PLACA_CAMBIADA, 
                :PLACA_CAMBIADA_NUMERO,
                :despacho_ID,
                :UBICACION,
                :imagen
                );
            ');
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(":CLIENTE_ENTREGA_ID", $CLIENTE_ENTREGA_ID, PDO::PARAM_STR);
            $query->bindParam(":SERVICIO_ID", $SERVICIO_ID, PDO::PARAM_STR);
            $query->bindParam(":DESTINO_ID", $DESTINO_ID, PDO::PARAM_STR);
            $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);
            $query->bindParam(":PARCIAL", $PARCIAL, PDO::PARAM_STR);
            $query->bindParam(":PLACA_CAMBIADA", $PLACA_CAMBIADA, PDO::PARAM_STR);
            $query->bindParam(":PLACA_CAMBIADA_NUMERO", $PLACA_CAMBIADA_NUMERO, PDO::PARAM_STR);
            $query->bindParam(":despacho_ID", $despacho_ID, PDO::PARAM_STR);
            $query->bindParam(":UBICACION", $UBICACION, PDO::PARAM_STR);
            $query->bindParam(":imagen", $fileName, PDO::PARAM_STR);
            $mensaje = 0;
            if ($query->execute()) {
                $CAB = array("GUARDADO" => 1, "MENSAJE" => "CABECERA GUARDADA");
                $DET = $this->Guardar_Guias_despacho_dt_parcial($DETALLE, $despacho_ID, $PEDIDO_INTERNO);
                $COM = 0;
                if ($DET["GUARDADO"] == 1) {
                    if ($PARCIAL == 0) {
                        $COM = $this->Actualizar_Parcial_Completo($param);
                        if ($COM["GUARDADO"] == 0) {
                            $this->db->connect_dobra()->rollback();
                        } else {
                            if ($imagen != null) {
                                $targetDirectory = "C:/xampp/htdocs/svsysback/recursos/guias_subidas/";
                                $targetFile = $targetDirectory . $fileName;
                                if (file_put_contents($targetFile, $imageData)) {
                                    // echo "La imagen se ha guardado correctamente en: " . $targetFile;
                                }
                            }
                            $this->db->connect_dobra()->commit();
                        }
                    } else {
                        $this->db->connect_dobra()->commit();
                    }
                } else {
                    $this->db->connect_dobra()->rollback();
                }
                $mensaje = [$CAB, $DET, $COM];
            } else {
                $err = $query->errorInfo();
                $CAB = array("GUARDADO" => 0, "MENSAJE" => $err);
                $mensaje = [$CAB, 0];
                $this->db->connect_dobra()->rollback();
            }

            echo json_encode($mensaje);
            exit();
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Guardar_Guias_despacho_dt_parcial($DETALLE, $DESPACHO_ID, $PEDIDO_INTERNO)
    {
        try {

            $val = 0;
            $err = 0;
            for ($i = 0; $i < count($DETALLE); $i++) {
                $PEDIDO_INTERNO  = $PEDIDO_INTERNO;
                $NO_ENTREGAR_CODIGO = $DETALLE[$i]["NO_ENTREGAR_CODIGO"];
                $CODIGO = $DETALLE[$i]["CODIGO"];
                $PARCIAL = $DETALLE[$i]["PARCIAL"];
                $CANTIDAD_PARCIAL = $PARCIAL == 1 ? $DETALLE[$i]["CANT_PARCIAL"] : 0;
                $CANTIDAD_TOTAL = $PARCIAL == 1 ? 0 : $DETALLE[$i]["RESTANTE"];
                $Despacho_ID = $DESPACHO_ID;
                if ($NO_ENTREGAR_CODIGO == 1) {
                    $CANTIDAD_PARCIAL = 0;
                    $CANTIDAD_TOTAL = 0;
                }

                $query = $this->db->connect_dobra()->prepare('INSERT INTO gui_guias_despachadas_dt
                (
                   PEDIDO_INTERNO, 
                   CODIGO, 
                   PARCIAL, 
                   CANTIDAD_PARCIAL,
                   CANTIDAD_TOTAL,
                   Despacho_ID,
                   NO_ENTREGADA_DESTINO
               ) VALUES(
                   :PEDIDO_INTERNO, 
                   :CODIGO, 
                   :PARCIAL, 
                   :CANTIDAD_PARCIAL,
                   :CANTIDAD_TOTAL,
                   :Despacho_ID,
                   :NO_ENTREGADA_DESTINO
               );
            ');
                $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
                $query->bindParam(":CODIGO", $CODIGO, PDO::PARAM_STR);
                $query->bindParam(":PARCIAL", $PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":CANTIDAD_PARCIAL", $CANTIDAD_PARCIAL, PDO::PARAM_STR);
                $query->bindParam(":CANTIDAD_TOTAL", $CANTIDAD_TOTAL, PDO::PARAM_STR);
                $query->bindParam(":Despacho_ID", $Despacho_ID, PDO::PARAM_STR);
                $query->bindParam(":NO_ENTREGADA_DESTINO", $NO_ENTREGAR_CODIGO, PDO::PARAM_STR);
                if ($query->execute()) {
                    $val++;
                } else {
                    $err = $query->errorInfo();
                }
            }
            if ($val == count($DETALLE)) {
                return array("GUARDADO" => 1, "MENSAJE" => "DETALLE GUARDADO");
            } else {
                return array("GUARDADO" => 0, "MENSAJE" =>  $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode($e);
            exit();
        }
    }

    function Actualizar_Parcial_Completo($param)
    {
        try {
            // date_default_timezone_set('America/Guayaquil');
            $PEDIDO = $param["PEDIDO_INTERNO"];
            // $FECHA_COMPLETO = date("Y-m-d h:m:s");

            $query = $this->db->connect_dobra()->prepare('UPDATE gui_guias_despachadas_estado
            SET 
                ESTADO_DESPACHO = 0, 
                ESTADO_DESPACHO_TEXTO = "COMPLETO"
            where PEDIDO_INTERNO = :pedido');
            $query->bindParam(":pedido", $PEDIDO, PDO::PARAM_STR);
            // $query->bindParam(":FECHA_COMPLETO", $FECHA_COMPLETO, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return array("GUARDADO" => 1, "MENSAJE" => "GUIA COMPLETA");
            } else {
                $err = $query->errorInfo();
                return array("GUARDADO" => 0, "MENSAJE" => $err);
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }


    //***** GUIAS ASIGNADAS */

    function Cargar_guias_asignadas($param)
    {

        try {
            $USUARIO = $param["USUARIO_ID"];
            $ESTADO = $param["ESTADO"];
            $PLACA = $param["PLACA"];
            $ITEMS_POR_PAGINA = $param["ITEMS_POR_PAGINA"];
            $PAGINA_ACTUAL = $param["PAGINA_ACTUAL"];
            $FECHA_INICIO = $param["FECHA_INICIO"];
            $FECHA_FIN = $param["FECHA_FIN"];
            if ($PAGINA_ACTUAL == 0) {
                $VALOR = 0;
            } else if ($PAGINA_ACTUAL == 1) {
                $VALOR = (int)($ITEMS_POR_PAGINA) * ((int)($PAGINA_ACTUAL));
            } else {
                $VALOR = (int)($ITEMS_POR_PAGINA) * ((int)($PAGINA_ACTUAL) - 1);
            }


            $query = $this->db->connect_dobra()->prepare('SELECT 
            g.* ,
            ggp.placa,
            ggde.ESTADO_DESPACHO ,
            ggde.ESTADO_DESPACHO_TEXTO,
            ggde.FECHA_COMPLETADO,
            STR_TO_DATE(g.FECHA_DE_EMISION, "%d.%m.%Y") as FECHA_DE_EMISION,
            (select count(*) from  guias g 
                left join gui_guias_placa ggp
                on ggp.pedido_interno = g.PEDIDO_INTERNO
                left join gui_guias_despachadas_estado ggde 
                on ggde.PEDIDO_INTERNO = g.PEDIDO_INTERNO
                where ggp.placa = :PLACA
                AND STR_TO_DATE(g.FECHA_DE_EMISION, "%d.%m.%Y") BETWEEN :FECHA_INICIO AND :FECHA_FIN 
            ) as CANTIDAD,
            CASE
        		WHEN STR_TO_DATE(g.FECHA_VALIDEZ, "%d.%m.%Y") >= CURDATE() THEN 0
        	ELSE 1
    			END AS ESTADO_VALIDEZ,
    		DATEDIFF(STR_TO_DATE(g.FECHA_VALIDEZ, "%d.%m.%Y"), CURDATE()) AS DIAS_RESTANTES
            from  guias g 
            left join gui_guias_placa ggp
            on ggp.pedido_interno = g.PEDIDO_INTERNO
            left join gui_guias_despachadas_estado ggde 
            on ggde.PEDIDO_INTERNO = g.PEDIDO_INTERNO
            where ggp.placa = :PLACA
            AND STR_TO_DATE(FECHA_DE_EMISION, "%d.%m.%Y") BETWEEN :FECHA_INICIO AND :FECHA_FIN 
            ORDER by  g.FECHA_DE_EMISION desc
            LIMIT ' . $ITEMS_POR_PAGINA . ' OFFSET ' . $VALOR . ';
            ');

            $query->bindParam(":PLACA", $PLACA, PDO::PARAM_STR);
            $query->bindParam(":FECHA_INICIO", $FECHA_INICIO, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    echo json_encode([$result, $result[0]["CANTIDAD"]]);
                    exit();
                } else {
                    echo json_encode($result);
                    exit();
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_guias_asignadas_detalle($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT  * from guias g 
            where PEDIDO_INTERNO = :PEDIDO_INTERNO');
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                $det = $this->Cargar_guias_asignadas_detalle_dt($param);
                echo json_encode([$result, $det]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_guias_asignadas_detalle_dt($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare('SELECT  * from guias_detalle g 
            where PEDIDO_INTERNO = :PEDIDO_INTERNO');
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            } else {
                $err = $query->errorInfo();
                return $err;
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //********************************************************************* */
    //*************************   GUIAS WEB    **************************** */
    //********************************************************************* */

    //*** GUIAS SIN DESPACHAR */


    function Cargar_Guias_Sin_Despachar($param)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];
            $query = $this->db->connect_dobra()->prepare("SELECT  g.*, ggp.placa,uc.usuario_id ,uu.Nombre as chofer_nombre ,
            case 	
                when STR_TO_DATE(g.FECHA_VALIDEZ  , '%d.%m.%Y') < curdate() then 0 else 1 
            end as VENCIDO,
            STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y') as FECHA_DE_EMISION,
            STR_TO_DATE(g.FECHA_VALIDEZ , '%d.%m.%Y') as FECHA_VALIDEZ,
            (select sum(factura_total) from gui_guias_facturas ggf where ggf.pedido_interno = g.PEDIDO_INTERNO) as TOTAL_FACTURAS
            from guias g 
            left join gui_guias_placa ggp 
            on ggp.pedido_interno  = g.PEDIDO_INTERNO
            left join us_choferes uc 
            on uc.PLACA  = ggp.placa
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            where g.PEDIDO_INTERNO  not in (select  PEDIDO_INTERNO  from gui_guias_despachadas_estado ggde) 
            and STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') >= :FECHA_INI 
            and STR_TO_DATE(FECHA_DE_EMISION , '%d.%m.%Y') <= :FECHA_FIN;");
            $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Guias_Sin_Despachar_detalle($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare("SELECT * from guias_detalle gd 
            where PEDIDO_INTERNO  = :PEDIDO_INTERNO");
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Reasignar_Nueva_placa($param)
    {
        try {
            $VAL = $this->Validar_Placa_Existente($param);
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $PLACA = $param["PLACA"];

            if ($VAL == 1) {
                $sql = "UPDATE gui_guias_placa 
                SET 
                    placa = :PLACA 
                where pedido_interno  = :PEDIDO_INTERNO";
            } else {
                $sql = "INSERT INTO gui_guias_placa (pedido_interno,placa)values(:PEDIDO_INTERNO,:PLACA)";
            }

            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare($sql);
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(":PLACA", $PLACA, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "DATOS GUARDADOS"]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, "ERROR AL GUARDAR"]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }
    function Validar_Placa_Existente($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare("SELECT pedido_interno from gui_guias_placa gd 
            where pedido_interno  = :PEDIDO_INTERNO");
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                $err = $query->errorInfo();
                echo json_encode($err);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //*** GUIAS DESPACHADAS */

    function Guias_Despachadas_General($param)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];
            $query = $this->db->connect_dobra()->prepare(" SELECT
            g.*, ggp.placa,uc.usuario_id ,uu.Nombre as chofer_nombre ,
           case 	
               when STR_TO_DATE(g.FECHA_VALIDEZ  , '%d.%m.%Y') < curdate() then 0 else 1 
           end as VENCIDO,
           STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y') as FECHA_DE_EMISION,
           STR_TO_DATE(g.FECHA_VALIDEZ , '%d.%m.%Y') as FECHA_VALIDEZ,
           ggde2 .*,
           uu.Nombre  as PEDIDO_CREADO_POR,
           (select sum(factura_total) from gui_guias_facturas ggf where ggf.pedido_interno = g.PEDIDO_INTERNO) as TOTAL_FACTURAS
           from gui_guias_despachadas_estado ggde2
           left join guias g 
           on g.PEDIDO_INTERNO = ggde2 .PEDIDO_INTERNO 
             left join gui_guias_placa ggp 
           on ggp.pedido_interno  = g.PEDIDO_INTERNO
             left join us_choferes uc 
           on uc.PLACA  = ggp.placa
             left join us_usuarios uu 
           on uu.Usuario_ID = uc.usuario_id
           WHERE
                DATE(ggde2.FECHA_COMPLETADO) BETWEEN :FECHA_INI AND :FECHA_FIN;");
            $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Guias_Despachadas_General_detalle($param)
    {
        try {
            $PEDIDO_INTERNO = $param["PEDIDO_INTERNO"];
            $query = $this->db->connect_dobra()->prepare("     
            SELECT 
            gd.PEDIDO_INTERNO,
            gd.ORD,
            gd.CODIGO,
            gd.DESCRIPCION,
            gd.UNIDAD,
            gd.POR_DESPACHAR,
            SUM(ggdd.CANTIDAD_PARCIAL) +  SUM(ggdd.CANTIDAD_TOTAL) AS DESPACHADA,
            SUM(ggdd.CANTIDAD_PARCIAL) +  SUM(ggdd.CANTIDAD_TOTAL) AS ENTREGADA,
            SUM(ggdd.CANTIDAD_TOTAL) AS CANTIDAD_TOTAL,
            gd.POR_DESPACHAR - SUM(ggdd.CANTIDAD_PARCIAL) - SUM(ggdd.CANTIDAD_TOTAL) as RESTANTE
            FROM 
                guias_detalle gd
            LEFT JOIN 
                gui_guias_despachadas_dt ggdd
            ON 
                gd.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO 
                AND gd.CODIGO = ggdd.CODIGO
            WHERE 
                gd.PEDIDO_INTERNO = :PEDIDO_INTERNO
            GROUP BY 
                gd.PEDIDO_INTERNO,
                gd.ORD,
                gd.CODIGO,
                gd.DESCRIPCION,
                gd.UNIDAD;
            ");
            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Guias_Despachadas_Historial($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $USUARIO = $param["USUARIO"];
            $query = $this->db->connect_dobra()->prepare('SELECT
            ggde.FECHA_CREADO, 
            cl.CLIENTE_NOMBRE,
            cl.ID as CLIENTE_ID,
            ser.nombre as SERVICIO, 
            gd.nombre as DESTINO,
            ggde.despacho_ID,
            ggde.PARCIAL,
            ggde.PEDIDO_INTERNO,
            ggde.PLACA_CAMBIADA,
            ggde.PLACA_CAMBIADA_NUMERO,
            uc.PLACA,
            ggde.UBICACION,
            uu.Nombre as DESPACHADO_POR
            from 
                gui_guias_despachadas ggde
            left join cli_clientes cl
            on cl.ID = ggde.CLIENTE_ENTREGA_ID
            left join gui_servicios ser
            on ser.ID = ggde.SERVICIO_ID
            left join gui_destinos gd
            on gd.ID = ggde .DESTINO_ID 
            left join us_choferes uc 
            on uc.usuario_id = ggde.CREADO_POR
            left join us_usuarios uu 
            on uu.Usuario_ID = ggde .CREADO_POR  
            WHERE ggde.PEDIDO_INTERNO = :PEDIDO_INTERNO
            ');

            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Guias_Despachadas_Historial_detalle($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $despacho_ID = $param["despacho_ID"];
            $query = $this->db->connect_dobra()->prepare(' SELECT 
            ggdd .PEDIDO_INTERNO,
            ggdd.CODIGO,
            gd.DESCRIPCION,
            gd.UNIDAD,
            gd.POR_DESPACHAR,
            ggdd.PARCIAL,
            ggdd.CANTIDAD_PARCIAL,
            ggdd.CANTIDAD_TOTAL
            from gui_guias_despachadas_dt ggdd 
            left join guias_detalle gd 
            on gd.PEDIDO_INTERNO = ggdd.PEDIDO_INTERNO and gd.CODIGO = ggdd.CODIGO 
            where ggdd.PEDIDO_INTERNO  = :PEDIDO_INTERNO
            and ggdd.despacho_ID = :despacho_ID
            ');

            $query->bindParam(":PEDIDO_INTERNO", $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(":despacho_ID", $despacho_ID, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //**** GUIAS EN PROCESO DESP */


    function Guias_En_Proceso_Despacho($param)
    {
        try {
            $FECHA_INI = $param["FECHA_INI"];
            $FECHA_FIN = $param["FECHA_FIN"];

            $query = $this->db->connect_dobra()->prepare("SELECT 
            g.PEDIDO_INTERNO,
            ggp.FECHA_CREADO,
            ggp.FECHA_SALE_PLANTA,
            ggp.placa,
            uu.Nombre,
            gd.POR_DESPACHAR
            from guias g 
            left join gui_guias_placa ggp 
            on g.PEDIDO_INTERNO = ggp.PEDIDO_INTERNO
            left join guias_detalle gd 
            on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO
            left join us_choferes uc 
            on uc.placa = ggp.placa 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            where g.PEDIDO_INTERNO not in 
                        (
                            select PEDIDO_INTERNO  from gui_guias_despachadas_estado ggde 
                        )
            and ggp.pedido_interno is not null
            and date(ggp.FECHA_SALE_PLANTA) BETWEEN :FECHA_INI  AND :FECHA_FIN
            and gd.CODIGO = '10016416'
            group by
            g.PEDIDO_INTERNO,
            ggp.FECHA_CREADO,
            ggp.FECHA_SALE_PLANTA,
            ggp.placa,
            uu.Nombre 
            ");

            $query->bindParam(":FECHA_INI", $FECHA_INI, PDO::PARAM_STR);
            $query->bindParam(":FECHA_FIN", $FECHA_FIN, PDO::PARAM_STR);
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //************* FACTURAS */

    function Obtener_Parametros($param)
    {
        try {
            $query = $this->db->connect_dobra()->prepare(' SELECT 
            * FROM sis_parametros');
            // $query->bindParam(":ESTADO", $ESTADO, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_facturas_Pedido($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $query = $this->db->connect_dobra()->prepare('SELECT f.*,c.CLIENTE_NOMBRE FROM
                gui_guias_facturas f
                left join cli_clientes c
                on f.CLIENTE_ID = c.ID
            WHERE pedido_interno = :pedido_interno
            ');
            $query->bindParam(":pedido_interno", $PEDIDO_INTERNO, PDO::PARAM_STR);

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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Guardar_Factura($param)
    {
        try {
            $PEDIDO_INTERNO = trim($param["PEDIDO_INTERNO"]);
            $FECHA = ($param["FECHA"]);
            $SECUENCIA = trim($param["SECUENCIA"]);
            $FACT_NOMBRE = ($param["FACT_NOMBRE"]);
            $NOTA = ($param["NOTA"]);
            $SUBTOTAL_0 = ($param["SUBTOTAL_0"]);
            $SUBTOTAL_12 = ($param["SUBTOTAL_12"]);
            $IVA = ($param["IVA"]);
            $TOTAL = ($param["TOTAL"]);
            $CREADO_POR = ($param["USUARIO"]);
            $CLIENTE_ID = ($param["FACT_CLIENTES"]);

            $query = $this->db->connect_dobra()->prepare('INSERT 
            INTO gui_guias_facturas 
            (
                pedido_interno, 
                factura_fecha, 
                factura_secuencia, 
                factura_nombre, 
                factura_subtotal_0, 
                factura_subtotal_12, 
                factura_impuesto, 
                factura_total,
                factura_nota, 
                CREADO_POR,
                CLIENTE_ID
            ) VALUES(
                :pedido_interno, 
                :factura_fecha, 
                :factura_secuencia, 
                :factura_nombre, 
                :factura_subtotal_0, 
                :factura_subtotal_12, 
                :factura_impuesto, 
                :factura_total,
                :factura_nota, 
                :CREADO_POR,
                :CLIENTE_ID            
            );
            ');
            $query->bindParam(":pedido_interno", $PEDIDO_INTERNO, PDO::PARAM_STR);
            $query->bindParam(":factura_fecha", $FECHA, PDO::PARAM_STR);
            $query->bindParam(":factura_secuencia", $SECUENCIA, PDO::PARAM_STR);
            $query->bindParam(":factura_nombre", $FACT_NOMBRE, PDO::PARAM_STR);
            $query->bindParam(":factura_subtotal_0", $SUBTOTAL_0, PDO::PARAM_STR);
            $query->bindParam(":factura_subtotal_12", $SUBTOTAL_12, PDO::PARAM_STR);
            $query->bindParam(":factura_impuesto", $IVA, PDO::PARAM_STR);
            $query->bindParam(":factura_total", $TOTAL, PDO::PARAM_STR);
            $query->bindParam(":factura_nota", $NOTA, PDO::PARAM_STR);
            $query->bindParam(":CREADO_POR", $CREADO_POR, PDO::PARAM_STR);
            $query->bindParam(":CLIENTE_ID", $CLIENTE_ID, PDO::PARAM_STR);

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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    //******************************************************************* */
    //******************************************************************* */
    //******************************************************************* */
    // GUIAS RETIRADAS 

    function Cargar_Guias_retiradas_Vigentes($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y') as FECHA_DE_EMISION,
            g.PEDIDO_INTERNO,
            STR_TO_DATE(g.FECHA_VALIDEZ , '%d.%m.%Y') as FECHA_VALIDEZ,
            case 	
                when STR_TO_DATE(g.FECHA_VALIDEZ  , '%d.%m.%Y') <= curdate() then 0 else 1 
            end as VENCIDO,
            grdd.despachado,
            grdd.despachado_fecha,
            grdd.despachado_por,
            grdd.cliente_id,
            cc.CLIENTE_NOMBRE as cliente_nombre,
            grdd.cliente_destino_id,
            ccs.sucursal_nombre as cliente_destino_nombre,
            grd.chofer_id,
            grd.fecha_creado as fecha_ruta_creada,
            uc.PLACA as chofer_placa,
            uu.Nombre as chofer_nombre,
            grdd.FACTURA,
            grdd.ID as GRDD_ID
            from guias g 
            left join gui_ruta_dia_detalle grdd 
            on grdd.pedido_interno = g.PEDIDO_INTERNO 
            left join gui_ruta_dia grd 
            on grd.ID = grdd.ruta_dia_id
            left join us_choferes uc 
            on uc.usuario_id = grd.chofer_id 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id
            left join cli_clientes cc 
            on cc.ID = grdd.cliente_id 
            left join cli_clientes_sucursales ccs 
            on ccs.ID = grdd.cliente_destino_id 
            where STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y') BETWEEN :inicio_mes AND :fin_mes
            and g.PEDIDO_INTERNO not in (select PEDIDO_INTERNO from gui_guias_placa ggp)
            order by STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y') desc ");
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);

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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Guias_retiradas_Entregas($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            distinct gd.PEDIDO_INTERNO,
            ggp.FECHA_SALE_PLANTA,
            STR_TO_DATE(g.FECHA_DE_EMISION , '%d.%m.%Y') as FECHA_DE_EMISION,
            uu.Nombre,
            ggp.placa,
            grdd.despachado,
            grdd.despachado_fecha,
            grdd.despachado_por,
            grdd.cliente_id,
            cc.CLIENTE_NOMBRE as cliente_nombre,
            grdd.cliente_destino_id,
            ccs.sucursal_nombre as cliente_destino_nombre,
            uc.usuario_id  as chofer_id,
            grd.fecha_creado as fecha_ruta_creada,
            uc.PLACA as chofer_placa,
            uu.Nombre as chofer_nombre,
            grdd.FACTURA,
            grdd.ID as GRDD_ID
            from gui_guias_placa ggp 
            left join guias g 
            on g.PEDIDO_INTERNO = ggp.pedido_interno
            left join guias_detalle gd 
            on gd.PEDIDO_INTERNO = g.PEDIDO_INTERNO
            left join us_choferes uc 
            on uc.PLACA = ggp.placa 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id
            left join gui_ruta_dia_detalle grdd 
            on grdd.pedido_interno = g.PEDIDO_INTERNO 
            left join gui_ruta_dia grd 
            on grd.ID = grdd.ruta_dia_id
            left join cli_clientes cc 
            on cc.ID = grdd.cliente_id 
            left join cli_clientes_sucursales ccs 
            on ccs.ID = grdd.cliente_destino_id 
            where date(ggp.FECHA_SALE_PLANTA) BETWEEN :inicio_mes AND :fin_mes
            and g.PEDIDO_INTERNO is not null
            order by date(ggp.FECHA_SALE_PLANTA) desc");
            $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);

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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Cargar_Guias_retiradas_No_Ingresadas($param)
    {
        try {
            $inicio_mes = $param["inicio_mes"];
            $fin_mes = $param["fin_mes"];
            $query = $this->db->connect_dobra()->prepare("SELECT 
            ggp.*,
            uu.Nombre
            from gui_guias_placa ggp
            left join us_choferes uc 
            on uc.PLACA = ggp.placa 
            left join us_usuarios uu 
            on uu.Usuario_ID = uc.usuario_id 
            where pedido_interno not in (select pedido_interno from guias g)");
            // $query->bindParam(":inicio_mes", $inicio_mes, PDO::PARAM_STR);
            // $query->bindParam(":fin_mes", $fin_mes, PDO::PARAM_STR);
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
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }

    function Actualizar_FActura($param)
    {
        try {
            $GRDD_ID = $param["GRDD_ID"];
            $FACTURA = $param["FACTURA"];
            $query = $this->db->connect_dobra()->prepare("UPDATE gui_ruta_dia_detalle
            SET
                factura = :FACTURA
            WHERE
                ID = :ID
                ");
            $query->bindParam(":ID", $GRDD_ID, PDO::PARAM_STR);
            $query->bindParam(":FACTURA", $FACTURA, PDO::PARAM_STR);

            if ($query->execute()) {
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode([1, "FACTURA ACTUALIZADA"]);
                exit();
            } else {
                $err = $query->errorInfo();
                echo json_encode([0, $err]);
                exit();
            }
        } catch (PDOException $e) {
            $e = $e->getMessage();
            echo json_encode([$e, 0, 0]);
            exit();
        }
    }
}
