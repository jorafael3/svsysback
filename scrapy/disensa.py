import time
import click
from numpy import can_cast
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
driver.get('https://www.portaldisensa.com/b2b_new/init.do')
time.sleep(3)

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
time.sleep(100)

driver.get('https://www.portaldisensa.com/b2b_new/b2b/zDocumentosElectronicos.do')

driver.quit()

