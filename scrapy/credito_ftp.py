import paramiko
import os
from pyunpack import Archive
import patoolib
import re
from datetime import datetime
import shutil


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


def main():
    # listar_archivos_sftp()
    # desconmprimir()
    mover_archivos()


main()