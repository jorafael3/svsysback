import json
import csv
import pandas as pd
import re
# Read the data from the TXT file
with open('C:/xampp/htdocs/svsysback/scrapy/SALVACERO_DOS.txt', 'r') as file:
    lines = file.readlines()

# Process the remaining lines and create a list of dictionaries
cabecera = []
data_list = []
for index,line in enumerate(lines):
    cadena = str(line)
    cadena = line.replace(' ', '-')
    cadena = ' '.join(cadena.split())
    cadena = cadena.replace(' ', '/')
    if(cadena != "\x00"):
        d = {"data":cadena}
        data_list.append(d)

lista = []
for index,cad in enumerate(data_list):
    # cadena_resultante = re.sub(r'(?<=/)/(?=/)', '0', cad)
    # print(cad["data"])
    cadena = cad["data"]
    cadena = cadena.split("/")
    tmp = []
    for i,data in enumerate(cadena):
        if(index ==0):
            cabecera.append(data)
        else:
            if(data == "\x00"):
                data = 0
            print(data)
            b = {
                "data"+str(i):data
            }
            tmp.append(b)
    c = {
        "datos"+str(index):tmp
    }
    lista.append(c)
    


for d in lista:
    print(d)


# result_list = []
# for data_dict in data_list:
#     row_dict = dict(zip(cabecera, data_dict['data'].split('-')))
#     result_list.append(row_dict)

# # Print the first result dictionary as an example
# if result_list:
#     for d in result_list:
#         print(d)
  
# with open('archivo.csv', 'w') as csv_file:
#     for linea in lines:
#         # Reemplaza cualquier otro delimitador con comas (,)
#         linea = linea.replace('\t', ',').replace(' ', ',')
#         # Escribe la línea en el archivo CSV
#         csv_file.write(linea)

# print("Conversión completada con éxito.")





