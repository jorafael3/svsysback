import React, { useState, Component, useEffect } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Alert, ScrollView, TextInput, ActivityIndicator } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"
import Checkbox from 'expo-checkbox';
import * as Location from 'expo-location';
import * as ImagePicker from 'expo-image-picker';
import Icon from 'react-native-vector-icons/FontAwesome'; // Importa el icono

export default function Guias_parcial({ route, navigation }) {
    const datos_sesion = route.params;

    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');
    const [pedido_interno, setpedido_interno] = useState('');


    //******* DATOS TABLA */
    const [isFormVisible, setisFormVisible] = useState(false);
    const [fecha_emision, setfecha_emision] = useState('');
    const [pedido, setpedido] = useState('');
    const [data_detalle, setdata_detalle] = useState([]);
    const [selectedRows, setSelectedRows] = useState([]); // Arreglo para almacenar las selecciones
    const [selectedRows_No, setSelectedRows_No] = useState([]); // Arreglo para almacenar las selecciones
    const [partialEntryEnabled, setPartialEntryEnabled] = useState(data_detalle.map(() => false));
    const [partialAmounts, setPartialAmounts] = useState(Array(data_detalle.length).fill(''));
    const [check_parcial_state, setcheck_parcial_state] = useState([]);


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

    const [image, setImage] = useState(null);
    const [loading, setLoading] = useState(false);

    const [btn_guardar_est, setbtn_guardar_est] = useState(false);


    //********** CLIENTES ******/
    const handleOptionChange = (value) => {
        // 
        setSelectedOption(value);
    };

    function Cargar_Clientes() {
        let url = "clientes/Cargar_Clientes_m"
        fetchData(url, [], function (x) {
            if (x == -1) {

            } else {
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
            if (x == -1) {
            } else {
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
            if (x == -1) {

            } else {
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
            }


        })
    }

    const handleLogout = () => {
        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
    };

    useEffect(() => {
        const datos_sesion = route.params;
        console.log('datos_sesion: ', datos_sesion);
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"])
        setpedido_interno(datos_sesion["PEDIDO_INTERNO"]);

        Cargar_Clientes();
        Cargar_Servicios();
        Cargar_Destinos();
        Cargar_guia(datos_sesion["PEDIDO_INTERNO"]);
    }, []);

    //************************************** */
    //*********** GUIAS */
    const Cargar_guia = (pedido, despacho) => {
        let url = 'despacho/Cargar_Guia_parcial';
        const param = {
            PEDIDO_INTERNO: pedido,
            DESPACHO_ID: despacho,
        };
        fetchData(url, param, function (x) {
            console.log('x: ', x);

            if (x == -1) {
                Alert.alert("Error de conexion", "Asegurese que este conectado a internet");
            } else {
                let val = x[2];
                if (val == 1) {
                    Llenar_Guia(x)
                } else {
                    Alert.alert("", x[0].toString())
                }
            }
        });

    };

    function Llenar_Guia(data) {
        // let DATOS = JSON.stringify(data);
        let CABECERA = data[0][0];
        let DETALLE = data[1];
        // 
        if (CABECERA.length == 0) {
            Alert.alert("", "Guia no encontrada, verifique el numero, o vuelva a escanear");
        } else {
            console.log('placa: ', placa);

            if (CABECERA["placa"] == datos_sesion["PLACA"]) {
                setbtn_guardar_est(true);
                setisFormVisible(true);
                setfecha_emision(CABECERA["FECHA_DE_EMISION"]);
                setpedido(CABECERA["PEDIDO_INTERNO"]);
                DETALLE.map(function (x) {
                    x.PARCIAL = 0;
                    x.CANT_PARCIAL = "NaN";
                    x.NO_ENTREGAR_CODIGO = 0;
                });

                let filtro_solo_por_entregar = DETALLE.filter(item => item.RESTANTE > 0);
                setdata_detalle(filtro_solo_por_entregar)
                // Alert.alert("asdasd", data[0][0]["ID"]);
            } else if (CABECERA["placa"] == null) {
                Alert.alert("ESTA GUIA NO TIENE CHOFER ASIGNADO", CABECERA["placa"]);
                navigation.navigate('Guias', datos_sesion);
                setbtn_guardar_est(false);

            } else {
                navigation.navigate('Guias', datos_sesion);
                Alert.alert("GUIA ASOCIADA A OTRA PLACA", CABECERA["placa"]);
                setbtn_guardar_est(false);
            }


        }

    }

    //**********************/******** */ */
    //*****  GUARDAR DATOS*/
    function Guardar_datos_guia() {
        let cliente = selectedOption.key;
        let servicio = selectedOption_servicios.key;
        let entrega = selectedOption_destinos.key;
        let placa_nuev = placa_nueva;

        let isplaca = isChecked == true ? 1 : 0;




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
            if (isplaca == 1) {
                if (placa_nuev.includes("-")) {
                    let letras = placa_nuev.split("-")[0];
                    let num = placa_nuev.split("-")[1];

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
            }





            let val = 0;
            data_detalle.map(function (x) {
                if ((x.PARCIAL == 1 && x.CANT_PARCIAL == "NaN")
                    || (x.PARCIAL == 1 && parseFloat(x.CANT_PARCIAL) <= 0)) {
                    val = val + 1;
                }

                let CANTIDAD_PARCIAL_TOTAL = parseFloat(x.CANTIDAD_PARCIAL_TOTAL);
                let RESTANTE = parseFloat(x.RESTANTE);
                let CANT_PARCIAL = parseFloat(x.CANT_PARCIAL);
                let PARCIAL = (x.PARCIAL);
                if (PARCIAL == 1) {
                    if (RESTANTE - CANT_PARCIAL == 0) {
                        x.PARCIAL = 0;
                    }
                }
                if (x.NO_ENTREGAR_CODIGO == 1) {
                    x.CANT_PARCIAL = 0;
                    x.PARCIAL = 1;
                }
            });
            (async () => {
                let { status } = await Location.requestForegroundPermissionsAsync();
                if (status !== 'granted') {
                    Alert.alert("Permisos de ubicacion no activados", "Porfavor permita el acceso a la ubicacion para continuar");
                    setErrorMsg('Permission to access location was denied');
                    return;
                }

                let location = await Location.getCurrentPositionAsync({});


                let param = {
                    USUARIO: usuario,
                    CREADO_POR: usuarioid,
                    PEDIDO_INTERNO: pedido,
                    CLIENTE_ENTREGA_ID: cliente,
                    SERVICIO_ID: servicio,
                    DESTINO_ID: entrega,
                    PLACA_CAMBIADA: isplaca,
                    PLACA_CAMBIADA_NUMERO: isplaca == 0 ? "" : placa_nuev,
                    PARCIAL: data_detalle.filter(item => item.PARCIAL == 1).length > 0 ? 1 : 0,
                    DETALLE: data_detalle,
                    UBICACION: location["coords"]["latitude"] + "," + location["coords"]["longitude"],
                    IMAGEN: image


                }

                if (val > 0) {
                    Alert.alert("Error en cantidad parcial", "La cantidad parcial no puede estar vacia o ser menor o igual a 0");
                } else {
                    let url = 'despacho/Guardar_Guias_despacho_parcial';

                    setLoading(true);
                    fetchData(url, param, function (x) {

                        setLoading(false);

                        if (x == -1) {
                            Alert.alert("Error de conexion", "Asegurese que este conectado a internet");
                        } else {
                            let CAB = x[0];
                            let DET = x[1];
                            if (CAB["GUARDADO"] == 1 && DET["GUARDADO"] == 1) {
                                setdata_detalle([]);
                                Alert.alert("Datos Guardados", "Los datos se guardaron con exito");
                                navigation.goBack('Guias_detalle', datos_sesion);
                                // navigation.reset({
                                //     index: 0,
                                //     routes: [{ name: "Guias_detalle", datos_sesion }],
                                // });

                            } else {
                                if (CAB["GUARDADO"] == 0) {
                                    Alert.alert("Error al guardar los datos", (CAB["MENSAJE"]).toString());
                                } else if (DET["GUARDADO"] == 0) {
                                    Alert.alert("Error al guardar los datos", (DET["MENSAJE"]).toString());
                                }
                            }
                        }


                    })
                }
            })();



        }

        //    
        //     RESET();

        // }


    }

    const Cantidad_Parcial_Change = (text, index, item) => {
        const partialAmount = parseFloat(text);

        let maxAmount = item.POR_DESPACHAR - item.CANTIDAD_PARCIAL_TOTAL

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
            });
            setdata_detalle(data_detalle);

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
            });

            setdata_detalle(data_detalle);

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
        });
        setdata_detalle(data_detalle);



    };

    const Cantidad_Parcial_Check_Change_No = (newValue, index, item) => {

        // if (newValue == true) {
        //     newValue = false;
        // } else {
        //     newValue = true;
        // }

        let chek_seleccionado = selectedRows[index]


        const updatedCheckBoxState = [...selectedRows_No];
        updatedCheckBoxState[index] = newValue;
        setSelectedRows_No(updatedCheckBoxState);

        let ne = true;
        if (chek_seleccionado == false) {
            ne = false;
        } else {
            if (newValue == true) {
                ne = false
            }
        }

        const updatedPartialEntryEnabled = [...partialEntryEnabled];
        updatedPartialEntryEnabled[index] = ne;
        setPartialEntryEnabled(updatedPartialEntryEnabled);

        const updatedCheckBoxState_no = [...selectedRows_No];
        updatedCheckBoxState_no[index] = newValue;
        setcheck_parcial_state(updatedCheckBoxState_no);


        data_detalle.map(function (x) {
            if (x.CODIGO == item.CODIGO) {
                if (newValue == true) {
                    x.NO_ENTREGAR_CODIGO = 1
                }
            }
        });
        setdata_detalle(data_detalle);



    };


    const Subir_imagen = async () => {

        let picker = await ImagePicker.launchImageLibraryAsync({ base64: true });
        let tipo = picker["assets"][0]["uri"];
        tipo = tipo.split(".")[1];

        let a = {
            image: picker["assets"][0]["base64"],
            // type: tipo
        }
        setImage(a);
    };

    return (
        <ScrollView style={{ flex: 1 }}>
            <View style={styles.container}>
                {/* Barra superior con nombre de usuario y botón de salida */}
                <View style={styles.header}>
                    <Text style={styles.username}>Usuario: {usuario}</Text>
                    <Text style={styles.username}>Placa: {placa}</Text>
                    <TouchableOpacity onPress={handleLogout}>
                        {/* <Text style={styles.logoutButton}>Salir</Text> */}
                    </TouchableOpacity>
                </View>
                {/* <Stack.Navigator>
                <Stack.Screen name="Menu" component={Menu} />
            </Stack.Navigator> */}
                {loading && <ActivityIndicator size="large" color="black" style={{ margin: 5 }} />}

                <View style={styles.card}>
                    <View style={styles.formContainer}>
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
                                    <Text style={[styles.columnHeader, { width: 110 }]}>DESPACHADO</Text>
                                    <Text style={[styles.columnHeader, { width: 110 }]}>RESTANTE</Text>
                                    {data_detalle.length > 1 && (
                                        <Text style={[styles.columnHeader, { width: 100 }]}>NO ENTREGAR</Text>
                                    )}
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
                                        <Text style={[styles.cell, { width: 110 }]}>{parseFloat(item.CANTIDAD_PARCIAL_TOTAL).toFixed(2)}</Text>
                                        <Text style={[styles.cell, { width: 110, backgroundColor: "#D5F5E3", fontWeight: "bold" }]}>{parseFloat(item.RESTANTE).toFixed(2)}</Text>
                                        {data_detalle.length > 1 && (
                                            <View style={[styles.cell, { width: 110 }]}>

                                                <Checkbox style={{ width: 25, height: 25, backgroundColor: "red" }}
                                                    value={selectedRows_No[index]}
                                                    onValueChange={(text) => Cantidad_Parcial_Check_Change_No(text, index, item)}
                                                />
                                            </View>
                                        )}

                                        <View style={[styles.cell, { width: 110 }]}>
                                            <Checkbox style={{ width: 25, height: 25 }}
                                                value={selectedRows[index]}
                                                onValueChange={(text) => Cantidad_Parcial_Check_Change(text, index, item)}
                                                disabled={check_parcial_state[index]}
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
                            <TouchableOpacity onPress={Subir_imagen} style={styles.subirButton}>
                                <View style={styles.buttonContent}>
                                    <Icon name="camera" size={20} color="white" />
                                    <Text style={styles.guardarButtonText}>  Cargar foto guía firmada  </Text>
                                    {image != null && (
                                        <Icon name="check" size={20} color="yellow" />
                                    )}
                                </View>
                            </TouchableOpacity>
                            <Text style={{ fontSize: 16, fontWeight: 'bold', marginTop: 15 }}>Cambiar placa * (check para cambiar)</Text>
                            <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 10 }}>
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
                            <TouchableOpacity onPress={() => {
                                if (btn_guardar_est == true) {
                                    Alert.alert(
                                        'Se guardarn los datos seleccionados',
                                        'Porfavor asegurese que sean los correctos',
                                        [
                                            {
                                                text: 'Cancelar',
                                                // onPress: () => Guardar_datos_guia(),
                                                style: 'cancel',
                                            },
                                            {
                                                text: 'Si, continuar!',
                                                onPress: () => Guardar_datos_guia(),
                                            },
                                        ],
                                        { cancelable: false },
                                    );
                                }


                            }} style={styles.guardarButton} disabled={loading}>
                                <View style={styles.buttonContent}>
                                    <Text style={styles.guardarButtonText}>Guardar datos  </Text>
                                    <Icon name="save" size={25} color="white" />

                                </View>
                            </TouchableOpacity>
                        </View>
                    </View>
                </View>
            </View >
        </ScrollView>

    )
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#ffffff',
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
        marginBottom: 10,
    },
    username: {
        fontSize: 16,
        fontWeight: 'bold',
    },
    logoutButton: {
        fontSize: 16,
        color: 'red',
        fontWeight: 'bold',
    },
    card: {
        flex: 1,
        margin: 10,
        padding: 10,
        borderRadius: 10,
        backgroundColor: '#F4F6F6',
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
        borderRadius: 20,
        marginHorizontal: 8,
        alignItems: 'center',
        fontWeight: 'bold',

    },
    buttonText: {
        color: 'white',
        fontWeight: 'bold',
        fontSize: 14

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
        backgroundColor: "#000000",
        color: "#ffffff"
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
        backgroundColor: 'red',
        padding: 15,
        borderRadius: 20,
        alignItems: 'center',
        justifyContent: 'center', // Mantenemos 'center' para centrar verticalmente
    },
    subirButton: {
        backgroundColor: '#273746',
        padding: 12,
        borderRadius: 6,
        alignItems: 'center',
        justifyContent: 'center', // Mantenemos 'center' para centrar verticalmente
        marginTop: 10
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
    buttonContent: {
        flexDirection: 'row', // Coloca los elementos en una fila
        alignItems: 'center', // Alinea los elementos verticalmente
        justifyContent: 'center', // Alinea los elementos horizontalmente
    },


});
