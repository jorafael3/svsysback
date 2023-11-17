import sqlalchemy
server = 'gator4166.hostgator.com' 
database = 'salvacer_svsys' 
username = 'salvacer_jorge' 
password = 'Equilivre3*'  

DATABASE_URL = 'mysql+mysqlconnector://salvacer_jorge:Equilivre3*@gator4166.hostgator.com:3306/salvacer_svsys'
engine = sqlalchemy.create_engine(DATABASE_URL)
conn = engine.connect()
consulta_sql = sqlalchemy.text('SELECT * FROM guias WHERE PEDIDO_INTERNO = :PEDIDO_INTERNO')
result = conn.execute(consulta_sql,PEDIDO_INTERNO='505700930' )
resultados = []
for row in result:
        resultados.append(row)

print(resultados)