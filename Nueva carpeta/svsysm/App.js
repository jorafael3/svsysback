import { StatusBar } from 'expo-status-bar';
import { StyleSheet, Text, View, TouchableOpacity, TextInput, Alert, ScrollView } from 'react-native';
import React, { useState, Component, useEffect } from 'react';
import $ from 'jquery';
import { CameraKitCameraScreen, CameraKitCamera, CameraKitGalleryView } from 'react-native-camera-kit';
import BarcodeScanner from './components/barscanner'
import { BarCodeScanner } from 'expo-barcode-scanner';

const host = "10.5.0.238";
const protocol = "http:";
const port = ":8080";
const URL = protocol + "//" + host + port + "/svsysback/"



export default function App() {
  const [inputValue, setInputValue] = useState('');
  const [hasPermission, setHasPermission] = useState(null);
  const [scanned, setScanned] = useState(false);
  const [scannedData, setScannedData] = useState(null);
  const [isScannerVisible, setIsScannerVisible] = useState(false);
  const [isManualInputVisible, setIsManualInputVisible] = useState(false);
  const [isFormVisible, setisFormVisible] = useState(false);
  const [data, setData] = useState(null);

  const [cliente, setCliente] = useState('');
  const [ruc, setRuc] = useState('');
  const [solicitante, setSolicitante] = useState('');
  const [direccion, setDireccion] = useState('');
  const [fecha_emision, setfecha_emision] = useState('');
  const [pedido, setpedido] = useState('');
  const [data_detalle, setdata_detalle] = useState([]);

  const Cargar_guia = (pedido) => {
    let url = 'despacho/Cargar_Guia_p';
    pedido = pedido.trim()
    // pedido = pedido.replace("-","");
    if (pedido.length >= 20) {
      pedido = pedido.slice(0, -8)
      // Alert.alert("asdasd", JSON.stringify(pedido));
    } else {
      pedido = pedido.slice(0, -8)
      // Alert.alert("asdasd", JSON.stringify(pedido));
    }

    fetchData(url, pedido)
  };

  function scanner_() {
    let url = 'despacho/Cargar_Guia_p';
    let pedido = '505420198-09001092023'
    pedido = pedido.trim()
    pedido = parseInt(pedido).toString()
    // pedido = pedido.replace("-","");
    if (pedido.length >= 20) {
      pedido = pedido.slice(0, -8)
      // Alert.alert("asdasd", JSON.stringify(pedido));
    } else {
      pedido = pedido.slice(0, -8)
      // Alert.alert("asdasd", JSON.stringify(pedido));
    }
    fetchData(url, pedido)
  }

  async function fetchData(url, pedido) {
    try {
      const param = {
        PEDIDO_INTERNO: pedido,
      };
      const response = await fetch(URL + url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(param),
      });
      const data = await response.json();
      Llenar_Guia(data)
      // console.log(data);
    } catch (error) {
      console.error(error);
    }
  }

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

  function scanner() {
    setIsScannerVisible(true);
    setScanned(false);
    setIsManualInputVisible(false);

    (async () => {
      const { status } = await BarCodeScanner.requestPermissionsAsync();
      setHasPermission(status === 'granted');
    })();
  }

  function manual() {
    setIsManualInputVisible(true);
    setIsScannerVisible(false);

  }
  useEffect(() => {

  }, []);

  async function Cargar_Clientes() {
    try {
      const param = {
        PEDIDO_INTERNO: pedido,
      };
      const response = await fetch(URL + url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(param),
      });
      const data = await response.json();
      Llenar_Guia(data)
      // console.log(data);
    } catch (error) {
      console.error(error);
    }
  }
  const handleBarCodeScanned = ({ type, data }) => {
    setScanned(true);
    setScannedData({ type, data });
    setIsScannerVisible(false);
    Cargar_guia(data)
  };

  const toggleScanner = () => {
    setIsScannerVisible(!isScannerVisible); // Toggle the visibility of the scanner
  };

  // function AjaxSendReceiveData(url, param, callback) {
  //   try {
  //     const response = fetch('https://jsonplaceholder.typicode.com/posts/1'); // Replace with your API URL
  //     if (!response.ok) {
  //       throw new Error('Network response was not ok');
  //     }
  //     const jsonData = response.json();
  //     setData(jsonData);

  //   } catch (error) {
  //     console.error('Error:', error);
  //   }
  // }

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
            </View>

          )
            : (
              <Text style={styles.label}></Text>

            )
          }


        </View>
      </View>
    </View>
  );
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