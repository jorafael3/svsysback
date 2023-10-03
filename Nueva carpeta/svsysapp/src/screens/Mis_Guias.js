import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView } from 'react-native';

import fetchData from "../config/config"

export default function Mis_guias({ route, navigation }) {
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');

    //**** DATOS TABLA *****/
    const [data_detalle, setdata_detalle] = useState([]);

    const datos_sesion = route.params;

    useEffect(() => {
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"])
        Consultar_guias()
    }, []);

    const handleLogout = () => {
        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
    };


    function Consultar_guias() {
        let USUARIO = usuarioid;
        let param = {
            USUARIO_ID: datos_sesion["Usuario_ID"]
        }
        console.log('param: ', param);
        let url = 'despacho/Guias_Usuario'
        fetchData(url, param, function (x) {
            console.log('x: ', x);
            setdata_detalle(x)
        })

    }

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


                <View style={styles.card}>

                    <View style={styles.formContainer}>

                        <ScrollView horizontal={true}>



                            <View style={styles.container}>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 80 }]}>ESTADO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>PEDIDO #</Text>
                                    <Text style={[styles.columnHeader, { width: 60 }]}>MAS</Text>
                                </View>

                                {data_detalle.map((item, index) => (
                                    <View style={styles.row} key={index}>
                                        <Text style={[styles.cell, { width: 80, fontWeight: "bold", backgroundColor: item.ESTADO_DESPACHO == 1 ? 'red' : 'green', color: 'white' }]}>{item.ESTADO_DESPACHO_TEXTO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold" }]}>{item.PEDIDO_INTERNO}</Text>
                                        <TouchableOpacity
                                            style={[styles.cell, { width: 60, backgroundColor: 'blue', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }]}
                                            onPress={() => {
                                                // Acción a realizar cuando se presiona el botón "MÁS"
                                            }}
                                        >
                                            <Text style={{ color: 'white', fontWeight: 'bold' }}>+</Text>
                                        </TouchableOpacity>
                                    </View>
                                ))}
                            </View>


                        </ScrollView>

                    </View>
                </View>
            </View >
        </ScrollView>
    );
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

