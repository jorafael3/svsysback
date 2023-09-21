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

options = Options()
options.add_argument("--start-maximized")
# options.add_argument("--headless")
# options.add_argument('--no-sandbox')
options.add_experimental_option('excludeSwitches', ['enable-logging'])
path = os.path.dirname(os.path.abspath(__file__))
prefs = {"download.default_directory": path}
options.add_experimental_option("prefs", prefs)
ser = Service()
driver = webdriver.Chrome(service=ser, options=options)
# driver = webdriver.Chrome()
conexion = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="svsys"
    )

def login():
    driver.get('https://www.portaldisensa.com/b2b_new/init.do')
    time.sleep(2)
    ced = driver.find_element(By.XPATH, '//*[@id="userid"]')
    ced.send_keys("EC304772")
    time.sleep(1)
    passw = driver.find_element(By.XPATH, '//*[@id="nolog_password"]')
    passw.send_keys("Jgarcia28@")
    time.sleep(1)
    token = driver.find_element(By.XPATH, '//*[@id="token"]')
    token.send_keys("979766")
    time.sleep(1)
    btn = driver.find_element(By.XPATH, '//*[@id="login"]')
    btn.click()
    time.sleep(2)
    Buscar_Documentos()

def Buscar_Documentos():
    # driver.get('file:///C:/xampp/htdocs/svsysback/scrapy/dis.html')
    driver.get('https://www.portaldisensa.com/b2b_new/b2b/zDocumentosElectronicos.do')
    time.sleep(1)
    try:
        btn_b = driver.find_element(By.XPATH, '//*[@id="aceptar_Cookies"]')
        btn_b.click()
        time.sleep(5)
    except:
        print("")
  
    desde = driver.find_element(By.XPATH, '//*[@id="inputfdesde"]')
    desde.clear()
    desde.send_keys("14/09/2023")

    # Activa el evento "change" del campo (esto puede ser necesario según la implementación)
    # driver.execute_script("arguments[0].dispatchEvent(new Event('change'))", desde)
    # time.sleep(1)
    # hasta = driver.find_element(By.XPATH, '//*[@id="inputfhasta"]')
    # hasta.send_keys("19/09/2023")
    # time.sleep(5)
   
    # time.sleep(2)
    time.sleep(2)
    # form = driver.find_element(By.NAME,'ZConsultaDocumentosElectronicosForm')
    driver.execute_script('arguments[0].value="500"', driver.find_element(By.NAME,'rowsPerPage'))
    # time.sleep(30)
    time.sleep(2)
    # time.sleep(1)
    btn_b = driver.find_element(By.XPATH, '//*[@id="btn_consultar_docs_electronicos"]')
    btn_b.click()
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
    for fila in filas:
    # Encuentra las celdas de cada fila
        celdas = fila.find_elements(By.TAG_NAME,'td')
        # print(celdas) 
    # Itera a través de las celdas de la fila
        print("+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++")
        for celda in celdas:
    # Realiza la acción deseada con el contenido de la celda
            if(celda.text != ""):
                try:
                    link = celda.find_element(By.CLASS_NAME,"link_docs_electronicos_pedido")
                    link.click()
                    time.sleep(1)
                    print("-----------------------------------")
                    print(link)
                    print("-----------------------------------")
                except:
                    print("")
                print(celda.text)
    driver.quit()

# login()
# Buscar_Documentos()

def Obtener_tabla(texto):
    texto_limpio = texto.replace("PRODUCTO", "").replace("CANTIDAD", "")
        # Dividir el texto en líneas
    lineas = texto_limpio.strip().split('\n')

        # Separar las palabras en cada línea
    palabras = lineas[0].split()

    # Obtener la cantidad "POR DESPACHAR"
    cantidad_por_despachar = (palabras[-1])
        # Crear un diccionario con los datos
    unidad = palabras[-2]

    informacion_producto = {
            'ORD': palabras[0],
            'CÓDIGO': palabras[1],
            'DESCRIPCIÓN': ' '.join(palabras[2:-2]),
            'UNIDAD': unidad,
            'POR DESPACHAR': cantidad_por_despachar,
            'DESPACHADA': "",
            'ENTREGADA': ""
        } 
    return informacion_producto

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

        return datos_b

def Leer_pdf():

    array_datos = []
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/pdf'
    archivos_en_directorio = os.listdir(directorio)
    for buscar_zip in archivos_en_directorio:
    # # Imprime el nombre de archivo (puedes hacer otra cosa con él)
        if(buscar_zip.endswith(".zip") or buscar_zip.endswith(".ZIP")):
            ruta_zip = (directorio+"/"+ buscar_zip)
            print(ruta_zip)
            with zipfile.ZipFile(ruta_zip, 'r') as archivo_zip:
                archivo_zip.extractall(directorio+"/")


    for nombre_archivo in archivos_en_directorio:
        if(nombre_archivo.endswith(".pdf") or nombre_archivo.endswith(".PDF")):
            with open('C:/xampp/htdocs/svsysback/scrapy/pdf/'+nombre_archivo, 'rb') as pdf_file:
                # Crea un objeto PdfFileReader para leer el PDF
                pdf_reader = PdfReader(pdf_file)
                page = pdf_reader.pages[0]
                # print(page.extract_text())
                texto = page.extract_text()
                # Definir un patrón utilizando una expresión regular
                # Encuentra la posición de "COMENTARIOS:"
                posicion_comentarios = texto.find("COMENTARIOS:")
                eliminiar_duplicado = texto[:posicion_comentarios].strip()
                # print(eliminiar_duplicado)
                posicion_cabecera = eliminiar_duplicado.find("ENTREGADA")
                tabla = eliminiar_duplicado[:posicion_cabecera].strip()
                # print(tabla)
                cabecera = eliminiar_duplicado[posicion_cabecera + len("ENTREGADA"):].strip()
                datos_tabla = Obtener_tabla(tabla)
                datos_cabecera = Obtener_cabecera(cabecera)
                # print(datos_tabla)
                # print(datos_cabecera)
                lista = [datos_cabecera,datos_tabla,]
                array_datos.append(lista)
    print("*******************************************")
    # print(array_datos)
    Guardar_Guias(array_datos)

def Guardar_Cabecera(datos):
    cursor = conexion.cursor()
    consulta = """
        INSERT INTO GUIAS (
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
    fecha_emision = datetime.strptime(datos["FECHA_EMISIÓN"].strip(), "%d.%m.%Y")
    fecha_emision = fecha_emision.strftime("%Y/%m/%d")

    fecha_val = datetime.strptime(datos["FECHA_VALIDEZ"].strip(), "%d.%m.%Y")
    fecha_val = fecha_val.strftime("%Y/%m/%d")

    valores = (
            fecha_emision, 
            datos["FACTURA"].strip(),           
            datos["TELÉFONO"].strip(),
            fecha_val,

            datos["CLIENTE"].strip(),
            datos["RUC"].strip(),
            datos["SOLICITANTE"].strip(),
            datos["DIRECCION"].strip(),

            datos["PTO_DE_PARTIDA"].strip(),
            datos["PTO_DE_LLEGADA"].strip(),
            datos["DIRECCIÓN"].strip(),

            datos["TIPO_DE_ENTREGA"].strip(),
            datos["PEDIDO_INTERNO"].strip(),
            datos["PED_COMPRA"].strip(),
    )
    try:
    # Intenta ejecutar la consulta con los valores
        cursor.execute(consulta,valores)
        # resultados = cursor.fetchall()
        # for fila in resultados:
        #     print(fila)
    # Realiza la confirmación para guardar los cambios en la base de datos
        conexion.commit()
        print("Inserción exitosa")
    except Exception as e:
        # Captura cualquier excepción que ocurra durante la inserción
        print("Error durante la inserción:", str(e))
    finally:
        # Cierra el cursor y la conexión
        cursor.close()




def Guardar_Guias(array_datos):
    # print(array_datos)
#     cursor = conexion.cursor()
#     consulta = "SELECT * FROM usuarios"
#     cursor.execute(consulta)
#     resultados = cursor.fetchall()
#     for fila in resultados:
#         print(fila)
# # Cierra el cursor y la conexión
#     cursor.close()
#     conexion.close()
    for row in array_datos:
        cabecera = row[0]
        detalle =  row[1]
        Guardar_Cabecera(cabecera)
        # for val in row[0]: 
        #     print(val[0]["MATRIZ"])

Leer_pdf()

 