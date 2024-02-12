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
import base64
from apiclient import errors
from credito import mora
from credito_guardar_txt import main_credito
# If modifying these scopes, delete the file token.json.
SCOPES = ['https://www.googleapis.com/auth/gmail.readonly']
credential = 'C:\\xampp\\htdocs\\svsysback\\scrapy\\client_secret.json'

# conexion = mysql.connector.connect(
#     host="localhost",
#     user="root",
#     password="",
#     database="svsys"
# )

conexion = mysql.connector.connect(
    host="gator4166.hostgator.com",
    user="salvacer_jorge",
    password="Equilivre3*",
    database="salvacer_svsys"
)
# LISTA_SIG2 = []

def Fecha_maxima():
    cursor = conexion.cursor()
    sql = """
        select max(FechaCorte)  from cli_creditos_mora ccm  
    """
    cursor.execute(sql)
    resultados = cursor.fetchall()
    lista = []
    for res in resultados:
        # print(res[0])
        # lista.append(res)
        return (res[0])

def Lista_Archivos_SIG1():
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/cartera1'
    lista = os.listdir(directorio)
    return lista

def Lista_Archivos_SIG2():
    directorio = 'C:/xampp/htdocs/svsysback/scrapy/cartera2'
    lista = os.listdir(directorio)
    return lista

def main(lista2,lista1):
    LISTA_SIG1 = lista1
    LISTA_SIG2 = lista2
    try:
        creds = None
        start_date_str = ''  # Inicializa las variables
        end_date_str = ''
    
        if os.path.exists('token.json'):
            creds = Credentials.from_authorized_user_file('token.json', SCOPES)
        else:
            pass
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
            nextPageToken = None
            con = True
            found_attachment = False
            # while con == True:
            results = service.users().messages().list(
                userId='me', 
                labelIds=['INBOX'],
                pageToken=nextPageToken
                # q=f'after:{start_date_str} before:{end_date_str}'
            ).execute()
            messages = results.get('messages', [])
            if not messages:
                print('No messages found in the inbox.')
                return
            print('Messages in the inbox:',len(messages))

                # print('FECHA MAXIMA GUARDADA')
                # print(Fecha_maxima())
                # print(len(messages))
                
            for message in messages:
                    msg = service.users().messages().get(userId='me', id=message['id']).execute()
                    from_header = [header['value'] for header in msg['payload']['headers'] if header['name'] == 'From']
                    if from_header:
                            sender_email = from_header[0]
                    subject_header = [header['value'] for header in msg['payload']['headers'] if header['name'] == 'Subject']
                    if subject_header:
                        email_subject = subject_header[0]
                    
                    date_header = [header['value'] for header in msg['payload']['headers'] if header['name'] == 'Date']
                    if date_header:
                            email_date = date_header[0]

                    if(sender_email == "sig@solidario.fin.ec"):
                        # print(msg["snippet"])
                        print("++++++++++++++++++++++++++++++++++++++") 
                        print("CORREO", email_date)

                        path_SIG1 = "scrapy/cartera1/"
                        path_SIG2 = "scrapy/cartera2/"
                        # lista_reporte = ["Reporte SALVACERO DOS"]
                        lista_reporte = ["Reporte SALVACERO DOS","Reporte SALVACERO CIA LTDA"]


                        if email_subject in lista_reporte:
                            print(f'REPORTE: {email_subject}')
                            email_date_obj = datetime.strptime(email_date, '%d %b %Y %H:%M:%S %z')
                            formatted_date = email_date_obj.strftime('%Y-%m-%d')
                            print(f'Date: {formatted_date}')

                            try:
                                # attachment_name = 'SALVACERODOS'+str(con)+'_'+str(formatted_date)+'.txt'
                                attachment_name = str(formatted_date)+'.txt'
                                path_cartera = ""
                                lista_cartera = []
                                if email_subject == "Reporte SALVACERO DOS":
                                    path_cartera = path_SIG2
                                    lista_cartera = LISTA_SIG2
                                elif email_subject == "Reporte SALVACERO CIA LTDA":
                                    path_cartera = path_SIG1
                                    lista_cartera = LISTA_SIG1
                                # print("found_attachment:", lista_cartera)

                                if attachment_name in lista_cartera:
                                    print("PROCESO TERMINADO")
                                    # found_attachment = True
                                    # break
                                else:
                                    # print(attachment_name,"")
                                    with open(path_cartera+attachment_name, 'w'):
                                        pass  # No es necesario escribir nada
                                
                                    message = service.users().messages().get(userId='me', id=message['id']).execute()
                                    for part in message['payload']['parts']:
                                        if 'filename' in part:
                                            if part['filename'] == "SALVACERO DOS.txt" or part['filename'] == "SALVACERO.txt":
                                                if 'data' in part['body']:
                                                    data=part['body']['data']
                                                else:
                                                    att_id=part['body']['attachmentId']
                                                    att=service.users().messages().attachments().get(userId="me", messageId=message['id'],id=att_id).execute()
                                                    data=att['data']
                                                # data = part['body']['data']
                                                try:
                                                    file_data = base64.urlsafe_b64decode(data.encode('UTF-8'))
                                                    decoded_content = file_data
                                                    # print(f'Contenido del archivo adjunto:\n{decoded_content}')
                                                    # Guarda el archivo adjunto en el directorio actual
                                                    with open(path_cartera+attachment_name, 'wb') as f:
                                                        f.write(decoded_content)
                                                except Exception as e:
                                                    print(f"Error al decodificar o escribir el archivo: {e}")
                                                    # Añadir más detalles del error según sea necesario
                                                    # mora()
                                    
                            except errors.HttpError as error:
                                    print('An error occurred: %s' % error)  
                # nextPageToken = results.get('nextPageToken')

        except HttpError as error:
            # TODO(developer) - Handle errors from gmail API.
            print(f'An error occurred: {error}')
            # guardar_log(f'An error occurred: {error}')
            # enviar_correo(str(error))

    except Exception as error:
        print(error)
        # guardar_log("ERROR " + str(error))
        # enviar_correo(str(error))


def ejecutar():
    LISTA_SIG1 = Lista_Archivos_SIG1()
    LISTA_SIG2 = Lista_Archivos_SIG2()
    main(LISTA_SIG2,LISTA_SIG1)
    main_credito()


ejecutar()