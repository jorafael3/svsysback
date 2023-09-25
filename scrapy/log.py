import os
import datetime


def guardar_log(dato,val):
    hora_actual = datetime.datetime.now()
    hora_actual = hora_actual.strftime("%Y-%m-%d %H:%M")
    texto = hora_actual
    try:
        # print(str(hora_actual)+":"+dato)
        archivo = 'scrapy/datos.txt'
        if val == 0:
            if os.path.exists(archivo):
            # Borra el archivo
                os.remove(archivo)
                #print(f"El archivo {archivo} ha sido borrado.")

        with open(archivo, 'a') as archivo:
            # datos = ["Dato 1", "Dato 2", "Dato 3"]
            # for dato in datos:
            archivo.write(str(hora_actual)+":"+dato + "\n")


    except IOError:
        print("Error al escribir en el archivo.")

