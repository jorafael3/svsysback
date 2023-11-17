#!/usr/bin/env python3.5
import time
import click
# from numpy import can_cast
import pandas as pd
from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
import json
from selenium.webdriver.chrome.service import Service
import math
import os
import PyPDF2
from PyPDF2 import PdfReader
import re
import zipfile
import mysql.connector
from datetime import datetime
from log import *
from datetime import datetime, timedelta

options = Options()
options.add_argument("--start-maximized")
# options.add_argument("--headless")
# options.add_argument('--no-sandbox')
options.add_experimental_option('excludeSwitches', ['enable-logging'])
path = os.path.dirname(os.path.abspath(__file__))+"\pdf"
# dire = 'C:/xampp/htdocs/svsysback/scrapy/pdf'
prefs = {"download.default_directory": path}
options.add_experimental_option("prefs", prefs)
ser = Service()
driver = webdriver.Chrome(service=ser, options=options)
# driver = webdriver.Chrome()
# conexion = mysql.connector.connect(
#     host="localhost",
#     user="root",
#     password="",
#     database="svsys"
#     )
conexion = mysql.connector.connect(
    host="gator4166.hostgator.com",
    user="salvacer_jorge",
    password="Equilivre3*",
    database="salvacer_svsys"
    )

# print(path+"\pdf")

def Limpiar_directorio():
    # directorio = 'C:/xampp/htdocs/svsysback/scrapy/pdf/'
    # archivos_en_directorio = os.listdir(directorio)
    # guardar_log("LIMPIANDO DIRECTORIO",0)
    # if(len(archivos_en_directorio) > 0):
    #     for archivo in archivos_en_directorio:
    #         ruta_archivo = os.path.join(directorio, archivo)
    #         os.remove(ruta_archivo)
    # else:  
    #     pass

    login()

def login():
    guardar_log("INICIANDO SCRAPY",0)
    driver.get('https://www.portaldisensa.com/b2b_new/init.do')
    time.sleep(2)
    ced = driver.find_element(By.XPATH, '//*[@id="userid"]')
    ced.send_keys("EC304772")
    time.sleep(1)
    guardar_log("USUARIO INGRESADO",1)
    passw = driver.find_element(By.XPATH, '//*[@id="nolog_password"]')
    passw.send_keys("Jgarcia28@")
    time.sleep(1)
    guardar_log("CONTRASEÑA INGRESADA",1)
    token = driver.find_element(By.XPATH, '//*[@id="token"]')
    token.send_keys("979766")
    time.sleep(1)
    guardar_log("TOKEN INGRESADO",1)
    btn = driver.find_element(By.XPATH, '//*[@id="login"]')
    btn.click()
    time.sleep(2)
    guardar_log("INICIANDO SESION",1)
    Buscar_Documentos()

def Buscar_Documentos():
    # driver.get('file:///C:/xampp/htdocs/svsysback/scrapy/dis.html')
    try:

        driver.get('https://www.portaldisensa.com/b2b_new/b2b/zDocumentosElectronicos.do')
        time.sleep(1)
        guardar_log("SESION INICIADA",1)
        try:
            btn_b = driver.find_element(By.XPATH, '//*[@id="aceptar_Cookies"]')
            btn_b.click()
            time.sleep(5)
            guardar_log("COOKIES ACEPTADAS",1)
        except:
            print("")
        time.sleep(2)
        try:
            desde = driver.find_element(By.XPATH, '//*[@id="inputfdesde"]')
            desde.clear()
            fecha_actual = datetime.now()
            # Calcula la fecha de hace 7 días
            fecha_hace_7_dias = fecha_actual - timedelta(days=7)

            # Formatea la fecha en "dd/mm/yyyy"
            fecha_formateada = fecha_hace_7_dias.strftime("%d/%m/%Y")
            desde.send_keys('01/10/2023')
            guardar_log("FECHA CAMBIADA",1)
        except:
            pass
        time.sleep(2)

        driver.execute_script('arguments[0].value="500"', driver.find_element(By.NAME,'rowsPerPage'))
        guardar_log("CANTIDAD DE ELEMENTOS EN TABLA CAMBIADO A 500",1)

        time.sleep(2)
        # time.sleep(1)
        btn_b = driver.find_element(By.XPATH, '//*[@id="btn_consultar_docs_electronicos"]')
        btn_b.click()
        guardar_log("CONSULTANDO TABLA DOCUMENTOS",1)
        # form.submit()
        time.sleep(5)
        # current_page_input = form.find_element(By.NAME,'currentPage')
        # total_pages_input = driver.find_element(By.XPATH,'//*[@id="formAux"]/input[2]').get_attribute("value")
        # print(total_pages_input)
        # current_page_input.send_keys('1')  # Establece el valor de "currentPage" a 1
        # total_pages_input.send_keys('10.0')
        # while True:


        tabla = driver.find_element(By.XPATH,'//*[@id="divResultados"]/div/div[1]/table/tbody')
        filas = tabla.find_elements(By.TAG_NAME,'tr') 
        contador_elemento = 0
        contador_archivos = 0  
        for fila in filas:
        # Encuentra las celdas de cada fila
            celdas = fila.find_elements(By.TAG_NAME,'td')
            # print(celdas) 
        # Itera a través de las celdas de la fila
            print("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
            
            for celda in celdas:
        # Realiza la acción deseada con el contenido de la celda
                contador_elemento = contador_elemento + 1
                if(celda.text != ""):
                    try:
                        link = celda.find_element(By.CLASS_NAME,"link_docs_electronicos_pedido")
                        link.click()
                        guardar_log("DESCARGANDO ARCHIVO "+str(celda.text),1)
                        time.sleep(2)
                        print("-----------------------------------")
                        print(link)
                        print("-----------------------------------")
                        contador_archivos = contador_archivos + 1
                    except:
                        print("NO HAY ARCHIVO POR DESCARGAR",1)
                    print(celda.text)
        driver.quit()
        guardar_log("ELEMENTOS EN TABLA RECORRIDOS "+ str(contador_elemento),1)
        guardar_log("ARCHIVOS DESCARGADOS "+ str(contador_archivos),1)
        guardar_log("SCRAPY REALIZADO",1)
        print("SCRAPY REALIZADO")
        # Leer_pdf()
    except:
        login()


#************************************
#********* LEER DOCUMENTOS **********
#************************************
def Obtener_tabla(texto):
    texto_limpio = texto.replace("PRODUCTO", "").replace("CANTIDAD", "")

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


# def Leer_pdf():

#     array_datos = []
#     directorio = 'C:/xampp/htdocs/svsysback/scrapy/pdf'
#     archivos_en_directorio = os.listdir(directorio)
#     if(len(archivos_en_directorio) > 0):

#         for buscar_zip in archivos_en_directorio:
#         # # Imprime el nombre de archivo (puedes hacer otra cosa con él)
#             if(buscar_zip.endswith(".zip") or buscar_zip.endswith(".ZIP")):
#                 guardar_log("BUSCANDO ZIP",1)
#                 ruta_zip = (directorio+"/"+ buscar_zip)
#                 # print(ruta_zip)
#                 with zipfile.ZipFile(ruta_zip, 'r') as archivo_zip:
#                     archivo_zip.extractall(directorio+"/")
#                     guardar_log("EXTRAYENDO ZIP",1)

#         # time.sleep(3)
#         guardar_log("BUSCANDO ARCHIVOS PARA ANALIZAR",1)
#         for nombre_archivo in archivos_en_directorio:
#             if(nombre_archivo.endswith(".pdf") or nombre_archivo.endswith(".PDF")):
#                 try:
#                     with open('C:/xampp/htdocs/svsysback/scrapy/pdf/'+nombre_archivo, 'rb') as pdf_file:
#                         # Crea un objeto PdfFileReader para leer el PDF
#                         guardar_log("ANALIZANDO "+str(nombre_archivo),1)
#                         pdf_reader = PdfReader(pdf_file)
#                         page = pdf_reader.pages[0]
#                         # print(page.extract_text())
#                         texto = page.extract_text()
#                         guardar_log("TEXTO EXTRAIDO",1)
#                         # Definir un patrón utilizando una expresión regular
#                         # Encuentra la posición de "COMENTARIOS:"
#                         posicion_comentarios = texto.find("COMENTARIOS:")
#                         eliminiar_duplicado = texto[:posicion_comentarios].strip()
#                         # print(eliminiar_duplicado)
#                         posicion_cabecera = eliminiar_duplicado.find("ENTREGADA")
#                         tabla = eliminiar_duplicado[:posicion_cabecera].strip()
#                         # print(tabla)
#                         cabecera = eliminiar_duplicado[posicion_cabecera + len("ENTREGADA"):].strip()
#                         guardar_log("ELIMINANDO DUPLICADOS",1)
#                         guardar_log("OBTENIENDO DATOS TABLA",1)
#                         datos_tabla = Obtener_tabla(tabla)
#                         # time.sleep(1)
#                         guardar_log("OBTENIENDO DATOS CABECERA",1)
#                         datos_cabecera = Obtener_cabecera(cabecera)
#                         # print(datos_tabla)
#                         # print(datos_cabecera)
#                         lista = [datos_cabecera,datos_tabla,]
#                         array_datos.append(lista)
#                         # print("*******************************************")
#                         # print(array_datos)
                   
#                 except:
#                     guardar_log("NO SE PUDO ABRIR EL ARCHIVO "+str(nombre_archivo),1)

#         guardar_log("PREPARANDO PARA GUARDAR DATOS",1)
#         Guardar_Guias(array_datos)
#     else:
#         guardar_log("NO HAY ARCHIVOS EN EL DIRECTORIO",1)

# def Guardar_Cabecera(datos):
#     try: 
#         cursor = conexion.cursor()
#         numero = datos["PEDIDO_INTERNO"].strip()
#         numero = numero.replace(" ","")

#         val = Validar_Cabecera(numero)
#         if val == 0:
#             consulta = """
#                 INSERT INTO guias (
#                     FECHA_DE_EMISION,
#                     FACTURA,
#                     TELEFONO,
#                     FECHA_VALIDEZ,

#                     CLIENTE,
#                     CLIENTE_RUC,
#                     SOLICITANTE,
#                     DIRECCION_1,

#                     PTO_DE_PARTIDA,
#                     PTO_DE_LLEGADA,
#                     DIRECCION_2,

#                     TIPO_DE_ENTREGA,
#                     PEDIDO_INTERNO,
#                     PED_COMPRA
#                     ) VALUES (
#                         %s, %s, %s , %s,
#                         %s, %s, %s , %s,
#                         %s, %s, %s , 
#                         %s, %s, %s
#                         )
#             """
#             # fecha_emision = datetime.strptime(datos["FECHA_EMISIÓN"].strip(), "%d.%m.%Y")
#             # fecha_emision = fecha_emision.strftime("%Y/%m/%d")

#             # fecha_val = datetime.strptime(datos["FECHA_VALIDEZ"].strip(), "%d.%m.%Y")
#             # fecha_val = fecha_val.strftime("%Y/%m/%d")

#             valores = (
#                     datos["FECHA_EMISIÓN"].strip(), 
#                     datos["FACTURA"].strip(),           
#                     datos["TELÉFONO"].strip(),
#                     datos["FECHA_VALIDEZ"].strip(),

#                     datos["CLIENTE"].strip(),
#                     datos["RUC"].strip(),
#                     datos["SOLICITANTE"].strip(),
#                     datos["DIRECCION"].strip(),

#                     datos["PTO_DE_PARTIDA"].strip(),
#                     datos["PTO_DE_LLEGADA"].strip(),
#                     datos["DIRECCIÓN"].strip(),

#                     datos["TIPO_DE_ENTREGA"].strip(),
#                     numero,
#                     datos["PED_COMPRA"].strip(),
#             )
#             try:
#             # Intenta ejecutar la consulta con los valores
            
#                 cursor.execute(consulta,valores)
#                 # resultados = cursor.fetchall()
#                 # for fila in resultados:
#                 #     print(fila)
#             # Realiza la confirmación para guardar los cambios en la base de datos
#                 print("Inserción exitosa")
#             except Exception as e:
#                 # Captura cualquier excepción que ocurra durante la inserción
#                 print("Error durante la inserción:", str(e))
#             finally:
#                 # Cierra el cursor y la conexión
#                 cursor.close()
#                 return 1
#         else:
#             guardar_log("YA SE ENCUENTRA EN LA BASE PEDIDO INTERNO "+str(numero),1)
#             return 0
#     except Exception as e:
#         guardar_log("ERROR AL GUARDAR CABECERA "+str(e),1)
#         return 0

# def Validar_Cabecera(numero):
#     # print(numero)
#     cursor = conexion.cursor()
#     consulta = 'SELECT PEDIDO_INTERNO FROM guias WHERE PEDIDO_INTERNO = %s'
#     valores = (numero,)
#     cursor.execute(consulta,valores)
#     resultados = cursor.fetchall()
#     return len(resultados)

# def Guadar_detalle(datos,PEDIDO):

#     val = 0
#     err = 0
#     for dato in datos:
#         valores = (
#                     PEDIDO,
#                     dato["ORD"].strip(),           
#                     dato["CODIGO"].strip(),
#                     dato["DESCRIPCION"].strip(),
#                     dato["UNIDAD"].strip(),
#                     dato["POR_DESPACHAR"].strip(),
#                     dato["DESPACHADA"].strip(),
#                     dato["ENTREGADA"].strip(),
#             )
        
#         consulta = """
#                 INSERT INTO guias_detalle (
#                     PEDIDO_INTERNO,
#                     ORD,
#                     CODIGO,
#                     DESCRIPCION,
#                     UNIDAD,
#                     POR_DESPACHAR,
#                     DESPACHADA,
#                     ENTREGADA
#                     ) VALUES (
#                         %s, %s, %s , %s,
#                         %s, %s, %s , %s
#                         )
#             """
#         try:
#                 cursor = conexion.cursor()
#                 cursor.execute(consulta,valores)
#                 conexion.commit()
#                 print("detalle guardado exitosa")
#         except Exception as e:
#                 # Captura cualquier excepción que ocurra durante la inserción
#                 print("Error durante la inserción:", str(e))
#                 # return 0
#                 err = err + 1
#         finally:
#                 # Cierra el cursor y la conexión
#                 cursor.close()
#                 val = val + 1
#                 # return 1
    
#     if err == 0:
#         return 1
#     else:
#         return 0

# def Guardar_Guias(array_datos):
#     cantidad_datos = 0
#     guardar_log("GUARDANDO DATOS",1)
#     for row in array_datos:
#         cabecera = row[0]
#         detalle =  row[1]
#         cantidad_datos = cantidad_datos + 1
#         cab = Guardar_Cabecera(cabecera)
#         if(cab == 1):
#             pedido  =cabecera["PEDIDO_INTERNO"].strip()
#             pedido = pedido.replace(" ","")
#             Guadar_detalle(detalle,pedido)
#     print("FINALIZADO DATOS")
#     guardar_log("FINALIZADO DATOS "+str(cantidad_datos) ,1)

login()
# Leer_pdf()

