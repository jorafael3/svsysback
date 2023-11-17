from __future__ import print_function

import os.path
import re
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from datetime import datetime, timedelta
import mysql.connector
from datetime import datetime
import email
import smtplib
import ssl
import time
from email import encoders
from email.mime.base import MIMEBase
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
# If modifying these scopes, delete the file token.json.
SCOPES = ['https://www.googleapis.com/auth/gmail.readonly']
credential = 'C:\\xampp\\htdocs\\svsysback\\scrapy\\client_secret.json'

datos_correo=[]

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

def main():
    try:
        creds = None
        start_date_str = ''  # Inicializa las variables
        end_date_str = ''
    
        if os.path.exists('token.json'):
            creds = Credentials.from_authorized_user_file('token.json', SCOPES)
        else:
            guardar_log("PIDIENDO ACCESO A CORREO")
            enviar_correo("PIDIENDO ACCESO A CORREO")
        # If there are no (valid) credentials available, let the user log in.
        if not creds or not creds.valid:
            if creds and creds.expired and creds.refresh_token:
                creds.refresh(Request())
            else:
                flow = InstalledAppFlow.from_client_secrets_file(
                    credential, SCOPES)
                creds = flow.run_local_server(port=0)
            # Save the credentials for the next run
            with open('token.json', 'w') as token:
                token.write(creds.to_json())


        try:
            # Obtén la fecha actual
            end_date = datetime.utcnow()
            # Calcula la fecha de inicio para "hoy"
            start_date = end_date - timedelta(days=7)

            # Convierte las fechas a formato RFC3339
            start_date_str = start_date.strftime('%Y-%m-%dT%H:%M:%SZ')
            end_date_str = end_date.strftime('%Y-%m-%dT%H:%M:%SZ')
        
            service = build('gmail', 'v1', credentials=creds)
            results = service.users().messages().list(
                userId='me', 
                labelIds=['INBOX'],
                # q=f'after:{start_date_str} before:{end_date_str}'
            ).execute()
            messages = results.get('messages', [])
            if not messages:
                print('No messages found in the inbox.')
                return

            print('Messages in the inbox:')
            for message in messages:
                # print(message)
                msg = service.users().messages().get(userId='me', id=message['id']).execute()
                from_header = [header['value'] for header in msg['payload']['headers'] if header['name'] == 'From']
                if from_header:
                        sender_email = from_header[0]
                # print(f'Sender: {sender_email}')
                print("++++++++++++++++++++++++++++++")
                # print(msg)
                if(sender_email == "ecu-noreply-apps@holcim.com"):
                    Obtener_Datos(msg["snippet"])
                # print(f'Subject: {msg["subject"]}')
                # print(f'From: {msg["from"]}')
                # print(f'Date: {msg["internalDate"]}')
            # print(datos_correo)
            Guardar_Datos()

        except HttpError as error:
            # TODO(developer) - Handle errors from gmail API.
            print(f'An error occurred: {error}')
            guardar_log(f'An error occurred: {error}')
            enviar_correo(str(error))

    except Exception as error:
        guardar_log("ERROR " + str(error))
        enviar_correo(str(error))

def Obtener_Datos(texto):
        # print(texto)
        fecha_hora_actual = datetime.now()
        fecha_hora_formateada = fecha_hora_actual.strftime("%Y")
    # if "REPORTE DE VEHÍCULO EN PLANTA" in texto:
        # texto = """Srs. SALVACERO CIA. LTDA., El Vehículo : GBO7782 Ha salido de la planta Guayaquil :MIE-25-10 15:30 Orden # 505610385 con 400 Saco Cemento Holcim Fuer. Nota: Favor no utilizar la opción de responder a"""
        # lineas = texto.split('\n')
        if "SALVACERO " in texto:
            placa_pattern = r"Vehículo : (\w+)"
            orden_pattern = r"Orden # (\d+)"
            fecha_pattern = r"Guayaquil :(\S+ \S+)"
            # Buscamos los datos en el texto
            placa_match = re.search(placa_pattern, texto)
            orden_match = re.search(orden_pattern, texto)
            fecha_match = re.search(fecha_pattern, texto)
            if fecha_match:
                fecha = fecha_match.group(1)
                fecha_original = fecha
                fecha = fecha.split("-")
                anio = fecha_hora_formateada
                mes = fecha[2].split(" ")[0]
                dia = fecha[1]
                hora = fecha[2].split(" ")[1]
                fecha = anio+"-"+mes+"-"+dia+" "+hora
                # print(fecha)
            else:
                print("No se encontró la fecha en el texto.")
                fecha_hora_formateada = fecha_hora_actual.strftime("%Y-%m-%d %H:%M")
                fecha = fecha_hora_formateada
                fecha_original = ""

            # print(orden_match.group(1))
            placa = placa_match.group(1)
            orden = orden_match.group(1)
            # fecha = fecha_match.group(1)
           
            placa = f"{placa[:3]}-{placa[3:]}"
            if len(orden) > 9:
                orden = f"{orden[:9]}-{orden[9:]}"
            datos = {
                "placa": placa,
                "orden": orden,
                "fecha":fecha,
                "fecha_original":fecha_original
            }
            print(datos)
            datos_correo.append(datos)

def Guardar_Datos():
    # print(datos_correo)
    datos_insertado = []
    errores = "0"
    act = ""
    for data in datos_correo:
        # print(data)
        val = Validar_Cabecera(data["orden"])
        if val == 0:
            print(data)

            consulta = """
                INSERT INTO gui_guias_placa (
                    pedido_interno,
                    placa,
                    FECHA_SALE_PLANTA,
                    FECHA_TEXTO_ORIGINAL
                    ) VALUES (
                        %s, %s,%s, %s
                    )
                    """
            valores = (
                    data["orden"].strip(), 
                    data["placa"].strip(),           
                    data["fecha"],           
                    data["fecha_original"],           
            )
            try:
            # Intenta ejecutar la consulta con los valores
                cursor = conexion.cursor()
                cursor.execute(consulta,valores)
                # resultados = cursor.fetchall()
                # for fila in resultados:
                #     print(fila)
            # Realiza la confirmación para guardar los cambios en la base de datos
                print("Inserción exitosa")
                datos_insertado.append(data)
                conexion.commit()
            except Exception as e:
                # Captura cualquier excepción que ocurra durante la inserción
                print("Error durante la inserción:", str(e))
                errores = str(e)
            finally:
                # Cierra el cursor y la conexión
                cursor.close()
                # return 1
        else:
            print("YA GUARDADO")
            act = "DATOS YA GUARDADOS"

    texto_insert = "DATOS INSERTADOS "+ str(len(datos_insertado)) +" "+ act + " - ERRORES " + errores
    print("1")
    guardar_log(texto_insert)

def Validar_Cabecera(numero):
    # print(numero)
    cursor = conexion.cursor()
    consulta = 'SELECT pedido_interno FROM gui_guias_placa WHERE pedido_interno = %s'
    valores = (numero,)
    cursor.execute(consulta,valores)
    resultados = cursor.fetchall()
    return len(resultados)

def enviar_correo(error_mensaje):
    port = 465  
    smtp_server = "smtp.gmail.com"
    gmail_user = 'jalvaradoe3@gmail.com'
    gmail_password = 'zwgp wqdl pihr eqom'
    sender_email = "jalvaradoe3@gmail.com"
    message = MIMEMultipart("multipart")
    text ="ERROR AL EJECUTAR " + str(error_mensaje)
    try:
        part1 = MIMEText(text, "plain")
        message.attach(part1)
        correo = 'jalvaradoe3@gmail.com'
        message["Subject"] = "svsys correos aut"
        message["From"] = sender_email
        message["To"] = correo
        try:
            with smtplib.SMTP_SSL(smtp_server, port) as server:
                server.ehlo()
                # server.starttls() # Secure the connection
                server.login(gmail_user, gmail_password)
                server.sendmail(sender_email, correo, message.as_string())
                server.close()
        except Exception as e:
            print(e)
        else:
            print("")

    except Exception as e:
            print(e)

def guardar_log(dato):
    hora_actual = datetime.now()
    hora_actual = hora_actual.strftime("%Y-%m-%d %H:%M")
    texto = hora_actual
    try:
        # print(str(hora_actual)+":"+dato)
        archivo = 'scrapy/datos_correos.txt'

        with open(archivo, 'a') as archivo:
            # datos = ["Dato 1", "Dato 2", "Dato 3"]
            # for dato in datos:
            archivo.write(str(hora_actual)+":"+dato + "\n")


    except IOError:
        print("Error al escribir en el archivo.")



if __name__ == '__main__':
    # while True:
    main()
        # time.sleep(180)



# from google_auth_oauthlib.flow import InstalledAppFlow
# from googleapiclient.discovery import build

# # Define el alcance de acceso a Gmail
# SCOPES = ['https://www.googleapis.com/auth/gmail.readonly']

# flow = InstalledAppFlow.from_client_secrets_file('C:\\xampp\\htdocs\\svsysback\\scrapy\\client_secret.json', SCOPES)
# creds = flow.run_local_server(port=0)


# service = build('gmail', 'v1', credentials=creds)

# results = service.users().messages().list(userId='me', labelIds=['INBOX']).execute()
# messages = results.get('messages', [])

# if not messages:
#     print('No se encontraron mensajes.')
# else:
#     print('Mensajes:')
#     for message in messages:
#         # msg = service.users().messages().get(userId='me', id=message['id']).execute()
#         # print(f"Asunto: {msg['subject']}, De: {msg['from']}")
