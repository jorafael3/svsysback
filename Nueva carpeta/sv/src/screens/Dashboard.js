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

var SESION = [];
var CLIENTES = [];
// const [selectedOption, setSelectedOption] = useState(''); // State to store the selected option
function MyComponent() {


  return (
    <View>
      <Text>Select an option:</Text>
      <ModalSelector
        data={CLIENTES}
        initValue="Select an option"
        onChange={(option) => {
          
          // Handle the selected option here
        }}
      />
    </View>
  );
}
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
      SESION = savedData[0][1]
      

      // Alert.alert("sesion inciada", JSON.stringify(savedData));
    } catch (error) {
      
    }
  };

  useEffect(() => {
    getMultipleData();
    // 
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
      
      Llenar_Guia(x)
    });

  };


  function Llenar_Guia(data) {
    // let DATOS = JSON.stringify(data);
    let CABECERA = data[0][0];
    let DETALLE = data[1];
    if (data[0].length == 0) {

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

    fetchData(url, [], function (x) {
      
      let t = [];
      x.map(function(x){
        let b = {
          key:x.key1,
          label:x.label
        }
        t.push(b)
      })
      setdatos_clientes(t)
      // CLIENTES = x
    })
  }

  return (

    <View style={styles.container}>
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Text style={styles.title}>Cargar Guias</Text>
          <View style={styles.cardToolbar}>

          </View>
        </View>
        <View style={styles.cardBody}>
          <View style={styles_con.buttonContainer}>
            <TouchableOpacity onPress={scanner} style={styles.button}>
              <Text style={styles_con.buttonText}>Scanear Codigo</Text>
            </TouchableOpacity>
            <TouchableOpacity onPress={manual} style={styles.button}>
              <Text style={styles_con.buttonText}>ingreso manual</Text>
            </TouchableOpacity>
          </View>
          {isManualInputVisible && (
            <TextInput
              style={styles.input}
              placeholder="Ingrese algo"
              value={inputValue}
              onChangeText={text => setInputValue(text)}
            // onSubmitEditing={handleManualInputSubmit}
            />
          )}
          {isScannerVisible ? (
            <View style={styles.cameraContainer}>
              <BarCodeScanner
                onBarCodeScanned={scanned ? undefined : handleBarCodeScanned}
                style={styles.camera}
              />
            </View>
          )
            : (
              <TouchableOpacity onPress={toggleScanner} style={styles.toggleButton}>
                <Text></Text>
              </TouchableOpacity>
            )
          }
          {scanned && (
            <View style={styles.scanResultContainer}>
              {/* <Text>Type: {scannedData?.type}</Text> */}
              <Text>Codigo: {scannedData?.data}</Text>
            </View>
          )}
          {isFormVisible ? (
            <View style={styles.scanResultContainer}>
              <Text style={styles.label}>FECHA_DE EMISION:</Text>
              <Text style={styles.text}>{fecha_emision}</Text>
              <Text style={styles.label}>PEDIDO INTERNO:</Text>
              <Text style={styles.text}>{pedido}</Text>
              <ScrollView horizontal={true}>
                <View style={styles_tabla.container}>
                  {/* Table headers */}
                  <View style={styles_tabla.tableRow}>
                    <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>Cliente</Text>
                    <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>RUC</Text>
                    <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>Solicitante</Text>
                    <Text style={[styles_tabla.headerCell, styles_tabla.cell]}>Dirección</Text>
                  </View>

                  {/* Table rows */}
                  {data_detalle.map((item, index) => (
                    <View style={styles_tabla.tableRow} key={index}>
                      <Text style={styles_tabla.cell}>{item.ORD}</Text>
                      <Text style={styles_tabla.cell}>{item.CODIGO}</Text>
                      <Text style={styles_tabla.cell}>{item.DESCRIPCION}</Text>
                      <Text style={styles_tabla.cell}>{item.UNIDAD}</Text>
                    </View>
                  ))}
                </View>
              </ScrollView>
              <View>
                <Text>Seleccione un cliente</Text>
                <ModalSelector
                  data={datos_clientes}
                  initValue="Seleccione"
                  onChange={(option) => {
                    
                    handleOptionChange(option);
                    // Handle the selected option here
                  }}
                />
              </View>
            </View>


          )
            : (
              <Text style={styles.label}></Text>

            )
          }


        </View>
      </View>
    </View>
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
  )
}
const styles = StyleSheet.create({
  container: {
    flex: 1,
    // justifyContent: 'center',
    // alignItems: 'center',
  },
  card: {
    marginTop: 50,
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
    margin: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
    elevation: 4, // Solo para Android
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  cardToolbar: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  button: {
    backgroundColor: 'green',
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 5,
    marginLeft: 10,
  },
  buttonText: {
    color: 'white',
    fontWeight: 'bold',
  },
  cardBody: {
    marginTop: 20,
    // Estilos adicionales para la tabla o contenido aquí
  },
  input: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 5,
    padding: 10,
    marginBottom: 10,
  },
  scanResultContainer: {
    backgroundColor: 'white',
    padding: 10,
    marginTop: 'auto',
  },
  cameraContainer: {
    flex: 1, // Haz que el contenedor de la cámara ocupe todo el espacio disponible
    // justifyContent: 'center', // Centra verticalmente la cámara en el contenedor
    // alignItems: 'center', // Centra horizontalmente la cámara en el contenedor
    width: '100%',
    // aspectRatio: 1,
  },
  camera: {
    width: '100%', // Ancho de la cámara al 100% del contenedor
    aspectRatio: 1,
    // height:100 
  },
  label: {
    fontSize: 16,
    marginBottom: 8,
  },
  selectedOptionText: {
    fontSize: 18,
  },
});

const styles_con = StyleSheet.create({
  // ... Otros estilos ...

  buttonContainer: {
    flexDirection: 'row', // Coloca los elementos hijos en una fila horizontal
  },
  button: {
    marginRight: 10, // Espacio entre los botones (ajusta según tus preferencias)
  },
  buttonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: 'white',
  },
  // ... Otros estilos ...
});

const styles_tabla = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: '#fff',
  },
  tableRow: {
    flexDirection: 'row',
    borderBottomWidth: 1,
    borderColor: '#ccc',
    paddingVertical: 12,
  },
  headerCell: {
    flex: 1,
    fontWeight: 'bold',
  },
  cell: {
    flex: 1,
    paddingHorizontal: 8,
    textAlign: 'center', // Alinea el contenido al centro horizontalmente
  },
});

const pickerSelectStyles = StyleSheet.create({
  inputIOS: {
    fontSize: 16,
    paddingVertical: 12,
    paddingHorizontal: 10,
    borderWidth: 1,
    borderColor: 'gray',
    borderRadius: 4,
    color: 'black',
  },
  inputAndroid: {
    fontSize: 16,
    paddingHorizontal: 10,
    paddingVertical: 8,
    borderWidth: 0.5,
    borderColor: 'gray',
    borderRadius: 8,
    color: 'black',
  },
});