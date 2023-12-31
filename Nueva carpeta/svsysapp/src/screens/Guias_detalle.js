import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"
import Icon from 'react-native-vector-icons/FontAwesome'; // Puedes cambiar 'FontAwesome' por el conjunto de íconos que estés usando
import { Image, Alert } from 'react-native';
import { useIsFocused } from '@react-navigation/native'
import * as ImagePicker from 'expo-image-picker';

export default function Guias_detalle({ route, navigation }) {
    const isFocused = useIsFocused()
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');
    const [pedido_interno, setpedido_interno] = useState('');
    const [despachoid, setdespachoid] = useState('');

    //***** DATOS CABECERA ******/
    const [fecha_emision, setfecha_emision] = useState('');
    const [estado_guia_text, setestado_guia_text] = useState('');
    const [estado_guia, setestado_guia] = useState('');
    // const [pedido_interno, setpedido_interno] = useState('');


    //**** DATOS TABLA *****/
    const [data_cabecera, setdata_cabecera] = useState([]);
    const [data_detalle, setdata_detalle] = useState([]);
    const [data_detalle_dt, setdata_detalle_dt] = useState([]);

    const datos_sesion = route.params;

    const [image, setImage] = useState(null);


    useEffect(() => {



        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"]);
        setpedido_interno(datos_sesion["PEDIDO_INTERNO"]);
        // Consultar_guias(estado_filtro)
        Consultar_guia_despachadas(datos_sesion["Usuario_ID"], datos_sesion["PEDIDO_INTERNO"]);
        Cargar_guia_cabecera(datos_sesion["PEDIDO_INTERNO"]);
    }, [isFocused]);

    function Cargar_guia_cabecera(pedido) {
        let param = {
            PEDIDO_INTERNO: pedido,
            // USUARIO: usuario,
        }

        let url = 'despacho/Consultar_guia_despachadas_cabecera'
        fetchData(url, param, function (x) {

            setfecha_emision(x[0]["FECHA_DE_EMISION"]);
            setestado_guia_text(x[0]["ESTADO_DESPACHO_TEXTO"]);
            setestado_guia(x[0]["ESTADO_DESPACHO"]);
            setdespachoid(x[0]["despacho_ID"]);
            setdata_cabecera(x);
        });
    }

    function Consultar_guia_despachadas(usuario, pedido) {
        let param = {
            PEDIDO_INTERNO: pedido,
            USUARIO: usuario,
        }

        let url = 'despacho/Consultar_guia_despachadas'
        fetchData(url, param, function (x) {
            console.log('x: ', x);

            setdata_detalle(x);
        });
    }

    function Consultar_Detalle(text, index, item) {


        let param = {
            PEDIDO_INTERNO: pedido_interno,
            DESPACHO_ID: item.despacho_ID
        }

        let url = 'despacho/Consultar_guia_despachadas_dt'
        fetchData(url, param, function (x) {

            setdata_detalle_dt(x);
        });

    }

    function Completar_Parcial() {
        let PEDIDO_INTERNO = pedido_interno;
        datos_sesion.PEDIDO_INTERNO = PEDIDO_INTERNO;
        navigation.navigate('Guias_parcial', datos_sesion);
    }

    const Subir_imagen = async () => {
        let picker = await ImagePicker.launchImageLibraryAsync({ base64: true });

        if (picker.assets != null) {
            let tipo = picker["assets"][0]["uri"];
            tipo = tipo.split(".")[1];

            let a = {
                image: picker["assets"][0]["base64"],
            }

            setImage(a);
        }
    };

    function Guardar_imagen(text, index, item) {
        console.log('item: ', item);

        if (image == null) {
            Alert.alert("No ha seleccionado una imagen para subir", "Seleccione una imagen");
        } else {

            let param = {
                IMAGEN: image,
                PEDIDO_INTERNO: pedido_interno,
                DESPACHO_ID: item.despacho_ID
            }
            let url = 'despacho/Guardar_Imagen_guia_despachada';
            // console.log('param: ', param);

            fetchData(url, param, function (x) {
                console.log('x: ', x);
                if (x[0] == 1) {
                    Alert.alert(x[1], "");
                    Consultar_guia_despachadas(usuarioid, pedido_interno)
                } else {
                    Alert.alert("ERROR AL SUBIR IMAGEN", x[1].toString());
                }
            });

        }

    }

    

    return (
        <ScrollView style={{ flex: 1 }}>

            <View style={styles.container}>
                {/* Barra superior con nombre de usuario y botón de salida */}
                <View style={styles.header}>
                    <Text style={styles.username}>Usuario: {usuario}</Text>
                    <Text style={styles.username}>Placa: {placa}</Text>
                    {/* <TouchableOpacity onPress={handleLogout}>
                        <Text style={styles.logoutButton}>Salir</Text>
                    </TouchableOpacity> */}
                </View>


                <View style={styles.card}>

                    <View style={styles.formContainer}>
                        <View style={styles.rowContainer}>
                            <View style={styles.column}>
                                <Text style={[styles.label, { fontSize: 14 }]}>Fecha de Emisión:</Text>
                                <Text style={styles.text}>{fecha_emision}</Text>
                            </View>
                            <View style={styles.column}>
                                <Text style={styles.label}>Pedido interno:</Text>
                                <Text style={styles.text}>{pedido_interno}</Text>
                            </View>
                            {/* Agregar más campos y valores aquí */}
                        </View>
                        <View style={styles.rowContainer}>
                            <View style={styles.column}>
                                <Text style={styles.label}>Estado:</Text>
                                <Text style={[styles.text, { fontSize: 18, fontWeight: 'bold', color: estado_guia == 1 ? "#E74C3C" : "#27AE60" }]}>{estado_guia_text}</Text>
                            </View>
                            <View style={styles.column}>
                                {estado_guia == 1 ? (
                                    <TouchableOpacity
                                        style={[styles.cell, { backgroundColor: '#2980B9', borderRadius: 5 }]}
                                        onPress={(text) => Completar_Parcial()}
                                    >
                                        <Text style={{ color: '#fff', fontWeight: 'bold', marginBottom: 5 }}>
                                            Despachar <Icon name="check" size={20} color="#fff" />
                                        </Text>
                                    </TouchableOpacity>
                                ) : (
                                    ""
                                )

                                }

                            </View>

                            {/* Agregar más campos y valores aquí */}
                        </View>

                        <ScrollView horizontal={true} style={{ marginTop: 25 }}>

                            <View style={styles.container}>
                                <Text style={{ color: '#000', fontWeight: 'bold', margin: 5 }}>
                                    GUIAS
                                </Text>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 60 }]}></Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>FECHA</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>CLIENTE</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>SERVICIO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>DESTINO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>ENTREGA</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>IMAGEN</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>FOTO</Text>
                                    <Text style={[styles.columnHeader, { width: 60 }]}></Text>
                                    <Text style={[styles.columnHeader, { width: 60, display: "none" }]}></Text>
                                </View>

                                {data_detalle.map((item, index) => (
                                    <View style={styles.row} key={index}>
                                        <TouchableOpacity
                                            style={[styles.cell, { width: 60, margin: 2, backgroundColor: '#F2F4F4', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }]}
                                            onPress={(text) => Consultar_Detalle(text, index, item)}
                                        >
                                            <Text style={{ color: 'white', fontWeight: 'bold' }}>
                                                <Icon name="eye" size={25} color="#1C2833" />
                                            </Text>
                                        </TouchableOpacity>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold" }]}>{item.FECHA_CREADO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.CLIENTE_NOMBRE}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.SERVICIO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15, backgroundColor: "#FEF9E7" }]}>{item.DESTINO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15, color: item.PARCIAL == 0 ? '#27AE60' : '#E74C3C' }]}>{item.PARCIAL == 0 ? 'COMPLETA' : 'PARCIAL'}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold" }]}>{item.imagen == null || item.imagen == "" ? "NO" : "SI"}</Text>

                                        <TouchableOpacity onPress={Subir_imagen} style={styles.subirButton}>
                                            <View style={styles.buttonContent}>
                                                <Icon name="camera" size={25} color="white" />
                                                <Text style={styles.guardarButtonText}></Text>
                                                {image != null && (
                                                    <Icon name="check" size={20} color="yellow" />
                                                )}
                                            </View>
                                        </TouchableOpacity>
                                        <TouchableOpacity
                                            style={[styles.cell, { width: 60, margin: 2, backgroundColor: '#E74C3C', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }]}
                                            onPress={(text) => {
                                                Alert.alert(
                                                    'Se guardaran los datos seleccionados',
                                                    'Por favor asegurese que sean los correctos',
                                                    [
                                                        {
                                                            text: 'Cancelar',
                                                            // onPress: () => Guardar_datos_guia(),
                                                            style: 'cancel',
                                                        },
                                                        {
                                                            text: 'Si, continuar!',
                                                            onPress: () => Guardar_imagen(text, index, item),
                                                        },
                                                    ],
                                                    { cancelable: false },
                                                );
                                            }}
                                        >
                                            <Text style={{ color: 'red', fontWeight: 'bold' }}>
                                                <Icon name="save" size={30} color="#ffffff" />
                                            </Text>
                                        </TouchableOpacity>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", display: "none" }]}>{item.despacho_ID}</Text>

                                    </View>
                                ))}
                            </View>


                        </ScrollView>

                        <ScrollView horizontal={true} style={{ marginTop: 25 }}>
                            <View style={styles.container}>
                                <Text style={{ color: '#000', fontWeight: 'bold', margin: 5 }}>
                                    DETALLE
                                </Text>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 90 }]}>CODIGO</Text>
                                    <Text style={[styles.columnHeader, { width: 150 }]}>PRODUCTO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>POR DESPACHAR</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>DESPACHADA</Text>
                                </View>

                                {data_detalle_dt.map((item, index) => (
                                    <View style={[styles.row]} key={index}>
                                        <Text style={[styles.cell, { width: 90, fontWeight: "bold" }]}>{item.CODIGO}</Text>
                                        <Text style={[styles.cell, { width: 150, fontWeight: "bold", fontSize: 15 }]}>{item.DESCRIPCION}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15, backgroundColor: "#FEF9E7" }]}>{item.POR_DESPACHAR}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.PARCIAL == 1 ? parseFloat(item.CANTIDAD_PARCIAL).toFixed(2) : parseFloat(item.CANTIDAD_TOTAL).toFixed(2)}</Text>

                                    </View>
                                ))}
                            </View>


                        </ScrollView>
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

