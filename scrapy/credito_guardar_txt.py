import mysql.connector
import os.path


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
# )

# ARRAY_TOTAL = []

def Lista_Archivos_SIG1():
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/cartera1'
    lista = os.listdir(directorio)
    return lista

def Lista_Archivos_SIG2():
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/cartera2'
    lista = os.listdir(directorio)
    return lista

def Lista_Ingresada_2():
    cursor = conexion.cursor()
    sql = """
        select * from cli_creditos_mora_archivo_ingresado ccm
        where cartera = 2  
    """
    cursor.execute(sql)
    resultados = cursor.fetchall()
    lista = []
    for res in resultados:
        # print(res[1])
        lista.append(res[1])
        # return (res[0])
    return lista

def Lista_Ingresada_1():
    cursor = conexion.cursor()
    sql = """
        select * from cli_creditos_mora_archivo_ingresado ccm
        where cartera = 1
    """
    cursor.execute(sql)
    resultados = cursor.fetchall()
    lista = []
    for res in resultados:
        # print(res[1])
        lista.append(res[1])
        # return (res[0])
    return lista

def quitar_x00(array):
    return [elemento.replace('\x00', '') if isinstance(elemento, str) else elemento for elemento in array]

def mora_2(ruta):
# Read the data from the TXT file
    ARRAY_TOTAL = []
    with open(ruta, 'r') as file:
        lines = file.readlines()

    # Process the remaining lines and create a list of dictionaries
    cabecera = []
    data_list = []
    for index,line in enumerate(lines[1:]):
        # print("************************")
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
        # print("-------------------------------")
        array1_sin_x00 = quitar_x00(t)
        # print(array1_sin_x00)
        ARRAY_DATOS.append(array1_sin_x00)
    for d in ARRAY_DATOS:
        # print(d)
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
        # print(b)
        ARRAY_TOTAL.append(b)


        # val = Validar_Datos(d[0],d[1])
        # if val == 0:
        #     print("GUARDANDO")
        #     Guardar_Datos(b)
        # else:
        #     print("YA EXISTE")

    return ARRAY_TOTAL

def mora_1(ruta):
# Read the data from the TXT file
    ARRAY_TOTAL = []
    with open(ruta, 'r') as file:
        lines = file.readlines()

    # Process the remaining lines and create a list of dictionaries
    cabecera = []
    data_list = []
    for index,line in enumerate(lines[1:]):
        # print("************************")
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
        # print("-------------------------------")
        array1_sin_x00 = quitar_x00(t)
        # print(array1_sin_x00)
        ARRAY_DATOS.append(array1_sin_x00)
    for d in ARRAY_DATOS:
        # print(d)
        b = {
            "FechaCorte":d[0],
            "Identificacion":d[1],
            "Cliente":d[2],
            "NumeroCredito":d[3],
            "NumeroCreditoNuevo":"",
            "Oficina": d[4],
            "OrigenCredito": d[5],
            "EstadoCredito": d[6],
            "TipoCartera": d[7],
            "TipoTablaAmortizacion": d[8],
            "TipoGracia": "",
            "PeriodosGracia": d[9],
            "MontoOriginal": d[10],
            "PlazoOriginal": d[11],
            "Saldo": d[12],
            "ValorAPagar": d[13],
            "ValorCuota": d[14],
            "FechaDesembolso": d[15],
            "FechaPrimerVencimiento": d[16],
            "FechaVencimiento": d[17],
            "FechaCancelacion": "",
            "Atraso": d[18],
            "AtrasoMaximo": d[19],
            "CuotasRestantes": d[20],
            "CuotaImpaga": d[21],
            "TipoCancelacion": "",
            "DispositivoNotificacion": d[22],
            "Celular_01": "",
            "Celular_02": "",
            "Celular_03": "",
            "TelefonoNegocio_01": "",
            "TelefonoNegocio_02": "",
            "TelefonoNegocio_03": "",
            "TelefonoDomicilio_01": "",
            "TelefonoDomicilio_02": "",
            "TelefonoDomicilio_03": "",
            "TelefonoLaboral_01": "",
            "TelefonoLaboral_02": "",
            "TelefonoLaboral_03": "",
        }
        # print(b)
        ARRAY_TOTAL.append(b)


        # val = Validar_Datos(d[0],d[1])
        # if val == 0:
        #     print("GUARDANDO")
        #     Guardar_Datos(b)
        # else:
        #     print("YA EXISTE")

    return ARRAY_TOTAL

def Guardar(DATOS,archivo,cartera,nombre_tabla):
    cursor = conexion.cursor()
    # Nombre de la tabla en la base de datos
    # Consulta SQL para insertar datos en la tabla
    consulta_insertar = f"""
        INSERT INTO {nombre_tabla} (
            FechaCorte, Identificacion, Cliente, NumeroCredito, NumeroCreditoNuevo, Oficina,
            OrigenCredito, EstadoCredito, TipoCartera, TipoTablaAmortizacion, TipoGracia,
            PeriodosGracia, MontoOriginal, PlazoOriginal, Saldo, ValorAPagar, ValorCuota,
            FechaDesembolso, FechaPrimerVencimiento, FechaVencimiento, FechaCancelacion,
            Atraso, AtrasoMaximo, CuotasRestantes, CuotaImpaga, TipoCancelacion,
            DispositivoNotificacion, Celular_01, Celular_02, Celular_03,
            TelefonoNegocio_01, TelefonoNegocio_02, TelefonoNegocio_03,
            TelefonoDomicilio_01, TelefonoDomicilio_02, TelefonoDomicilio_03,
            TelefonoLaboral_01, TelefonoLaboral_02, TelefonoLaboral_03
        ) 
        VALUES (
            %(FechaCorte)s, %(Identificacion)s, %(Cliente)s, %(NumeroCredito)s, %(NumeroCreditoNuevo)s,
            %(Oficina)s, %(OrigenCredito)s, %(EstadoCredito)s, %(TipoCartera)s, %(TipoTablaAmortizacion)s,
            %(TipoGracia)s, %(PeriodosGracia)s, %(MontoOriginal)s, %(PlazoOriginal)s, %(Saldo)s, %(ValorAPagar)s,
            %(ValorCuota)s, %(FechaDesembolso)s, %(FechaPrimerVencimiento)s, %(FechaVencimiento)s,
            %(FechaCancelacion)s, %(Atraso)s, %(AtrasoMaximo)s, %(CuotasRestantes)s, %(CuotaImpaga)s,
            %(TipoCancelacion)s, %(DispositivoNotificacion)s, %(Celular_01)s, %(Celular_02)s, %(Celular_03)s,
            %(TelefonoNegocio_01)s, %(TelefonoNegocio_02)s, %(TelefonoNegocio_03)s, %(TelefonoDomicilio_01)s,
            %(TelefonoDomicilio_02)s, %(TelefonoDomicilio_03)s, %(TelefonoLaboral_01)s, %(TelefonoLaboral_02)s,
            %(TelefonoLaboral_03)s
        )
    """
    # Insertar los datos en la tabla de la base de datos
    cursor.executemany(consulta_insertar, DATOS)
    # Confirmar los cambios y cerrar la conexión



    nombre_tabla2 = 'cli_creditos_mora_archivo_ingresado'

    consulta_insertar2 = f"""
        INSERT INTO {nombre_tabla2} (
            archivo, cartera
        ) 
        VALUES (
             %s, %s
        )
    """
    valores = (
            archivo, 
            cartera        
            )
    cursor.execute(consulta_insertar2,valores)

    conexion.commit()
    # conexion.close()
    print("Los datos se han insertado correctamente en la tabla de MySQL.")



def main():

    SIG1 = Lista_Archivos_SIG1()
    ING1 = Lista_Ingresada_1()
    # print(ING2)
    for s1 in SIG1:
        if s1 in ING1:
            print("YA GUARDADO", s1)
        else:
            ruta = 'C:/xampp/htdocs/svsysback/scrapy/cartera1/'+s1
            nombre_tabla = 'cli_creditos_mora_1'
            print(ruta)
            DATOS = mora_1(ruta)
            Guardar(DATOS,s1,1,nombre_tabla)
    print("------------------------------------------")
    SIG2 = Lista_Archivos_SIG2()
    ING2 = Lista_Ingresada_2()
    for s2 in SIG2:
        if s2 in ING2:
            print("YA GUARDADO", s2)
        else:
            ruta = 'C:/xampp/htdocs/svsysback/scrapy/cartera2/'+s2
            nombre_tabla = 'cli_creditos_mora_2'
            print(ruta)
            DATOS = mora_2(ruta)
            Guardar(DATOS,s2,2,nombre_tabla)

main()