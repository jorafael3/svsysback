import requests

# Definir el dato que deseas enviar
dato = {"cedula": "bzj8gix/XnXTZF6EZclk7UPQxvRaupeSC1LLlsaWhRI="}  # Reemplaza "key" y "value" con los nombres adecuados y los valores que deseas enviar

# Hacer la solicitud POST a la URL de la API con el dato en el cuerpo de la solicitud
response = requests.post("https://apidatoscedula20240216081841.azurewebsites.net/api/GetData", json=dato)

# Verificar si la solicitud fue exitosa (código de estado 200)
if response.status_code == 200:
    # Si la solicitud fue exitosa, imprimir la respuesta
    print(response.json())
else:
    # Si hubo un error, imprimir el código de estado de la respuesta
    print("Error:", response.status_code)
