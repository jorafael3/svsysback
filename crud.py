# Crear un arreglo con datos de estudiantes (id, nombres, apellido, estado).
#  Crear un programa principal en el que debe uƟlizar funciones para implementar un
# CRUD/Mantenimiento que le permita realizar: ingreso, modificación, consulta y
# eliminación de elementos al arreglo. El acceso a cada una de esas opciones se debe
# presentar mediante el uso de un menú que incluya “Salir”.
#  El ID es un dato secuencial numérico, el nombre y apellido es una cadena de texto y el
# estado solo almacena una letra A para AcƟvo o I para InacƟvo.



#ESTE ES EL ARREGLO PRINCIPAL DONDE SE ALMACENARAN LOS OBJETOS
array_estudiantes = []


#INGRESO DE ESTUDIANTE
def ingreso():
    print("---------------------")
    print("INGRESO DE ESTUDIANTE")
    print("---------------------")
    nombre = input("INGRESA NOMBRE >>")
    apellido = input("INGRESA APELLIDO >>")
	#VALIDAMOS QUE INGRESE A O I
    while True:
        estado = input("INGRESA ESTADO (A o I) >>")
        if estado.upper() in ["A", "I"]:
			#CREAMOS EL OBJETO CON LOS DATOS
            ides = 0
			#SI EL ARREGLO NO ESTA VACIO
            if(len(array_estudiantes) > 0):
				#BUSCAMOS EL MAYOR ID INGRESADO
                for id in array_estudiantes:
                    if int(id["ID"]) > ides:
                       ides = id["ID"]
            datos_poringresar = {
				"ID":int(ides) + 1, 
				"NOMBRE":nombre,
				"APELLIDO":apellido,
				"ESTADO":estado
			}
			#LO AGREGAMOS AL ARREGLO PRINCIPAL
            array_estudiantes.append(datos_poringresar)
            break  # Salir del bucle si el estado es válido
        else:
            print("Estado incorrecto. Por favor, ingresa 'A' o 'I'.")
    
#MOSTRAR TODOS ESTUDIANTES
def mostrar_todo():
	#CONTAMOS CUANTOS ELEMENTOS HAY EN EL ARREGLO
	cantidad_elementos = len(array_estudiantes)
	#VALIDAMOS QUE EL ARREGLO NO ESTE VACIO
	if(cantidad_elementos == 0):
		print("------------------------")
		print("NO HAY DATOS INGRESADOS")
		print("------------------------")
		
	else:
		#MOSTRAMOS TODOS LOS DATOS
		print("------------------------")
		print("ESTUDIANTES INGRESADOS ",str(cantidad_elementos))
		print("------------------------")
		print(array_estudiantes)
		for datos in array_estudiantes:
			print("------------------------------")
			print("ID",datos["ID"])
			print("NOMBRE",datos["NOMBRE"])
			print("APELLIDO",datos["APELLIDO"])
			print("ESTADO",datos["ESTADO"])
			print("------------------------------")

#MOSTRAR ESTUDIANTES POR ID
def mostrar_por_id():
	#CONTAMOS CUANTOS ELEMENTOS HAY EN EL ARREGLO
	cantidad_elementos = len(array_estudiantes)
	#VALIDAMOS QUE EL ARREGLO NO ESTE VACIO
	if(cantidad_elementos == 0):
		print("------------------------")
		print("NO HAY DATOS INGRESADOS")
		print("------------------------")
	else:
		id_est = int(input("INGRESA EL ID A BUSCAR >> "))
		#RECORREMOS EL ARREGLO
		for datos in array_estudiantes:
			#BUSCAMOS EL ID SE IGUAL AL INGRESADO
			if datos["ID"]== id_est:
				print("------------------------------")
				print("ID",datos["ID"])
				print("NOMBRE",datos["NOMBRE"])
				print("APELLIDO",datos["APELLIDO"])
				print("ESTADO",datos["ESTADO"])
				print("------------------------------")
			else:
				print("NO SE ENCUNTRA EL ID")


#MODIFICAR ESTUDIANTES POR ID
def modificar():
	#CONTAMOS CUANTOS ELEMENTOS HAY EN EL ARREGLO
	cantidad_elementos = len(array_estudiantes)
	#VALIDAMOS QUE EL ARREGLO NO ESTE VACIO
	if(cantidad_elementos == 0):
		print("------------------------")
		print("NO HAY DATOS INGRESADOS")
		print("------------------------")
	else:
		id_est = int(input("INGRESA EL ID A BUSCAR >> "))
		#RECORREMOS EL ARREGLO
		for datos in array_estudiantes:
				#BUSCAMOS EL ID SE IGUAL AL INGRESADO
				if datos["ID"]== id_est:
					#PEDIMOS LOS NUEVOS DATOS
					nombre = str(input("INGRESE NUEVO NOMBRE >>"))
					apellido = str(input("INGRESE NUEVO APELLIDO >>"))
					while True:
						estado = input("INGRESA ESTADO (A o I) >>")
						if estado.upper() in ["A", "I"]:
						#MODIFICAMOS LAS LLAVES 
							datos["NOMBRE"] = nombre
							datos["APELLIDO"] = apellido
							datos["ESTADO"] = estado
							break
						else:
							print("Estado incorrecto. Por favor, ingresa 'A' o 'I'.")
				else:
					print("NO SE ENCUENTRA EL ID")

def eliminar():
	cantidad_elementos = len(array_estudiantes)
	#VALIDAMOS QUE EL ARREGLO NO ESTE VACIO
	if(cantidad_elementos == 0):
		print("------------------------")
		print("NO HAY DATOS INGRESADOS")
		print("------------------------")
	else:
		id_est = int(input("INGRESA EL ID A ELIMINAR >> "))
		for datos in array_estudiantes:
			#BUSCAMOS EL ID SE IGUAL AL INGRESADO
			if datos["ID"]== id_est:
				#LO ELMINAMOS1
				array_estudiantes.remove(datos)
				print("------------------------")
				print("ESTUDIANTE ELIMINADO !")
				print("------------------------")
			else:
				print("NO SE ENCUENTRA EL ID")



def menu():
	print ("\t")
	print ("\t")
	print ("*************************************************")
	print ("Selecciona una opción")
	print ("\t1 - INGRESO")
	print ("\t2 - MODIFICACION")
	print ("\t3 - CONSULTA TODO")
	print ("\t4 - CONSULTA POR ID")
	print ("\t5 - ELIMINAR")
	print ("\t0 - SALIR")
 
 
while True:
	# Mostramos el menu
	menu()
 
	# solicituamos una opción al usuario
	opcionMenu = input("inserta un numero valor >> ")
 
	if opcionMenu=="1":
		print ("")
		ingreso()
	elif opcionMenu=="2":
		print ("")
		modificar()
	elif opcionMenu=="3":
		print ("")
		mostrar_todo()
	elif opcionMenu=="4":
		print ("")
		mostrar_por_id()
	elif opcionMenu=="5":
		print ("")
		eliminar()
	elif opcionMenu=="0":
		print ("")
		print ("GRACIAS POR USAR EL SISTEMA")
		break
	else:
		print ("")
		input("No has pulsado ninguna opción correcta...\npulsa una tecla para continuar")

