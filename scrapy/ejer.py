import pandas as pd

class Estudiante:
    def __init__(self, nombre, apellido, nota1, nota2, nota3):
        self.nombre = nombre.strip().title()  # Elimina espacios en blanco y formatea el nombre
        self.apellido = apellido.strip().title()  # Elimina espacios en blanco y formatea el apellido
        self.nota1 = nota1
        self.nota2 = nota2
        self.nota3 = nota3

    def calcular_promedio(self):
        return round((self.nota1 + self.nota2 + self.nota3) / 3, 2)  # Redondea el promedio a 2 decimales

    def determinar_estado(self):
        promedio = self.calcular_promedio()
        return 'A' if promedio >= 7 else 'R'

class GestorEstudiantes:
    def __init__(self):
        self.estudiantes = [
            Estudiante("Norma", "Cajamarca", 7.0, 8.4, 9.5),
            Estudiante("Jeyson", "Hurtado", 5.5, 10.0, 7.0),
            Estudiante("Lucila", "Mora", 8.0, 9.5, 6.5),
            Estudiante("Carlos", "Navarrete", 7.0, 9.5, 10.0),
            Estudiante("Nayeli", "Rodriguez", 6.5, 8.0, 7.5),
            Estudiante("Naomi", "Hurtado", 6.4, 8.5, 9.0)
        ]
        self.promedios = []
        self.estados = []

    def ingresar_actualizar_notas(self):
        nombre = input("Ingrese el nombre del estudiante: ").strip().title()  # Elimina espacios y formatea el nombre
        apellido = input("Ingrese el apellido del estudiante: ").strip().title()  # Elimina espacios y formatea el apellido

        # Buscar si el estudiante ya existe en la lista
        estudiante_encontrado = None
        for estudiante in self.estudiantes:
            print(estudiante)

            if estudiante.nombre == nombre and estudiante.apellido == apellido:
                estudiante_encontrado = estudiante
                break

        # Si el estudiante no está en la lista, mostrar un mensaje y salir
        if estudiante_encontrado is None:
            print("Estudiante no encontrado en la lista.")
            return

        # Si el estudiante está en la lista, actualizar sus notas
        nota1 = float(input("Ingrese la primera nota: "))
        nota2 = float(input("Ingrese la segunda nota: "))
        nota3 = float(input("Ingrese la tercera nota: "))
        estudiante_encontrado.nota1 = nota1
        estudiante_encontrado.nota2 = nota2
        estudiante_encontrado.nota3 = nota3
        print("Notas actualizadas correctamente.")

    def mostrar_estudiantes(self, orden):
        self.promedios = []
        self.estados = []
        for estudiante in self.estudiantes:
            promedio = estudiante.calcular_promedio()
            estado = estudiante.determinar_estado()
            self.promedios.append(promedio)
            self.estados.append(estado)

        data = []
        for estudiante, promedio, estado in zip(self.estudiantes, self.promedios, self.estados):
            data.append([estudiante.apellido, estudiante.nombre, estudiante.nota1, estudiante.nota2,
                         estudiante.nota3, promedio, estado])

        df = pd.DataFrame(data, columns=["Apellido", "Nombre", "Nota1", "Nota2", "Nota3", "Promedio", "Estado"])
        if orden == "ascendente":
            df = df.sort_values(by=["Apellido"], ascending=True)
        elif orden == "descendente":
            df = df.sort_values(by=["Apellido"], ascending=False)

        print(df.to_string(index=False))  # Mostrar DataFrame sin índices

# Programa principal
gestor = GestorEstudiantes()

while True:
    print("\nMenú:")
    print("1. Ingresar/Actualizar notas de un estudiante")
    print("2. Mostrar promedios ordenados por apellido (Ascendente)")
    print("3. Mostrar promedios ordenados por apellido (Descendente)")
    print("4. Salir")
    opcion = input("Seleccione una opción: ")

    if opcion == "1":
        gestor.ingresar_actualizar_notas()
    elif opcion == "2":
        gestor.mostrar_estudiantes("ascendente")
    elif opcion == "3":
        gestor.mostrar_estudiantes("descendente")
    elif opcion == "4":
        print("¡Hasta luego!")
        break
    else:
        print("Opción inválida. Por favor, seleccione una opción válida.")