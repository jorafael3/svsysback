import { View, Text, TouchableOpacity, StyleSheet, Alert, ScrollView } from 'react-native';
import React, { useState, Component, useEffect } from 'react';
import { BarCodeScanner } from 'expo-barcode-scanner';
import fetchData from "../config/config"
import { DataTable } from 'react-native-paper';
import ModalSelector from 'react-native-modal-selector';


export default function Guias({ route, navigation }) {
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');

    //***SCANNER */
    const [isScannerVisible, setIsScannerVisible] = useState(false);
    const [scanned, setScanned] = useState(false);
    const [scannedData, setScannedData] = useState(null);
    const [hasPermission, setHasPermission] = useState(null);

    //******* DATOS TABLA */
    const [isFormVisible, setisFormVisible] = useState(false);
    const [fecha_emision, setfecha_emision] = useState('');
    const [pedido, setpedido] = useState('');
    const [data_detalle, setdata_detalle] = useState([]);

    //********** CLIENTES *******/
    const [datos_clientes, setdatos_clientes] = useState([]);
    const [selectedOption, setSelectedOption] = useState("");


    //********** CLIENTES ******/
    const handleOptionChange = (value) => {
        // console.log('value: ', value.key);
        setSelectedOption(value);
    };

    function Cargar_Clientes() {
        let url = "clientes/Cargar_Clientes_m"
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
        })
    }


    useEffect(() => {
        const datos_sesion = route.params;
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        Cargar_Clientes()
    }, []);

    const handleLogout = () => {
        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
    };

    //************************************* */
    //******* SCANNER */
    const toggleScanner = () => {
        RESET(false);
        setIsScannerVisible(!isScannerVisible); // Toggle the visibility of the scanner
    };

    //! INICIA SCANNER
    function scanner() {
        setIsScannerVisible(true);
        setScanned(false);
        setSelectedOption("")
        setpedido("")
        // setIsManualInputVisible(false);
        setisFormVisible(false);
        (async () => {
            const { status } = await BarCodeScanner.requestPermissionsAsync();
            setHasPermission(status === 'granted');
        })();
    }
    //*** CUANDO ESCANEA CODIGO */
    const handleBarCodeScanned = ({ type, data }) => {
        setScanned(true);
        setScannedData({ type, data });
        setIsScannerVisible(false);
        // Alert.alert("type", data)
        Cargar_guia(data)
    };

    function RESET(scanner) {
        setIsScannerVisible(scanner);
        setScanned(false);
        setSelectedOption("")
        setpedido("")
        // setIsManualInputVisible(false);
        setisFormVisible(false);
    }

    //************************************** */
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
            let val = x[2];
            console.log('datos: ', val);
            if (val == 1) {
                console.log('datos: ', datos);
                Llenar_Guia(x)
            } else {
                Alert.alert("", x[0].toString())
            }
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
        // setIsManualInputVisible(true);
        // setIsScannerVisible(false);
    }


    //**********************/******** */ */
    //*****  GUARDAR DATOS*/
    function Guardar_datos_guia() {
        let cliente = selectedOption.key;
        if (cliente == "" || cliente == undefined) {
            Alert.alert("", "Debe seleccionar un cliente");
        } else {
            let param = {
                USUARIO: usuario,
                USUARIO_ID: usuarioid,
                PEDIDO: pedido,
                CLIENTE: selectedOption.key
            }
            console.log('param: ', param);
            Alert.alert("", "Datos Guardados");
            RESET();

        }

    }

    return (
        <View style={styles.container}>
            {/* Barra superior con nombre de usuario y botón de salida */}
            <View style={styles.header}>
                <Text style={styles.username}>Usuario: {usuario}</Text>
                <TouchableOpacity onPress={handleLogout}>
                    <Text style={styles.logoutButton}>Salir</Text>
                </TouchableOpacity>
            </View>
            <View style={styles.card}>
                <View style={styles.buttonContainer}>
                    <TouchableOpacity onPress={scanner} style={styles.button}>
                        <Text style={styles.buttonText}>Escanear Código</Text>
                    </TouchableOpacity>
                    <TouchableOpacity onPress={manual} style={styles.button}>
                        <Text style={styles.buttonText}>Ingreso Manual</Text>
                    </TouchableOpacity>
                </View>
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
    )
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    header: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        paddingHorizontal: 20,
        paddingTop: 40,
    },
    username: {
        fontSize: 16,
    },
    logoutButton: {
        color: 'red',
        fontSize: 16,
    },
    card: {
        flex: 1,
        margin: 20,
        padding: 20,
        borderRadius: 10,
        backgroundColor: '#f0f0f0',
    },
    buttonContainer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        marginBottom: 16,
    },
    button: {
        flex: 1,
        backgroundColor: 'green',
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
