import paramiko
import os
from pyunpack import Archive
import patoolib
import re
from datetime import datetime
import shutil
import mysql.connector

conexion = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="svsys"
    )

# Datos de conexión al servidor SFTP
sftp_host = 'ftp.solidario-online.com'
sftp_usuario = 'SALVACERO_SIG'
sftp_contraseña = 'QhhL31QX'
sftp_puerto = 22

# Directorio en el servidor SFTP al que deseas acceder
directorio_sftp = '/'
directorio_local = "scrapy/cartera_rar/"

# Función para listar archivos en el directorio SFTP
def listar_archivos_sftp():
    # Crear una instancia de cliente SFTP
    cliente = paramiko.SSHClient()
    cliente.load_system_host_keys()
    cliente.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    
    # Conectar al servidor SFTP
    cliente.connect(sftp_host, sftp_puerto, username=sftp_usuario, password=sftp_contraseña)
    
    # Abrir un canal SFTP
    sftp = cliente.open_sftp()
    
    # Cambiar al directorio deseado en el servidor SFTP
    sftp.chdir(directorio_sftp)
    
    # Listar archivos en el directorio
    archivos = sftp.listdir()
    
    # Imprimir los nombres de los archivos
    for archivo in archivos:
        if archivo.endswith('.rar'):
            print(archivo)
            ruta_remota = os.path.join(directorio_sftp, archivo)
            ruta_local = os.path.join(directorio_local, archivo)
            sftp.get(ruta_remota, ruta_local)
            print(f"Archivo '{archivo}' descargado correctamente.")
    
    # Cerrar la conexión SFTP
    sftp.close()
    
    # Cerrar la conexión SSH
    cliente.close()

# Llamar a la función para listar archivos en el directorio SFTP

def desconmprimir():
    # Directorio donde se encuentran los archivos RAR descargados
    directorio_descargas = "scrapy/cartera_rar/"

    # Directorio donde se guardarán los archivos descomprimidos
    directorio_descomprimidos = "scrapy/cartera_carpetas/"

    # Contraseña para descomprimir los archivos RAR
    contraseña = 'cC05a1V@c3R0'

    # Crear el directorio de descomprimidos si no existe
    if not os.path.exists(directorio_descomprimidos):
        os.makedirs(directorio_descomprimidos)

    for archivo_rar in os.listdir(directorio_descargas):
        if archivo_rar.endswith('.rar'):

            patron_fecha = r'\d{8}'
            nuevo_nombre = re.search(patron_fecha, archivo_rar)
            fecha = nuevo_nombre.group()
            fecha_obj = datetime.strptime(fecha, "%Y%m%d")
            fecha_formateada = fecha_obj.strftime("%Y-%m-%d")

            ruta_archivo_rar = os.path.join(directorio_descargas, archivo_rar)
            nombre_archivo_descomprimido = fecha_formateada # Cambia la extensión al tipo de archivo que sea
            
            ruta_salida = os.path.join(directorio_descomprimidos, nombre_archivo_descomprimido)  # Elimina la extensión .rar del nombre del directorio
            print(ruta_salida)

            try:
            # Descomprimir el archivo RAR con contraseña
                patoolib.extract_archive(ruta_archivo_rar, outdir=ruta_salida, password=contraseña)
                
                # # Renombrar el archivo descomprimido
                # nuevo_ruta_archivo = os.path.join(ruta_salida, nombre_archivo_descomprimido)
                # viejo_ruta_archivo = os.path.join(ruta_salida, archivo_rar[:-4] + ".txt")  # Archivo generado automáticamente
                # os.rename(viejo_ruta_archivo, nuevo_ruta_archivo)
                
                # print(f"Archivo '{archivo_rar}' descomprimido correctamente.")
            except Exception as e:
                pass
                # print(f"Error al descomprimir el archivo '{archivo_rar}': {e}")


def mover_archivos():

    # Directorio donde se encuentran las carpetas
    directorio_principal = "scrapy/cartera_carpetas/"

    # Directorio donde se guardarán los archivos .txt renombrados
    directorio_destino = "scrapy/cartera3/"

    # Recorrer las carpetas dentro del directorio principal
    for carpeta in os.listdir(directorio_principal):
        carpeta_actual = os.path.join(directorio_principal, carpeta)
        
        # Verificar si es una carpeta
        if os.path.isdir(carpeta_actual):
            # Buscar archivos .txt dentro de la carpeta
            for archivo in os.listdir(carpeta_actual):
                if archivo.endswith('.txt'):
                    # Renombrar el archivo .txt con el nombre de la carpeta
                    nuevo_nombre = f"{carpeta}.txt"
                    ruta_original = os.path.join(carpeta_actual, archivo)
                    ruta_destino = os.path.join(directorio_destino, nuevo_nombre)
                    
                    # Mover el archivo renombrado al directorio destino
                    shutil.move(ruta_original, ruta_destino)
                    print(f"Archivo '{archivo}' renombrado y movido a '{directorio_destino}'.")

def Ya_ingresado(archivo):
    cursor = conexion.cursor()
    sql = """
        select * from cli_creditos_mora_archivo_ingresado
        where archivo =  '"""+archivo+"""' 
        and cartera = 2
    """
    cursor.execute(sql)
    resultados = cursor.fetchall()
    # if(len(res))
    return (len(resultados))

def leer_Archivos():

    directorio_principal = "scrapy/cartera3/"
    for archivo_rar in os.listdir(directorio_principal)[:1]:

        ingreso = Ya_ingresado(archivo_rar)
        # print(ingreso)
        if ingreso == 0:
            print("ARCHIVO YA INGRESADO")
        else:
            with open(directorio_principal+archivo_rar, 'r') as file:
                lineas = file.readlines()
                # print(lineas)
            campos = [
                "FechaCorte", "Identificacion", "Cliente", "NumeroCredito", "NumeroCreditoNuevo",
                "Oficina", "OrigenCredito", "EstadoCredito", "TipoCartera", "TipoTablaAmortizacion",
                "TipoGracia", "PeriodosGracia", "MontoOriginal", "PlazoOriginal", "Saldo", "ValorAPagar",
                "ValorCuota", "FechaDesembolso", "FechaPrimerVencimiento", "FechaVencimiento",
                "FechaCancelacion", "Atraso", "AtrasoMaximo", "CuotasRestantes", "CuotaImpaga",
                "TipoCancelacion", "DispositivoNotificacion", "Celular_01", "Celular_02", "Celular_03",
                "TelefonoNegocio_01", "TelefonoNegocio_02", "TelefonoNegocio_03", "TelefonoDomicilio_01",
                "TelefonoDomicilio_02", "TelefonoDomicilio_03", "TelefonoLaboral_01", "TelefonoLaboral_02",
                "TelefonoLaboral_03"
            ]
    # Lista para almacenar los diccionarios
            lista_diccionarios = []
            # Iterar sobre las líneas leídas del archivo
            # for linea in lineas:
            #     # Dividir la línea en sus campos utilizando la coma como delimitador
            #     valores = linea.strip().split(',')
            #     # Crear un diccionario para almacenar los campos de la línea
            #     diccionario = {}
            #     # Asignar cada valor a su campo correspondiente en el diccionario
            #     for campo, valor in zip(campos, valores):
            #         diccionario[campo] = valor
            #     # Agregar el diccionario a la lista
            #     lista_diccionarios.append(diccionario)
            # # Mostrar la lista de diccionarios resultante
            for linea in lineas:
                # print(linea)
                # Dividir la línea en sus campos utilizando la coma como delimitador
                valores = linea.strip().split(',')
                if(len(valores) <10):
                    valores = linea.strip().split('\t')

                # print((valores))
                if(valores[6].strip() == "CANCELADO"):

                    diccionario = {
                    "FechaCorte": valores[0],
                    "Identificacion": valores[1],
                    "Cliente": valores[2],
                    "NumeroCredito": valores[3],
                    "NumeroCreditoNuevo": valores[22],
                    "Oficina": valores[4],
                    "OrigenCredito": valores[5],
                    "EstadoCredito": valores[6],
                    "TipoCartera": valores[7],
                    "TipoTablaAmortizacion": valores[8],
                    "TipoGracia": "-1",
                    "PeriodosGracia": 0,
                    "MontoOriginal": valores[9],
                    "PlazoOriginal": valores[10],
                    "Saldo": valores[11],
                    "ValorAPagar": valores[12],
                    "ValorCuota": valores[13],
                    "FechaDesembolso": valores[14],
                    "FechaPrimerVencimiento": valores[15],
                    "FechaVencimiento": valores[16],
                    "FechaCancelacion": valores[17],
                    "Atraso": valores[18],
                    "AtrasoMaximo": valores[19],
                    "CuotasRestantes": valores[20],
                    "CuotaImpaga": 0, #FALTA CAMPO
                    "TipoCancelacion": valores[21],
                    "DispositivoNotificacion": valores[26], 
                    "Celular_01": valores[27],
                    "Celular_02": valores[28],
                    "Celular_03": valores[29],
                    "TelefonoNegocio_01": valores[30],
                    "TelefonoNegocio_02": valores[31],
                    "TelefonoNegocio_03": valores[32],
                    "TelefonoDomicilio_01": valores[33],
                    "TelefonoDomicilio_02": valores[34],
                    "TelefonoDomicilio_03": valores[35],
                    "TelefonoLaboral_01": valores[36],
                    "TelefonoLaboral_02": valores[37],
                    "TelefonoLaboral_03": valores[38]
                }
                    print(diccionario)
    
    # Agregar el diccionario a la lista
                    lista_diccionarios.append(diccionario)


            # print((lista_diccionarios))

            tabla = "cli_creditos_mora_2"
            archivo = archivo_rar
            cartera = 2
            Guardar(lista_diccionarios,archivo,cartera,tabla)
    
def Guardar(DATOS,archivo,cartera,nombre_tabla):
    cursor = conexion.cursor()
    # Nombre de la tabla en la base de datos
    # Consulta SQL para insertar datos en la tabla
    consulta_insertar = f"""
        INSERT INTO {nombre_tabla} 
        (
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
        VALUES 
        (
            %(FechaCorte)s, %(Identificacion)s, %(Cliente)s, %(NumeroCredito)s, %(NumeroCreditoNuevo)s,
            %(Oficina)s, %(OrigenCredito)s, %(EstadoCredito)s, %(TipoCartera)s, %(TipoTablaAmortizacion)s,
            %(TipoGracia)s, %(PeriodosGracia)s, %(MontoOriginal)s, %(PlazoOriginal)s, %(Saldo)s, %(ValorAPagar)s,
            %(ValorCuota)s, %(FechaDesembolso)s, %(FechaPrimerVencimiento)s, %(FechaVencimiento)s,
            %(FechaCancelacion)s, %(Atraso)s, %(AtrasoMaximo)s, %(CuotasRestantes)s, %(CuotaImpaga)s,
            %(TipoCancelacion)s, %(DispositivoNotificacion)s, 
            %(Celular_01)s, %(Celular_02)s, %(Celular_03)s, 
            %(TelefonoNegocio_01)s, %(TelefonoNegocio_02)s, %(TelefonoNegocio_03)s,
            %(TelefonoDomicilio_01)s, %(TelefonoDomicilio_02)s, %(TelefonoDomicilio_03)s,
            %(TelefonoDomicilio_01)s, %(TelefonoDomicilio_02)s, %(TelefonoDomicilio_03)s

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
    # listar_archivos_sftp()
    # desconmprimir()
    # mover_archivos()
    leer_Archivos()


main()