import { View, Text, TouchableOpacity, StyleSheet, Alert, ScrollView, TextInput } from 'react-native';
import React, { useState, Component, useEffect } from 'react';
import { BarCodeScanner } from 'expo-barcode-scanner';
import fetchData from "../config/config"
import { DataTable } from 'react-native-paper';
import ModalSelector from 'react-native-modal-selector';
import Checkbox from 'expo-checkbox';

import Menu from './Menu'
import { createStackNavigator } from '@react-navigation/stack';

const Stack = createStackNavigator();

export default function Guias({ route, navigation }) {
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');

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
    const [selectedRows, setSelectedRows] = useState([]); // Arreglo para almacenar las selecciones
    const [partialEntryEnabled, setPartialEntryEnabled] = useState(data_detalle.map(() => false));
    const [partialAmounts, setPartialAmounts] = useState(Array(data_detalle.length).fill(''));


    //********** CLIENTES *******/
    const [datos_clientes, setdatos_clientes] = useState([]);
    const [selectedOption, setSelectedOption] = useState("");

    //********** CHECKBOX PLACA */
    const [isChecked, setChecked] = useState(false);
    const [inputEnabled, setinputEnabled] = useState(false);
    const [placa_nueva, setplaca_nueva] = useState('');

    //******* SERVICIOS */
    const [datos_servicios, setdatos_servicios] = useState([]);
    const [selectedOption_servicios, setselectedOption_servicios] = useState("");

    //******* DESTINOS */
    const [datos_destinos, setdatos_destinos] = useState([]);
    const [selectedOption_destinos, setselectedOption_destinos] = useState("");




    //********** CLIENTES ******/
    const handleOptionChange = (value) => {
        // 
        setSelectedOption(value);
    };

    function Cargar_Clientes() {
        let url = "clientes/Cargar_Clientes_m"
        fetchData(url, [], function (x) {


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

    //********* CARGAR SERVICIOS  */

    const handleOptionChange_servicios = (value) => {
        setselectedOption_servicios(value);
    }
    function Cargar_Servicios() {
        let url = "despacho/Cargar_Gui_Servicios"

        fetchData(url, [], function (x) {
            console.log('x: ', x);

            let datos = x[0];
            if (x[1] == 0) {

            } else {
                let t = [];
                datos.map(function (x) {
                    let b = {
                        key: x.ID,
                        label: x.nombre
                    }
                    t.push(b)
                })
                setdatos_servicios(t)
            }
        })
    }

    //********* CARGAR DESTINOS  */

    const handleOptionChange_destinos = (value) => {
        setselectedOption_destinos(value);
    }
    function Cargar_Destinos() {
        let url = "despacho/Cargar_Gui_Destinos"
        fetchData(url, [], function (x) {
            console.log('x: ', x);

            let datos = x[0];
            if (x[1] == 0) {

            } else {
                let t = [];
                datos.map(function (x) {
                    let b = {
                        key: x.ID,
                        label: x.nombre
                    }
                    t.push(b)
                })
                setdatos_destinos(t)
            }
        })
    }


    useEffect(() => {
        const datos_sesion = route.params;
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"])
        Cargar_Clientes();
        Cargar_Servicios();
        Cargar_Destinos();
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
        setisFormVisible(true);

        fetchData(url, param, function (x) {

            let val = x[2];

            if (val == 1) {
                Llenar_Guia(x)
            } else {
                Alert.alert("", x[0].toString())
            }
        });

    };

    function Llenar_Guia(data) {
        // let DATOS = JSON.stringify(data);
        let CABECERA = data[0][0];
        // let DETALLE = data[1];
        let DETALLE = [{
            "CODIGO": "10016416",
            "DESCRIPCION": "Cemento Holcim Fuerte Tipo GU 50Kg - GU",
            "DESPACHADA": "", "ENTREGADA": "",
            "ID": "82", "ORD": "01",
            "PEDIDO_INTERNO": "505420272",
            "POR_DESPACHAR": "400.00",
            "UNIDAD": "SAC",
            "CANT_PARCIAL": 0,
            "PARCIAL": 0
        }, {
            "CODIGO": "91025087",
            "DESCRIPCION": "Groutex Fino Blanco 2Kg",
            "DESPACHADA": "", "ENTREGADA": "",
            "ID": "83",
            "ORD": "02",
            "PEDIDO_INTERNO": "505420272",
            "POR_DESPACHAR": "20.00",
            "UNIDAD": "UN",
            "CANT_PARCIAL": 0,
            "PARCIAL": 0
        }];
        // 
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
        let servicio = selectedOption_servicios.key;
        let entrega = selectedOption_destinos.key;
        let placa_nuev = placa_nueva;
        console.log('placa_nuev: ', placa_nuev);
        let isplaca = isChecked ? 1 : 0;
        console.log('isplaca: ', isplaca);



        if (cliente == "" || cliente == undefined) {
            Alert.alert("", "Debe seleccionar un cliente");
        } else if (servicio == "" || servicio == undefined) {
            Alert.alert("", "Debe seleccionar un servicio");
        } else if (entrega == "" || entrega == undefined) {
            Alert.alert("", "Debe seleccionar un lugar de entrega");
        } else {

            if (isplaca == 1 && (placa_nuev == "" || placa_nuev == undefined)) {
                Alert.alert("", "Debe ingresar nuevo numero de placa");
                return;
            }

            if (placa_nuev.includes("-")) {
                let letras = placa_nuev.split("-")[0];
                let num = placa_nuev.split("-")[1];
                console.log('num: ', num.length);
                if (letras.length > 4 || letras.length < 3) {
                    Alert.alert("", "Formato de placa incorrecto");
                    return;
                }

                if (num.length > 4 || num.length < 3) {
                    Alert.alert("", "Formato de placa incorrecto");
                    return;
                }

            } else {
                Alert.alert("", "Formato de placa incorrecto");
                return;
            }

            let param = {
                USUARIO: usuario,
                USUARIO_ID: usuarioid,
                PEDIDO: pedido,
                CLIENTE: cliente,
                SERVICIO: servicio,
                ENTREGA: entrega,
                PLACA_NUEVA: isplaca,
                PLACA_TEXTO: placa_nuev
                // DETALLE:data_detalle
            }
            // console.log('param: ', param);
            Alert.alert("", "Datos Guardados");
        }

        //    
        //     RESET();

        // }


    }


    const Cantidad_Parcial_Change = (text, index, item) => {
        const partialAmount = parseFloat(text);
        let maxAmount = item.POR_DESPACHAR

        if (!isNaN(partialAmount) && partialAmount > maxAmount) {
            // Si es mayor, muestra un mensaje de error o realiza alguna acción
            // Por ejemplo, puedes mostrar un mensaje de error o restablecer el valor

            Alert.alert("Cantidad no permitidad", "La cantidad maxima disponible es " + maxAmount.toString());
            const updatedPartialAmounts = [...partialAmounts];
            updatedPartialAmounts[index] = maxAmount.toString();
            setPartialAmounts(updatedPartialAmounts);

            data_detalle.map(function (x) {
                if (x.CODIGO == item.CODIGO) {
                    x.CANT_PARCIAL = maxAmount.toString()
                }
            })
            // Puedes ajustar la lógica de manejo de error según tus necesidades.
        } else {
            // Si la validación pasa, puedes actualizar el estado o realizar otras acciones
            // Por ejemplo, actualiza el estado de la cantidad parcial
            const updatedPartialAmounts = [...partialAmounts];
            updatedPartialAmounts[index] = partialAmount;
            setPartialAmounts(updatedPartialAmounts);

            data_detalle.map(function (x) {
                if (x.CODIGO == item.CODIGO) {
                    x.CANT_PARCIAL = partialAmount.toString()
                }
            })
        }
    };

    const Cantidad_Parcial_Check_Change = (newValue, index, item) => {


        const updatedCheckBoxState = [...selectedRows];
        updatedCheckBoxState[index] = newValue;
        setSelectedRows(updatedCheckBoxState);

        const updatedPartialEntryEnabled = [...partialEntryEnabled];
        updatedPartialEntryEnabled[index] = newValue;
        setPartialEntryEnabled(updatedPartialEntryEnabled);

        data_detalle.map(function (x) {
            if (x.CODIGO == item.CODIGO) {
                if (newValue == true) {
                    x.PARCIAL = 1
                } else {
                    x.PARCIAL = 0
                }
            }
        })



    };

    return (
        <ScrollView style={{ flex: 1 }}>

            <View style={styles.container}>
                {/* Barra superior con nombre de usuario y botón de salida */}
                <View style={styles.header}>
                    <Text style={styles.username}>Usuario: {usuario}</Text>
                    <TouchableOpacity onPress={handleLogout}>
                        <Text style={styles.logoutButton}>Salir</Text>
                    </TouchableOpacity>
                </View>
                {/* <Stack.Navigator>
                <Stack.Screen name="Menu" component={Menu} />
            </Stack.Navigator> */}

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
                    {/* {scanned && (
                    <View style={styles.scanResultContainer}>
                        <Text>Código Escaneado: {scannedData?.data}</Text>
                    </View>
                )} */}
                    {isFormVisible && (
                        <View style={styles.formContainer}>
                            {/* <Text style={styles.label}>Fecha de Emisión:</Text>
                        <Text style={styles.text}>{fecha_emision}</Text>
                        <Text style={styles.label}>Pedido Interno:</Text>
                        <Text style={styles.text}>{pedido}</Text> */}
                            <View style={styles.rowContainer}>
                                <View style={styles.column}>
                                    <Text style={styles.label}>Fecha de Emisión:</Text>
                                    <Text style={styles.text}>{fecha_emision}</Text>
                                </View>
                                <View style={styles.column}>
                                    <Text style={styles.label}>Pedido Interno:</Text>
                                    <Text style={styles.text}>{pedido}</Text>
                                </View>
                                {/* Agregar más campos y valores aquí */}
                            </View>
                            <ScrollView horizontal={true}>



                                <View style={styles.container}>
                                    {/* Encabezados de la tabla */}
                                    <View style={styles.row}>
                                        <Text style={[styles.columnHeader, { width: 50 }]}>ORD</Text>
                                        <Text style={[styles.columnHeader, { width: 100 }]}>CODIGO</Text>
                                        <Text style={[styles.columnHeader, { width: 300 }]}>DESCRIPCION</Text>
                                        <Text style={[styles.columnHeader, { width: 80 }]}>UNIDAD</Text>
                                        <Text style={[styles.columnHeader, { width: 110 }]}>POR DESPACHAR</Text>
                                        <Text style={[styles.columnHeader, { width: 100 }]}>ENT. PARCIAL</Text>
                                        <Text style={[styles.columnHeader, { width: 110 }]}>CANT. PARCIAL</Text>
                                    </View>

                                    {data_detalle.map((item, index) => (
                                        <View style={styles.row} key={index}>
                                            <Text style={[styles.cell, { width: 50 }]}>{item.ORD}</Text>
                                            <Text style={[styles.cell, { width: 100 }]}>{item.CODIGO}</Text>
                                            <Text style={[styles.cell, { width: 300 }]}>{item.DESCRIPCION}</Text>
                                            <Text style={[styles.cell, { width: 80 }]}>{item.UNIDAD}</Text>
                                            <Text style={[styles.cell, { width: 110 }]}>{item.POR_DESPACHAR}</Text>
                                            <View style={[styles.cell, { width: 110 }]}>
                                                <Checkbox style={{ width: 25, height: 25 }}
                                                    value={selectedRows[index]}
                                                    onValueChange={(text) => Cantidad_Parcial_Check_Change(text, index, item)}
                                                />
                                            </View>

                                            <TextInput
                                                style={[styles.cell, { width: 110, backgroundColor: partialEntryEnabled[index] ? undefined : '#B2BABB', }]}
                                                placeholder="Cantidad Parcial"
                                                keyboardType="numeric" // Teclado numérico
                                                onChangeText={(text) => Cantidad_Parcial_Change(text, index, item)}
                                                editable={partialEntryEnabled[index] == true}
                                                value={partialAmounts[index]}
                                            />
                                        </View>
                                    ))}
                                </View>


                            </ScrollView>
                            <View style={[styles.clientSelector, { marginTop: 20 }]}>
                                <Text style={{ fontSize: 16, fontWeight: 'bold' }}>Seleccione un cliente *</Text>
                                <ModalSelector
                                    // keyExtractor={(item) => item.key}
                                    data={datos_clientes}
                                    initValue="Seleccione"
                                    onChange={(option) => {
                                        handleOptionChange(option);
                                        // Handle the selected option here
                                    }}
                                />
                                <Text style={{ fontSize: 16, fontWeight: 'bold' }}>Seleccione un servicio *</Text>
                                <ModalSelector
                                    // keyExtractor={(item) => item.key}
                                    data={datos_servicios}
                                    initValue="Seleccione"
                                    onChange={(option) => {
                                        handleOptionChange_servicios(option);
                                        // Handle the selected option here
                                    }}
                                />
                                <Text style={{ fontSize: 16, fontWeight: 'bold' }}>Seleccione lugar de entrega *</Text>
                                <ModalSelector
                                    // keyExtractor={(item) => item.key}
                                    data={datos_destinos}
                                    initValue="Seleccione"
                                    onChange={(option) => {
                                        handleOptionChange_destinos(option);
                                        // Handle the selected option here
                                    }}
                                />

                                <Text style={{ fontSize: 16, fontWeight: 'bold' }}>Cambiar placa * (check para cambiar)</Text>
                                <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                                    <Checkbox
                                        style={{ margin: 10 }}
                                        value={isChecked}
                                        onValueChange={setChecked}
                                        color={isChecked ? '#4630EB' : undefined}
                                    />
                                    <TextInput
                                        style={{
                                            flex: 1,
                                            borderWidth: 1,
                                            borderColor: 'gray',
                                            padding: 8,
                                            backgroundColor: isChecked == false ? '#B2BABB' : undefined,
                                        }}
                                        placeholder="Ingrese su texto"
                                        editable={isChecked}
                                        onChangeText={(text) => setplaca_nueva(text)}
                                        value={placa_nueva}
                                    />
                                </View>

                            </View>

                            <View style={styles.footer}>
                                <TouchableOpacity onPress={Guardar_datos_guia} style={styles.guardarButton}>
                                    <Text style={styles.guardarButtonText}>Guardar datos</Text>
                                </TouchableOpacity>
                            </View>
                        </View>
                    )}
                </View>
            </View >
        </ScrollView>

    )
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    row: {
        flexDirection: 'row',
        borderBottomWidth: 1,
        borderBottomColor: 'black',
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
        // width: 100,
        justifyContent: 'center',
        alignItems: 'center',
        fontWeight: 'bold',
        padding: 10,

    },
    cell: {
        // width: 100,
        justifyContent: 'center',
        alignItems: 'center',
        // flex: 1,
        padding: 10,
        marginTop: 10,
        marginBottom: 10
        // textAlign: 'center',
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
    rowContainer: {
        flexDirection: 'row',
        justifyContent: 'space-between',
    },
    column: {
        flex: 1,
        marginLeft: 10, // Espacio entre columnas (ajusta según tu preferencia)
    },
});


{/* <DataTable style={{ marginTop: 20 }}>
<DataTable.Header>
    <DataTable.Title style={[styles.columnHeader, { width: 50 }]}>ORD</DataTable.Title>
    <DataTable.Title style={[styles.columnHeader, { width: 100 }]} numeric>CÓDIGO</DataTable.Title>
    <DataTable.Title style={[styles.columnHeader, { width: 300 }]} numeric>DESCRIPCIÓN</DataTable.Title>
    <DataTable.Title style={[styles.columnHeader, { width: 80 }]} numeric>UNIDAD</DataTable.Title>
    <DataTable.Title style={[styles.columnHeader, { width: 120 }]} numeric>POR DESPACHAR</DataTable.Title>
    <DataTable.Title style={[styles.columnHeader, { width: 80 }]}>Ent. Total</DataTable.Title>
    <DataTable.Title style={[styles.columnHeader, { width: 80 }]}>cant. Parcial</DataTable.Title>

</DataTable.Header>
{data_detalle.map((item, index) => (
    <DataTable.Row key={index}>

        <DataTable.Cell style={[styles.cell, { width: 50 }]}>{item.ORD}</DataTable.Cell>
        <DataTable.Cell style={[styles.cell, { width: 100 }]} numeric>{item.CODIGO}</DataTable.Cell>
        <DataTable.Cell style={[styles.cell, { width: 300 }]}>{item.DESCRIPCION}</DataTable.Cell>
        <DataTable.Cell style={[styles.cell, { width: 80 }]}>{item.UNIDAD}</DataTable.Cell>
        <DataTable.Cell style={[styles.cell, { width: 120 }]}>{item.POR_DESPACHAR}</DataTable.Cell>
        <DataTable.Cell style={[styles.cell, { width: 80 }]}>
            {/* <Checkbox
                value={selectedRows[index]}
                onValueChange={(newValue) => {
                    // const updatedSelectedRows = [...selectedRows];
                    // updatedSelectedRows[index] = newValue;
                    // setSelectedRows(updatedSelectedRows);

                    // // Habilita o deshabilita el campo de entrada numérica
                    // const updatedPartialEntryEnabled = [...partialEntryEnabled];
                    // updatedPartialEntryEnabled[index] = newValue;
                    // setPartialEntryEnabled(updatedPartialEntryEnabled);
                }}
            /> */}
//     <TouchableOpacity onPress={() => } style={styles.guardarButton}>
//         <Text style={styles.guardarButtonText}>Guardar datos</Text>
//     </TouchableOpacity>
// </DataTable.Cell>
// <DataTable.Cell style={[styles.cell, { width: 80 }]}>
{/* {selectedRows[index] == false ? (
                <TextInput
                    style={{
                        flex: 1,
                        borderWidth: 1,
                        borderColor: 'gray',
                        padding: 8,
                    }}
                    placeholder="Cantidad Parcial"
                    keyboardType="numeric" // Teclado numérico
                    onChangeText={(text) => handlePartialEntryChange(text, index)}
                />
            ) : "asasds"} */}
//         </DataTable.Cell>

//     </DataTable.Row>
// ))}
// </DataTable> */}