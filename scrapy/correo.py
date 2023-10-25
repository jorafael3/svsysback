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
# If modifying these scopes, delete the file token.json.
SCOPES = ['https://www.googleapis.com/auth/gmail.readonly']
credential = 'C:\\xampp\\htdocs\\svsysback\\scrapy\\client_secret.json'

datos_correo=[]

conexion = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="svsys"
    )

def main():
    """Shows basic usage of the Gmail API.
    Lists the user's Gmail labels.
    pip install --upgrade google-api-python-client google-auth-httplib2 google-auth-oauthlib

    """
    creds = None
    start_date_str = ''  # Inicializa las variables
    end_date_str = ''
    # The file token.json stores the user's access and refresh tokens, and is
    # created automatically when the authorization flow completes for the first
    # time.
    if os.path.exists('token.json'):
        creds = Credentials.from_authorized_user_file('token.json', SCOPES)
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

        end_date = datetime.utcnow()
        print(end_date)
        start_date = end_date - timedelta(days=7)

        # Convierte las fechas a formato RFC3339
        start_date_str = start_date.strftime('%Y-%m-%dT%H:%M:%SZ')
        end_date_str = end_date.strftime('%Y-%m-%dT%H:%M:%SZ')

    try:
        # Call the Gmail API
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
            msg = service.users().messages().get(userId='me', id=message['id']).execute()
            print("++++++++++++++++++++++++++++++")
            # print(msg["snippet"])
            Obtener_Datos(msg["snippet"])
            # print(f'Subject: {msg["subject"]}')
            # print(f'From: {msg["from"]}')
            # print(f'Date: {msg["internalDate"]}')
        Guardar_Datos()

    except HttpError as error:
        # TODO(developer) - Handle errors from gmail API.
        print(f'An error occurred: {error}')

def Obtener_Datos(texto):
    # if "REPORTE DE VEHÍCULO EN PLANTA" in texto:
        # texto = """GU - REPORTE DE VEHÍCULO EN PLANTA Srs. SALVACERO CIA. LTDA., El Vehículo : GBO7782 Ha salido de la planta Guayaquil :SA-16-09 13:19 Orden #505420274 con 400 Saco Cemento holcim Fuer. Nota: Favor no
        # No se encontraron todos los datos."""
        # lineas = texto.split('\n')
        if "REPORTE DE VEHÍCULO EN PLANTA" in texto:
            placa_pattern = r"Vehículo : (\w+)"
            orden_pattern = r"Orden #(\d+)"
            # Buscamos los datos en el texto
            placa_match = re.search(placa_pattern, texto)
            orden_match = re.search(orden_pattern, texto)
            datos = {
                "placa": placa_match.group(1),
                "orden": orden_match.group(1)
            }
            datos_correo.append(datos)

            # # Inicializar variables para almacenar los datos
            #         placa = None
            #         orden = None
            #         fecha = None
            #         detalle = None

            #         # Recorrer cada línea y buscar los datos
            #         for linea in lineas:
            #             if "Vehículo :" in linea:
            #                 placa = linea.split(":")[1].strip()
            #             elif "Orden #" in linea:
            #                 orden = linea.split("#")[1].split()[0]
            #                 detalle = ' '.join(linea.split(" con ")[1:])
            #             elif ":" in linea:
            #                 fecha = linea.split(":")[1].strip()

            #         # Comprobar si se encontraron todos los datos
            #         if placa and orden and fecha and detalle:
            #             datos = {
            #                 "placa": placa,
            #                 "orden": orden,
            #                 "fecha": fecha,
            #                 "detalle": detalle
            #             }
            #             print(datos)
            #         else:
            #             print("No se encontraron todos los datos.")

def Guardar_Datos():
    for data in datos_correo:
        print(data)
        val = Validar_Cabecera(data["orden"])
        if val == 0:
            consulta = """
                INSERT INTO gui_guias_placa (
                    pedido_interno,
                    placa
                    ) VALUES (
                        %s, %s
                    )
                    """
            valores = (
                    data["orden"].strip(), 
                    data["placa"].strip(),           
            )
            try:
                cursor = conexion.cursor()
            # Intenta ejecutar la consulta con los valores
            
                cursor.execute(consulta,valores)
                # resultados = cursor.fetchall()
                # for fila in resultados:
                #     print(fila)
            # Realiza la confirmación para guardar los cambios en la base de datos
                print("Inserción exitosa")
                conexion.commit()
            except Exception as e:
                # Captura cualquier excepción que ocurra durante la inserción
                print("Error durante la inserción:", str(e))
            finally:
                # Cierra el cursor y la conexión
                cursor.close()
                return 1
        else:
            print("YA GUARDADO")
def Validar_Cabecera(numero):
    # print(numero)
    cursor = conexion.cursor()
    consulta = 'SELECT pedido_interno FROM gui_guias_placa WHERE pedido_interno = %s'
    valores = (numero,)
    cursor.execute(consulta,valores)
    resultados = cursor.fetchall()
    return len(resultados)

if __name__ == '__main__':
    main()


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
