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
import tabula
import re
import zipfile

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
        campos = ["FACTURA","FECHA EMISIÓN","FECHA DE EMISIÓN","TIPO DE ENTREGA","TELÉFONO","FECHA VALIDEZ","PEDIDO INTERNO","PED. COMPRA"]
        for clave, valor in datos.items():
            print(f'{clave}: {valor}')
            time.sleep(0)
            # for campo in campos:
            if valor in campos:
            #    partes = valor.split(campo)
               print(partes)
               campo1 = partes[0].strip()
               campo2 = partes[1].strip()
            #    datos_a[clave.replace(" ","_")] = valor
               datos_a[valor.replace(" ","_")] = campo2
            # else:
            #    datos_a[clave.replace(" ","_")] = valor


        return datos_a

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
    
    print(array_datos)
Leer_pdf()

