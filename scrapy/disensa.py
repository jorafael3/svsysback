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


options = Options()
options.add_argument("--start-maximized")
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

    btn_b = driver.find_element(By.XPATH, '//*[@id="aceptar_Cookies"]')
    btn_b.click()
    time.sleep(5)

  
    desde = driver.find_element(By.XPATH, '//*[@id="inputfdesde"]')
    desde.clear()
    desde.send_keys("14/09/2023")

# Activa el evento "change" del campo (esto puede ser necesario según la implementación)
    driver.execute_script("arguments[0].dispatchEvent(new Event('change'))", desde)
    time.sleep(1)
    # hasta = driver.find_element(By.XPATH, '//*[@id="inputfhasta"]')
    # hasta.send_keys("19/09/2023")
    # time.sleep(5)
    btn_b = driver.find_element(By.XPATH, '//*[@id="btn_consultar_docs_electronicos"]')
    btn_b.click()
    time.sleep(2)
    while True:

        tabla = driver.find_element(By.XPATH,'//*[@id="divResultados"]/div/div[1]/table/tbody')
        filas = tabla.find_elements(By.TAG_NAME,'tr')

        for fila in filas:
        # # Encuentra las celdas de cada fila
            celdas = fila.find_elements(By.TAG_NAME,'td')
            # print(celdas)

        # # Itera a través de las celdas de la fila
            print("++++++++++++++++++++++++")
            for celda in celdas:
        #         # Realiza la acción deseada con el contenido de la celda
                if(celda.text != ""):
                    try:
                        link = celda.find_element(By.CLASS_NAME,"link_docs_electronicos_pedido")
                        link.click()
                        time.sleep(1)
                        print("---------------------")
                        print(link)
                        print("---------------------")
                    except:
                        print("")
                    print(celda.text)
        try:
            boton_siguiente = driver.find_element(By.XPATH,'//*[@id="divPaginacao2"]/ul')
            btn = boton_siguiente.find_elements(By.TAG_NAME,'li')
            for b in btn:
                if((b.text).lower() == "siguiente"):
                    print(b.text.lower())
                    b.click()
                    break
               
        except:
        # Si no se encuentra el botón "Siguiente" o no se puede hacer clic, sal del bucle
            print("YA NO HAY BOTON")
            break
    driver.quit()

login()
# Buscar_Documentos()
