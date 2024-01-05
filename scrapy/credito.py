import json
import csv
import pandas as pd
import re
import mysql.connector

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


def quitar_x00(array):
    return [elemento.replace('\x00', '') if isinstance(elemento, str) else elemento for elemento in array]


def mora():
# Read the data from the TXT file
    with open('C:/xampp/htdocs/svsysback/scrapy/cartera1/SALVACERO DOS.txt', 'r') as file:
        lines = file.readlines()

    # Process the remaining lines and create a list of dictionaries
    cabecera = []
    data_list = []
    for index,line in enumerate(lines[1:]):
        print("************************")
        cadena = str(line)
        cadena = line.replace(' ', '-')
        cadena = ' '.join(cadena.split())
        cadena = cadena.replace(' ', '/')
        if(cadena != "\x00"):
            # print(cadena)
            temp = []
            cadena = cadena.split("/")
            for i,data in enumerate(cadena):
                if(data == "\x00"):
                    data = -1
                # print(data)
                temp.append(data)
            data_list.append(temp)
        
    # decoded_data = [item.decode('utf-16le') for item in data_list]
    ARRAY_DATOS = []
    for t in data_list:
        print("-------------------------------")
        array1_sin_x00 = quitar_x00(t)
        # print(array1_sin_x00)
        ARRAY_DATOS.append(array1_sin_x00)

    ARRAY_TOTAL = []
    for d in ARRAY_DATOS:
        b = {
            "FechaCorte":d[0],
            "Identificacion":d[1],
            "Cliente":d[2],
            "NumeroCredito":d[3],
            "NumeroCreditoNuevo":d[4],
            "Oficina":d[5],
            "OrigenCredito":d[6],
            "EstadoCredito":d[7],
            "TipoCartera":d[8],
            "TipoTablaAmortizacion":d[9],
            "TipoGracia":d[10],
            "PeriodosGracia":d[11],
            "MontoOriginal":d[12],
            "PlazoOriginal":d[13],
            "Saldo":d[14],
            "ValorAPagar":d[15],
            "ValorCuota":d[16],
            "FechaDesembolso":d[17],
            "FechaPrimerVencimiento":d[18],
            "FechaVencimiento":d[19],
            "FechaCancelacion":d[20],
            "Atraso":d[21],
            "AtrasoMaximo":d[22],
            "CuotasRestantes":d[23],
            "CuotaImpaga":d[24],
            "TipoCancelacion":d[25],
            "DispositivoNotificacion":d[26],
            "Celular_01":d[27],
            "Celular_02":d[28],
            "Celular_03":d[29],
            "TelefonoNegocio_01":d[30],
            "TelefonoNegocio_02":d[31],
            "TelefonoNegocio_03":d[32],
            "TelefonoDomicilio_01":d[33],
            "TelefonoDomicilio_02":d[34],
            "TelefonoDomicilio_03":d[35],
            "TelefonoLaboral_01":d[36],
            "TelefonoLaboral_02":d[37],
            "TelefonoLaboral_03":d[38],
        }
        ARRAY_TOTAL.append(b)
        val = Validar_Datos(d[0],d[1])
        if val == 0:
            print("GUARDANDO")
            Guardar_Datos(b)
        else:
            print("YA EXISTE")


    # print(ARRAY_TOTAL)

def Validar_Datos(fecha,ruc):
    cursor = conexion.cursor()
    sql = """
        select * from cli_creditos_mora
        where FechaCorte =  '"""+fecha+"""' and Identificacion =  '"""+ruc+"""'
    """
    valores = (fecha,ruc,)
    cursor.execute(sql)
    resultados = cursor.fetchall()
    lista = []
    for res in resultados:
        # print(res)
        lista.append(res)
    return len(lista)
    # print(resultados)

    # count = resultados[0]
    # print(count)


def Guardar_Datos(datos):
    cursor = conexion.cursor()
    sql = """
        INSERT INTO cli_creditos_mora (
            FechaCorte, Identificacion, Cliente, NumeroCredito, NumeroCreditoNuevo,
            Oficina, OrigenCredito, EstadoCredito, TipoCartera, TipoTablaAmortizacion,
            TipoGracia, PeriodosGracia, MontoOriginal, PlazoOriginal, Saldo, ValorAPagar,
            ValorCuota, FechaDesembolso, FechaPrimerVencimiento, FechaVencimiento,
            FechaCancelacion, Atraso, AtrasoMaximo, CuotasRestantes, CuotaImpaga,
            TipoCancelacion, DispositivoNotificacion, Celular_01, Celular_02, Celular_03,
            TelefonoNegocio_01, TelefonoNegocio_02, TelefonoNegocio_03, TelefonoDomicilio_01,
            TelefonoDomicilio_02, TelefonoDomicilio_03, TelefonoLaboral_01, TelefonoLaboral_02,
            TelefonoLaboral_03
        ) VALUES (
            %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
            %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
            %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
            %s, %s, %s, %s, %s, %s, %s, %s, %s
        )
        """
    values = (
        datos["FechaCorte"],
        datos["Identificacion"],
        datos["Cliente"],
        datos["NumeroCredito"],
        datos["NumeroCreditoNuevo"],
        datos["Oficina"],
        datos["OrigenCredito"],
        datos["EstadoCredito"],
        datos["TipoCartera"],
        datos["TipoTablaAmortizacion"],
        datos["TipoGracia"],
        datos["PeriodosGracia"],
        datos["MontoOriginal"],
        datos["PlazoOriginal"],
        datos["Saldo"],
        datos["ValorAPagar"],
        datos["ValorCuota"],
        datos["FechaDesembolso"],
        datos["FechaPrimerVencimiento"],
        datos["FechaVencimiento"],
        datos["FechaCancelacion"],
        datos["Atraso"],
        datos["AtrasoMaximo"],
        datos["CuotasRestantes"],
        datos["CuotaImpaga"],
        datos["TipoCancelacion"],
        datos["DispositivoNotificacion"],
        datos["Celular_01"],
        datos["Celular_02"],
        datos["Celular_03"],
        datos["TelefonoNegocio_01"],
        datos["TelefonoNegocio_02"],
        datos["TelefonoNegocio_03"],
        datos["TelefonoDomicilio_01"],
        datos["TelefonoDomicilio_02"],
        datos["TelefonoDomicilio_03"],
        datos["TelefonoLaboral_01"],
        datos["TelefonoLaboral_02"],
        datos["TelefonoLaboral_03"],
    )
    # print(datos.values())
    try:
        # Intenta ejecutar la consulta con los valores
            
        cursor.execute(sql,values)
        conexion.commit()

        # resultados = cursor.fetchall()
        # for fila in resultados:
        #     print(fila)
        # Realiza la confirmación para guardar los cambios en la base de datos
        print("Inserción exitosa")
    except Exception as e:
        # Captura cualquier excepción que ocurra durante la inserción
        print("Error durante la inserción:", str(e))

mora()




