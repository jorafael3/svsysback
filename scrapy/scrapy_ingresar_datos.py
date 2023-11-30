import pandas as pd
import os
from PyPDF2 import PdfReader
import zipfile
import mysql.connector
from datetime import datetime
from log import *
from datetime import datetime, timedelta
import sqlalchemy
import shutil
import time
conexion = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="svsys"
    )
# conexion = mysql.connector.connect(
#     host="gator4166.hostgator.com",
#     user="salvacer_jorge",
#     password="Equilivre3*",
#     database="salvacer_svsys"
#     )

# server = 'gator4166.hostgator.com' 
# database = 'salvacer_svsys' 
# username = 'salvacer_jorge' 
# password = 'Equilivre3*'  

# DATABASE_URL = 'mysql+mysqlconnector://salvacer_jorge:Equilivre3*@gator4166.hostgator.com:3306/salvacer_svsys'
# engine = sqlalchemy.create_engine(DATABASE_URL)



def Obtener_tabla(texto):
    texto_limpio = texto.replace("PRODUCTO", "").replace("CANTIDAD", "")
    print(texto_limpio)
        # Dividir el texto en líneas
    lineas = texto_limpio.strip().split('\n')
    arra_pro = []

    # Separar las palabras en cada línea

    for i in range(len(lineas) -1):
        palabras = lineas[i].split()
        # Obtener la cantidad "POR DESPACHAR"
        cantidad_por_despachar = (palabras[-1])
            # Crear un diccionario con los datos
        unidad = palabras[-2]
        informacion_producto = {
                'ORD': palabras[0],
                'CODIGO': palabras[1],
                'DESCRIPCION': ' '.join(palabras[2:-2]),
                'UNIDAD': unidad,
                'POR_DESPACHAR': cantidad_por_despachar,
                'DESPACHADA': "",
                'ENTREGADA': ""
            }
        print(informacion_producto)
        arra_pro.append(informacion_producto)
    guardar_log("DATOS TABLA EXTRAIDO",1)
    return arra_pro

def Obtener_cabecera(cabecera):
        # print(cabecera)
        # print(cabecera)
        datos = {}
        # Divide el texto en líneas
        lineas = cabecera.split('\n')
        # Itera a través de las líneas y agrega los datos al diccionario
        for linea in lineas:
            # Divide la línea en clave y valor usando el primer ':' como separador
            partes = linea.split(':', 1)
            # Si hay al menos dos partes (clave y valor), agrega al diccionario
            if len(partes) == 2:
                clave = partes[0].strip()
                valor = partes[1].strip()
                datos[clave] = valor
        # print(datos)
        # Imprime el diccionario
        datos_a = {}
        campos = ["FECHA EMISIÓN",
                  "FECHA DE EMISIÓN",
                  "TIPO DE ENTREGA",
                  "TELÉFONO",
                  "FECHA VALIDEZ",
                  "TIPO DE ENTREGA",
                  "PEDIDO INTERNO",
                  "PED. COMPRA",
                  "FACTURA"]
        
        for clave, valor in datos.items():
                for campo in campos:
                    indice_fecha_emision = valor.find(campo)
                    if indice_fecha_emision != -1:
                        campo1 = valor[:indice_fecha_emision].strip()
                        campo2 = valor[indice_fecha_emision + len(campo) + 1:].strip()
                        datos_a[clave] = campo1
                        datos_a[campo] = campo2
                    else:
                        if clave in datos_a:
                            pass
                        else:
                            datos_a[clave] = valor

        datos_b = {}
        for clave, valor in datos_a.items():
            clave = clave.strip()
            clave  = clave.replace('.', '')
            clave  = clave.replace(' ', '_')
            valor = valor.replace(":",'')
            if(clave =="FECHA_EMISIÓN" or clave =="FECHA_DE_EMISIÓN"):
                clave = "FECHA_EMISIÓN"
            datos_b[clave] = valor

        guardar_log("DATOS CABECERA EXTRAIDOS",1)
        return datos_b

def Leer_pdf():
    

    array_datos = []
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/pdf'
    archivos_en_directorio = os.listdir(directorio)
    if(len(archivos_en_directorio) > 0):

        for buscar_zip in archivos_en_directorio:
        # # Imprime el nombre de archivo (puedes hacer otra cosa con él)
            if(buscar_zip.endswith(".zip") or buscar_zip.endswith(".ZIP")):
                guardar_log("BUSCANDO ZIP",1)
                ruta_zip = (directorio+"/"+ buscar_zip)
                # print(ruta_zip)
                with zipfile.ZipFile(ruta_zip, 'r') as archivo_zip:
                    archivo_zip.extractall(directorio+"/")
                    guardar_log("EXTRAYENDO ZIP",1)

        # time.sleep(3)
        guardar_log("BUSCANDO ARCHIVOS PARA ANALIZAR",1)
        for nombre_archivo in archivos_en_directorio:
            if(nombre_archivo.endswith(".pdf") or nombre_archivo.endswith(".PDF")):
                try:
                    with open('C:/xampp/htdocs/svsysback/scrapy/pdf/'+nombre_archivo, 'rb') as pdf_file:
                        # Crea un objeto PdfFileReader para leer el PDF
                        guardar_log("ANALIZANDO "+str(nombre_archivo),1)
                        pdf_reader = PdfReader(pdf_file)
                        page = pdf_reader.pages[0]
                        # print(page.extract_text())
                        texto = page.extract_text()
                        guardar_log("TEXTO EXTRAIDO",1)
                        # Definir un patrón utilizando una expresión regular
                        # Encuentra la posición de "COMENTARIOS:"
                        posicion_comentarios = texto.find("COMENTARIOS:")
                        eliminiar_duplicado = texto[:posicion_comentarios].strip()
                        # print(eliminiar_duplicado)
                        posicion_cabecera = eliminiar_duplicado.find("ENTREGADA")
                        tabla = eliminiar_duplicado[:posicion_cabecera].strip()
                        # print(tabla)
                        cabecera = eliminiar_duplicado[posicion_cabecera + len("ENTREGADA"):].strip()
                        guardar_log("ELIMINANDO DUPLICADOS",1)
                        guardar_log("OBTENIENDO DATOS TABLA",1)
                        datos_tabla = Obtener_tabla(tabla)
                        # time.sleep(1)
                        guardar_log("OBTENIENDO DATOS CABECERA",1)
                        datos_cabecera = Obtener_cabecera(cabecera)
                        # print(datos_tabla)
                        # print(datos_cabecera)
                        lista = [datos_cabecera,datos_tabla,]
                        array_datos.append(lista)
                        # print("*******************************************")
                        # print(array_datos)
                   
                except:
                    guardar_log("NO SE PUDO ABRIR EL ARCHIVO "+str(nombre_archivo),1)

        guardar_log("PREPARANDO PARA GUARDAR DATOS",1)
        print("PREPARANDO PARA GUARDAR DATOS")
        Guardar_Guias(array_datos)
    else:
        guardar_log("NO HAY ARCHIVOS EN EL DIRECTORIO",1)

def Validar_Cabecera(numero):
    # conn = engine.connect()
    # consulta_sql = sqlalchemy.text('SELECT * FROM guias WHERE PEDIDO_INTERNO = :PEDIDO_INTERNO')
    # result = conn.execute(consulta_sql,PEDIDO_INTERNO=numero )
    # resultados = []
    # for row in result:
    #         resultados.append(row)
    # print(numero)
    cursor = conexion.cursor()
    consulta = 'SELECT count(PEDIDO_INTERNO) as PEDIDO_INTERNO  FROM guias WHERE PEDIDO_INTERNO = %s'
    valores = (numero,)
    cursor.execute(consulta,valores)
    resultados = cursor.fetchone()
    count = resultados[0]
    print(count)
    return count

def Guardar_Cabecera(datos):
    try: 
        cursor = conexion.cursor()
        numero = datos["PEDIDO_INTERNO"].strip()
        numero = numero.replace(" ","")

        val = Validar_Cabecera(numero)
        if val == 0:
            consulta = """
                INSERT INTO guias(
                    FECHA_DE_EMISION,
                    FACTURA,
                    TELEFONO,
                    FECHA_VALIDEZ,

                    CLIENTE,
                    CLIENTE_RUC,
                    SOLICITANTE,
                    DIRECCION_1,

                    PTO_DE_PARTIDA,
                    PTO_DE_LLEGADA,
                    DIRECCION_2,

                    TIPO_DE_ENTREGA,
                    PEDIDO_INTERNO,
                    PED_COMPRA
                    ) VALUES (
                        %s, %s, %s , %s,
                        %s, %s, %s , %s,
                        %s, %s, %s , 
                        %s, %s, %s
                        )
            """
            # fecha_emision = datetime.strptime(datos["FECHA_EMISIÓN"].strip(), "%d.%m.%Y")
            # fecha_emision = fecha_emision.strftime("%Y/%m/%d")

            # fecha_val = datetime.strptime(datos["FECHA_VALIDEZ"].strip(), "%d.%m.%Y")
            # fecha_val = fecha_val.strftime("%Y/%m/%d")

            valores = (
                    datos["FECHA_EMISIÓN"].strip(), 
                    datos["FACTURA"].strip(),           
                    datos["TELÉFONO"].strip(),
                    datos["FECHA_VALIDEZ"].strip(),

                    datos["CLIENTE"].strip(),
                    datos["RUC"].strip(),
                    datos["SOLICITANTE"].strip(),
                    datos["DIRECCION"].strip(),

                    datos["PTO_DE_PARTIDA"].strip(),
                    datos["PTO_DE_LLEGADA"].strip(),
                    datos["DIRECCIÓN"].strip(),

                    datos["TIPO_DE_ENTREGA"].strip(),
                    numero,
                    datos["PED_COMPRA"].strip(),
            )
            try:
            # Intenta ejecutar la consulta con los valores
            
                cursor.execute(consulta,valores)
                # resultados = cursor.fetchall()
                # for fila in resultados:
                #     print(fila)
            # Realiza la confirmación para guardar los cambios en la base de datos
                print("Inserción exitosa")
            except Exception as e:
                # Captura cualquier excepción que ocurra durante la inserción
                print("Error durante la inserción:", str(e))
            finally:
                # Cierra el cursor y la conexión
                cursor.close()
                return 1
        else:
            guardar_log("YA SE ENCUENTRA EN LA BASE PEDIDO INTERNO "+str(numero),1)
            print("YA SE ENCUENTRA EN LA BASE PEDIDO INTERNO "+str(numero))
            return 0
    except Exception as e:
        guardar_log("ERROR AL GUARDAR CABECERA "+str(e),1)
        print("ERROR AL GUARDAR CABECERA "+str(e))
        return 0

def Guadar_detalle(datos,PEDIDO):
    print("*******************************")
    print(datos)
    val = 0
    err = 0
    for dato in datos:
        valores = (
                    PEDIDO,
                    dato["ORD"].strip(),           
                    dato["CODIGO"].strip(),
                    dato["DESCRIPCION"].strip(),
                    dato["UNIDAD"].strip(),
                    dato["POR_DESPACHAR"].strip(),
                    dato["DESPACHADA"].strip(),
                    dato["ENTREGADA"].strip(),
            )
        
        consulta = """
                INSERT INTO guias_detalle (
                    PEDIDO_INTERNO,
                    ORD,
                    CODIGO,
                    DESCRIPCION,
                    UNIDAD,
                    POR_DESPACHAR,
                    DESPACHADA,
                    ENTREGADA
                    ) VALUES (
                        %s, %s, %s , %s,
                        %s, %s, %s , %s
                        )
            """
        try:
                cursor = conexion.cursor()
                cursor.execute(consulta,valores)
                conexion.commit()
                print("detalle guardado exitosa")
        except Exception as e:
                # Captura cualquier excepción que ocurra durante la inserción
                print("Error durante la inserción:", str(e))
                # return 0
                err = err + 1
        finally:
                # Cierra el cursor y la conexión
                cursor.close()
                val = val + 1
                # return 1
    
    if err == 0:
        return 1
    else:
        return 0

def Guardar_Guias(array_datos):
    cantidad_datos = 0
    guardar_log("GUARDANDO DATOS",1)
    for row in array_datos:
        cabecera = row[0]
        detalle =  row[1]
        cantidad_datos = cantidad_datos + 1
        cab = Guardar_Cabecera(cabecera)
        if(cab == 1):
            pedido  =cabecera["PEDIDO_INTERNO"].strip()
            pedido = pedido.replace(" ","")
            Guadar_detalle(detalle,pedido)
    print("FINALIZADO DATOS")
    guardar_log("FINALIZADO DATOS "+str(cantidad_datos) ,1)

def mover_archivos():
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/pdf'
    carpeta_destino  = 'C:/xampp/htdocs/svsysback/scrapy/pdf_old'

    archivos = os.listdir(directorio)
    for archivo in archivos:
        ruta_origen = os.path.join(directorio, archivo)
        ruta_destino = os.path.join(carpeta_destino, archivo)
        shutil.move(ruta_origen, ruta_destino)

def ejecutar():
    # for i in range(3):
    Leer_pdf()
    time.sleep(5)
    print("MOVIENDO ARCHIVOS")
    mover_archivos()

ejecutar()
