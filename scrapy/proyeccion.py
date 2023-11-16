import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.model_selection import train_test_split
import matplotlib.pyplot as plt

# Generar datos simulados
np.random.seed(42)
meses = pd.date_range(start='2022-01-01', end='2022-12-31', freq='M')
ventas = np.random.randint(1000, 2000, size=len(meses))

# Crear un DataFrame
datos_ventas = pd.DataFrame({'Fecha': meses, 'Ventas': ventas})

# Extraer características temporales relevantes
datos_ventas['Año'] = datos_ventas['Fecha'].dt.year
datos_ventas['Mes'] = datos_ventas['Fecha'].dt.month

# Dividir los datos en conjuntos de entrenamiento y prueba
X = datos_ventas[['Año', 'Mes']].values
y = datos_ventas['Ventas'].values
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Entrenar un modelo de regresión lineal
modelo = LinearRegression()
modelo.fit(X_train, y_train)

# Prever las ventas para todo el año
ventas_predichas = modelo.predict(X)

# Visualizar las ventas reales y predichas
plt.figure(figsize=(10, 6))
plt.scatter(datos_ventas['Fecha'], ventas, label='Ventas reales', color='blue')
plt.plot(datos_ventas['Fecha'], ventas_predichas, label='Ventas predichas', color='red')
plt.title('Proyección de Ventas')
plt.xlabel('Fecha')
plt.ylabel('Ventas')
plt.legend()
plt.show()

# Calcular la proyección para el próximo mes
proyeccion_fecha_nueva = pd.Timestamp('2023-01-01')
proyeccion_ventas_nueva = modelo.predict([[proyeccion_fecha_nueva.year, proyeccion_fecha_nueva.month]])
print(f"Proyección de ventas para enero de 2023: {proyeccion_ventas_nueva[0]:.2f}")
