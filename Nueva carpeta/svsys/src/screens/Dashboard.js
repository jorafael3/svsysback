import React, { useState, Component, useEffect } from 'react';
import Background from '../components/Background'
import Logo from '../components/Logo'
import Header from '../components/Header'
import Paragraph from '../components/Paragraph'
import Button from '../components/Button'
import { Alert } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage';
import { StyleSheet, Text, View, TouchableOpacity, TextInput, ScrollView } from 'react-native';
import { BarCodeScanner } from 'expo-barcode-scanner';
import fetchData from "../config/config"
import ModalSelector from 'react-native-modal-selector';
import { DataTable } from 'react-native-paper';
var SESION = [];
var CLIENTES = [];
// const [selectedOption, setSelectedOption] = useState(''); // State to store the selected option

const data = [
];

export default function Dashboard({ navigation }) {
  const [selectedOption, setSelectedOption] = useState("");
  const [datos_usuario, setdatos_usuario] = useState([]);
  const [inputValue, setInputValue] = useState('');
  const [scanned, setScanned] = useState(false);
  const [scannedData, setScannedData] = useState(null);
  const [isScannerVisible, setIsScannerVisible] = useState(false);
  const [isManualInputVisible, setIsManualInputVisible] = useState(false);
  const [isFormVisible, setisFormVisible] = useState(false);
  const [fecha_emision, setfecha_emision] = useState('');
  const [pedido, setpedido] = useState('');
  const [data_detalle, setdata_detalle] = useState([]);
  const [hasPermission, setHasPermission] = useState(null);
  const [datos_clientes, setdatos_clientes] = useState([]);
  // const [selectedOption, setSelectedOption] = useState(''); // State to store the selected option

  const getMultipleData = async () => {
    try {
      const savedData = await AsyncStorage.multiGet(["datos_usuario"]);
      setdatos_usuario(savedData[0])
      // SESION = savedData[0][1]
      SESION = JSON.parse(savedData[0][1]);

      // Alert.alert("sesion inciada", JSON.stringify(savedData));
    } catch (error) {

    }
  };

  useEffect(() => {
    getMultipleData();
    Cargar_Clientes()
  }, []);

  const handleBarCodeScanned = ({ type, data }) => {
    setScanned(true);
    setScannedData({ type, data });
    setIsScannerVisible(false);
    Cargar_guia(data)
  };

  function scanner() {
    setIsScannerVisible(true);
    setScanned(false);
    setIsManualInputVisible(false);
    setisFormVisible(false);
    (async () => {
      const { status } = await BarCodeScanner.requestPermissionsAsync();
      setHasPermission(status === 'granted');
    })();
  }
  //*********** GUIAS */
  const Cargar_guia = (pedido) => {
    let url = 'despacho/Cargar_Guia_p';
    pedido = pedido.trim()
    if (pedido.length >= 20) {
      pedido = pedido.slice(0, -8)
    } else {
      pedido = pedido.slice(0, -8)
    }
    // fetchData(url, pedido)
    pedido = parseInt(pedido).toString()

    const param = {
      PEDIDO_INTERNO: pedido,
    };


    fetchData(url, param, function (x) {
      console.log('x: ', x);

      Llenar_Guia(x)
    });

  };


  function Llenar_Guia(data) {
    // let DATOS = JSON.stringify(data);
    let CABECERA = data[0][0];
    let DETALLE = data[1];
    if (CABECERA.length == 0) {
      Alert.alert("", "Guia no encontrada, verifique el numero, o vuelva a escanear");
    } else {
      setisFormVisible(true);
      setfecha_emision(CABECERA["FECHA_DE_EMISION"]);
      setpedido(CABECERA["PEDIDO_INTERNO"])
      setdata_detalle(DETALLE)
      // Alert.alert("asdasd", data[0][0]["ID"]);
    }

  }

  function manual() {
    setIsManualInputVisible(true);
    setIsScannerVisible(false);
  }

  const toggleScanner = () => {
    setIsScannerVisible(!isScannerVisible); // Toggle the visibility of the scanner
  };

  //********** CLIENTES */
  const handleOptionChange = (value) => {
    console.log('value: ', value.key);
    setSelectedOption(value);
  };

  function Cargar_Clientes() {
    let url = "clientes/Cargar_Clientes_m"
    // let t = [];
    // let b = {
    //   key: 17,
    //   label: "CLIENTE 1"
    // }
    // t.push(b)
    // setdatos_clientes(t)
    fetchData(url, [], function (x) {
      console.log('x: ', x);

      if (x.length == 0) {
       

      } else {

        let t = [];
        x.map(function (x) {
          let b = {
            key: x.key1,
            label: x.label
          }
          t.push(b)
        })
        setdatos_clientes(t)
      }

      // CLIENTES = x
    })
  }

  const handleRowClick = (rowData) => {
    // Aquí puedes llamar a la función con los datos de la fila
    console.log('Datos de la fila:', rowData);
  };

  function Guardar_datos_guia() {
    let param = {
      USUARIO: SESION["usuario"],
      PEDIDO: pedido,
      CLIENTE:selectedOption.key

    }
    console.log('param: ', param);

  }

  return (
    <View style={styles.container}>
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Text style={styles.title}>Cargar Guías</Text>
          <View style={styles.cardToolbar}>
            {/* Aquí puedes agregar cualquier elemento de la barra de herramientas */}
          </View>
        </View>
        <View style={styles.cardBody}>
          <View style={styles.buttonContainer}>
            <TouchableOpacity onPress={scanner} style={styles.button}>
              <Text style={styles.buttonText}>Escanear Código</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={manual} style={styles.button}>
              <Text style={styles.buttonText}>Ingreso Manual</Text>
            </TouchableOpacity>
          </View>
          {isManualInputVisible && (
            <TextInput
              style={styles.input}
              placeholder="Ingrese algo"
              value={inputValue}
              onChangeText={text => setInputValue(text)}
            />
          )}
          {isScannerVisible ? (

            <View style={styles.cameraContainer}>
              <BarCodeScanner
                onBarCodeScanned={scanned ? undefined : handleBarCodeScanned}
                style={styles.camera}
              />
            </View>
          ) : (
            <TouchableOpacity onPress={toggleScanner} style={styles.toggleButton}>
              <Text></Text>
            </TouchableOpacity>
          )}
          {scanned && (
            <View style={styles.scanResultContainer}>
              <Text>Código Escaneado: {scannedData?.data}</Text>
            </View>
          )}
          {isFormVisible && (
            <View style={styles.formContainer}>
              <Text style={styles.label}>Fecha de Emisión:</Text>
              <Text style={styles.text}>{fecha_emision}</Text>
              <Text style={styles.label}>Pedido Interno:</Text>
              <Text style={styles.text}>{pedido}</Text>
              <ScrollView horizontal={true}>
                <DataTable>
                  <DataTable.Header>
                    <DataTable.Title style={styles.columnHeader}>ORD</DataTable.Title>
                    <DataTable.Title style={styles.columnHeader} numeric>CÓDIGO</DataTable.Title>
                    <DataTable.Title style={styles.columnHeader} numeric>DESCRIPCIÓN</DataTable.Title>
                    <DataTable.Title style={styles.columnHeader} numeric>UNIDAD</DataTable.Title>
                    <DataTable.Title style={styles.columnHeader} numeric>POR DESPACHAR</DataTable.Title>
                  </DataTable.Header>
                  {data_detalle.map((item, index) => (
                    <DataTable.Row key={index}>
                      <DataTable.Cell style={styles.cell}>{item.ORD}</DataTable.Cell>
                      <DataTable.Cell style={styles.cell} numeric>{item.CODIGO}</DataTable.Cell>
                      <DataTable.Cell style={styles.cell} numeric>{item.DESCRIPCION}</DataTable.Cell>
                      <DataTable.Cell style={styles.cell} numeric>{item.UNIDAD}</DataTable.Cell>
                      <DataTable.Cell style={styles.cell} numeric>{item.POR_DESPACHAR}</DataTable.Cell>
                    </DataTable.Row>
                  ))}
                </DataTable>
              </ScrollView>
              <View style={styles.clientSelector}>
                <Text>Seleccione un cliente</Text>
                <ModalSelector
                  // keyExtractor={(item) => item.key}
                  data={datos_clientes}
                  initValue="Seleccione"
                  onChange={(option) => {
                    handleOptionChange(option);
                    // Handle the selected option here
                  }}
                />
              </View>
              <View style={styles.footer}>
                <TouchableOpacity onPress={Guardar_datos_guia} style={styles.guardarButton}>
                  <Text style={styles.guardarButtonText}>Guardar datos</Text>
                </TouchableOpacity>
              </View>
            </View>

          )}

        </View>

      </View>
    </View>

  )

}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: '#f0f0f0',
    marginTop: 80
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 8,
    elevation: 3,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.3,
    shadowRadius: 2,
  },
  cardHeader: {
    padding: 16,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  cardToolbar: {
    flexDirection: 'row',
  },
  cardBody: {
    padding: 16,
  },
  buttonContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 16,
  },
  button: {
    flex: 1,
    backgroundColor: 'blue',
    padding: 12,
    borderRadius: 6,
    marginHorizontal: 8,
    alignItems: 'center',
  },
  buttonText: {
    color: 'white',
    fontWeight: 'bold',
  },
  input: {
    height: 40,
    borderWidth: 1,
    borderColor: 'gray',
    borderRadius: 6,
    paddingHorizontal: 8,
    marginBottom: 16,
  },
  cameraContainer: {
    height: 300,
    backgroundColor: 'black',
    marginBottom: 16,
  },
  toggleButton: {
    alignItems: 'center',
    marginBottom: 16,
  },
  scanResultContainer: {
    marginBottom: 16,
  },
  formContainer: {
    marginBottom: 16,
  },
  label: {
    fontWeight: 'bold',
  },
  text: {
    marginBottom: 8,
  },
  columnHeader: {
    width: 100,
    justifyContent: 'center',
    alignItems: 'center',
    fontWeight: 'bold',
  },
  cell: {
    width: 100,
    justifyContent: 'center',
    alignItems: 'center',
  },
  clientSelector: {
    marginTop: 16,
  },
  camera: {
    width: 300, // Ancho de la cámara al 100% del contenedor
    aspectRatio: 1,
    // height:100
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center', // Cambiamos a 'center' para centrar horizontalmente
    marginTop: 16,
  },
  guardarButton: {
    backgroundColor: 'green',
    padding: 12,
    borderRadius: 6,
    alignItems: 'center',
    justifyContent: 'center', // Mantenemos 'center' para centrar verticalmente
  },
  guardarButtonText: {
    color: 'white',
    fontWeight: 'bold',
  },
});















// const styles = StyleSheet.create({
//   container: {
//     flex: 1,
//     // justifyContent: 'center',
//     // alignItems: 'center',
//   },
//   card: {
//     marginTop: 50,
//     backgroundColor: '#fff',
//     borderRadius: 8,
//     padding: 16,
//     margin: 16,
//     shadowColor: '#000',
//     shadowOffset: { width: 0, height: 2 },
//     shadowOpacity: 0.2,
//     shadowRadius: 4,
//     elevation: 4, // Solo para Android
//   },
//   cardHeader: {
//     flexDirection: 'row',
//     justifyContent: 'space-between',
//     alignItems: 'center',
//   },
//   title: {
//     fontSize: 20,
//     fontWeight: 'bold',
//   },
//   cardToolbar: {
//     flexDirection: 'row',
//     alignItems: 'center',
//   },
//   button: {
//     backgroundColor: 'green',
//     paddingVertical: 10,
//     paddingHorizontal: 20,
//     borderRadius: 5,
//     marginLeft: 10,
//   },
//   buttonText: {
//     color: 'white',
//     fontWeight: 'bold',
//   },
//   cardBody: {
//     marginTop: 20,
//     // Estilos adicionales para la tabla o contenido aquí
//   },
//   input: {
//     borderWidth: 1,
//     borderColor: '#ccc',
//     borderRadius: 5,
//     padding: 10,
//     marginBottom: 10,
//   },
//   scanResultContainer: {
//     backgroundColor: 'white',
//     padding: 10,
//     marginTop: 'auto',
//   },
//   cameraContainer: {
//     flex: 1, // Haz que el contenedor de la cámara ocupe todo el espacio disponible
//     // justifyContent: 'center', // Centra verticalmente la cámara en el contenedor
//     // alignItems: 'center', // Centra horizontalmente la cámara en el contenedor
//     width: '100%',
//     // aspectRatio: 1,
//   },
//   camera: {
//     width: '100%', // Ancho de la cámara al 100% del contenedor
//     aspectRatio: 1,
//     // height:100
//   },
//   label: {
//     fontSize: 16,
//     marginBottom: 8,
//   },
//   selectedOptionText: {
//     fontSize: 18,
//   },
// });

// const styles_con = StyleSheet.create({
//   // ... Otros estilos ...

//   buttonContainer: {
//     flexDirection: 'row', // Coloca los elementos hijos en una fila horizontal
//   },
//   button: {
//     marginRight: 10, // Espacio entre los botones (ajusta según tus preferencias)
//   },
//   buttonText: {
//     fontSize: 16,
//     fontWeight: 'bold',
//     color: 'white',
//   },
//   // ... Otros estilos ...
// });

// const styles_tabla = StyleSheet.create({
//   container: {
//     flex: 1,
//     padding: 16,
//     backgroundColor: '#fff',
//   },
//   tableRow: {
//     flexDirection: 'row',
//     borderBottomWidth: 1,
//     borderColor: '#ccc',
//     paddingVertical: 12,
//   },
//   headerCell: {
//     flex: 1,
//     fontWeight: 'bold',
//   },
//   cell: {
//     flex: 1,
//     paddingHorizontal: 8,
//     textAlign: 'center', // Alinea el contenido al centro horizontalmente
//   },
// });

// const pickerSelectStyles = StyleSheet.create({
//   inputIOS: {
//     fontSize: 16,
//     paddingVertical: 12,
//     paddingHorizontal: 10,
//     borderWidth: 1,
//     borderColor: 'gray',
//     borderRadius: 4,
//     color: 'black',
//   },
//   inputAndroid: {
//     fontSize: 16,
//     paddingHorizontal: 10,
//     paddingVertical: 8,
//     borderWidth: 0.5,
//     borderColor: 'gray',
//     borderRadius: 8,
//     color: 'black',
//   },
// });


// <View style={styles.container}>
// <View style={styles.card}>
//   <View style={styles.cardHeader}>
//     <Text style={styles.title}>Cargar Guias</Text>
//     <View style={styles.cardToolbar}>

//     </View>
//   </View>
//   <View style={styles.cardBody}>
//     <View style={styles_con.buttonContainer}>
//       <TouchableOpacity onPress={scanner} style={styles.button}>
//         <Text style={styles_con.buttonText}>Scanear Codigo</Text>
//       </TouchableOpacity>
//       <TouchableOpacity onPress={manual} style={styles.button}>
//         <Text style={styles_con.buttonText}>ingreso manual</Text>
//       </TouchableOpacity>
//     </View>
//     {isManualInputVisible && (
//       <TextInput
//         style={styles.input}
//         placeholder="Ingrese algo"
//         value={inputValue}
//         onChangeText={text => setInputValue(text)}
//       // onSubmitEditing={handleManualInputSubmit}
//       />
//     )}
//     {isScannerVisible ? (
//       <View style={styles.cameraContainer}>
//         <BarCodeScanner
//           onBarCodeScanned={scanned ? undefined : handleBarCodeScanned}
//           style={styles.camera}
//         />
//       </View>
//     )
//       : (
//         <TouchableOpacity onPress={toggleScanner} style={styles.toggleButton}>
//           <Text></Text>
//         </TouchableOpacity>
//       )
//     }
//     {scanned && (
//       <View style={styles.scanResultContainer}>
//         {/* <Text>Type: {scannedData?.type}</Text> */}
//         <Text>Codigo: {scannedData?.data}</Text>
//       </View>
//     )}
//     {isFormVisible ? (
//       <View style={styles.scanResultContainer}>
//         <Text style={styles.label}>FECHA_DE EMISION:</Text>
//         <Text style={styles.text}>{fecha_emision}</Text>
//         <Text style={styles.label}>PEDIDO INTERNO:</Text>
//         <Text style={styles.text}>{pedido}</Text>
//         <ScrollView horizontal={true}>
//           <View style={styles_tabla.container}>
//             <View style={styles_tabla.tableRow}>
//               <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>Cliente</Text>
//               <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>RUC</Text>
//               <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>Solicitante</Text>
//               <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>Dirección</Text>
//             </View>

//             {data_detalle.map((item, index) => (
//               <View style={styles_tabla.tableRow} key={index}>
//                 <Text style={styles_tabla.cell}>{item.ORD}</Text>
//                 <Text style={styles_tabla.cell}>{item.CODIGO}</Text>
//                 <Text style={styles_tabla.cell}>{item.DESCRIPCION}</Text>
//                 <Text style={styles_tabla.cell}>{item.UNIDAD}</Text>
//               </View>
//             ))}
//           </View>
//         </ScrollView>
//         {/* <View style={styles.container}>
//           <View style={styles.tableRow}>
//             <Text style={styles.headerCell}>ID</Text>
//             <Text style={styles.headerCell}>Nombre</Text>
//             <Text style={styles.headerCell}>Edad</Text>
//             <Text style={styles.headerCell}>Género</Text>
//           </View>

//           <FlatList
//             data={data}
//             keyExtractor={(item) => item.id.toString()}
//             renderItem={({ item }) => (
//               <View style={styles.tableRow}>
//                 <Text style={styles.cell}>{item.id}</Text>
//                 <Text style={styles.cell}>{item.name}</Text>
//                 <Text style={styles.cell}>{item.age}</Text>
//                 <Text style={styles.cell}>{item.gender}</Text>
//                 <TouchableOpacity
//                   onPress={() => handleRowClick(item)}
//                   style={styles.button}
//                 >
//                   <Text style={styles.buttonText}>Ver Detalles</Text>
//                 </TouchableOpacity>
//               </View>
//             )}
//           />
//         </View> */}
//         <ScrollView horizontal={true}>

//           <DataTable>
//             <DataTable.Header>
//               <DataTable.Title style={{ width: 50, justifyContent: 'center', alignItems: 'center' }}>ORD</DataTable.Title>
//               <DataTable.Title style={{ width: 100, justifyContent: 'center', alignItems: 'center' }} numeric>CODIGO</DataTable.Title>
//               <DataTable.Title style={{ width: 300, justifyContent: 'center', alignItems: 'center' }} numeric>DESCRIPCION</DataTable.Title>
//               <DataTable.Title style={{ width: 100, justifyContent: 'center', alignItems: 'center' }} numeric>UNIDAD</DataTable.Title>
//               <DataTable.Title style={{ width: 100, justifyContent: 'center', alignItems: 'center' }} numeric>POR DESPACHAR</DataTable.Title>
//             </DataTable.Header>

//             {data_detalle.map((item, index) => (
//               <DataTable.Row key={index}>
//                 <DataTable.Cell style={{ width: 50, justifyContent: 'center', alignItems: 'center' }}>{item.ORD}</DataTable.Cell>
//                 <DataTable.Cell style={{ width: 100, justifyContent: 'center', alignItems: 'center' }}>{item.CODIGO}</DataTable.Cell>
//                 <DataTable.Cell style={{ width: 300, justifyContent: 'center', alignItems: 'center' }}>{item.DESCRIPCION}</DataTable.Cell>
//                 <DataTable.Cell style={{ width: 100, justifyContent: 'center', alignItems: 'center' }}>{item.UNIDAD}</DataTable.Cell>
//                 <DataTable.Cell style={{ width: 100, justifyContent: 'center', alignItems: 'center' }}>{item.POR_DESPACHAR}</DataTable.Cell>
//               </DataTable.Row>
//             ))}
//           </DataTable>

//           {/* <DataTable.Pagination
//             page={1}
//             numberOfPages={3}
//             onPageChange={(page) => {
//               console.log(page);
//             }}
//             label="1-2 of 6"
//           /> */}
//         </ScrollView>

//         <View>
//           <Text>Seleccione un cliente</Text>
//           <ModalSelector
//             data={datos_clientes}
//             initValue="Seleccione"
//             onChange={(option) => {

//               handleOptionChange(option);
//               // Handle the selected option here
//             }}
//           />
//         </View>
//       </View>


//     )
//       : (
//         <Text style={styles.label}></Text>

//       )
//     }


//   </View>
// </View>
// </View>
// <Background>

//   <Button
//     mode="outlined"
//     onPress={() =>
//       navigation.reset({
//         index: 0,
//         routes: [{ name: 'LoginScreen' }],
//       })
//     }
//   >
//     Logout
//   </Button>
// </Background>