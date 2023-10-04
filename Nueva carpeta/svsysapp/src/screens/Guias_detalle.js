import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"
import Icon from 'react-native-vector-icons/FontAwesome'; // Puedes cambiar 'FontAwesome' por el conjunto de íconos que estés usando

export default function Guias_detalle({ route, navigation }) {
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');
    const [pedido_interno, setpedido_interno] = useState('');


    //**** DATOS TABLA *****/
    const [data_detalle, setdata_detalle] = useState([]);
    const [data_detalle_dt, setdata_detalle_dt] = useState([]);

    const datos_sesion = route.params;

    useEffect(() => {
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"]);
        setpedido_interno(datos_sesion["PEDIDO_INTERNO"]);
        // Consultar_guias(estado_filtro)
        Consultar_guia_despachadas(datos_sesion["Usuario_ID"], datos_sesion["PEDIDO_INTERNO"])
    }, []);

    function Consultar_guia_despachadas(usuario, pedido) {
        let param = {
            PEDIDO_INTERNO: pedido,
            USUARIO: usuario,
        }
        console.log('param: ', param);
        let url = 'despacho/Consultar_guia_despachadas'
        fetchData(url, param, function (x) {
            console.log('x: ', x);
            setdata_detalle(x);
        });
    }

    function Consultar_Detalle(text, index, item) {
        console.log('item: ', item);

        let param = {
            PEDIDO_INTERNO: pedido_interno,
            DESPACHO_ID: item.despacho_ID
        }
        console.log('param: ', param);
        let url = 'despacho/Consultar_guia_despachadas_dt'
        fetchData(url, param, function (x) {
            console.log('x: ', x);
            setdata_detalle_dt(x);
        });

    }

    const handleLogout = () => {
        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
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


                <View style={styles.card}>

                    <View style={styles.formContainer}>
                        <View style={styles.rowContainer}>
                            <View style={styles.column}>
                                <Text style={[styles.label, { fontSize: 14 }]}>Fecha de Emisión:</Text>
                                <Text style={styles.text}>asd</Text>
                            </View>
                            <View style={styles.column}>
                                <Text style={styles.label}>Pedido interno:</Text>
                                <Text style={styles.text}>asd</Text>
                            </View>
                            {/* Agregar más campos y valores aquí */}
                        </View>
                        <View style={styles.rowContainer}>
                            <View style={styles.column}>
                                <Text style={styles.label}>Estado:</Text>
                                <Text style={styles.text}>asd</Text>
                            </View>
                            {/* Agregar más campos y valores aquí */}
                        </View>

                        <ScrollView horizontal={true} style={{ marginTop: 25 }}>
                            <View style={styles.container}>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 60 }]}></Text>
                                    <Text style={[styles.columnHeader, { width: 80 }]}>FECHA</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>CLIENTE</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>SERVICIO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>DESTINO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>ENTREGA</Text>
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
                                        <Text style={[styles.cell, { width: 80, fontWeight: "bold" }]}>{item.FECHA_CREADO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.CLIENTE_NOMBRE}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.SERVICIO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.DESTINO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15,color: item.PARCIAL == 0 ? '#27AE60' : '#E74C3C
                                        '}]}>{item.PARCIAL == 0 ? 'COMPLETA' : 'PARCIAL'}</Text>

                                    </View>
                                ))}
                            </View>


                        </ScrollView>

                        <ScrollView horizontal={true} style={{ marginTop: 25 }}>
                            <View style={styles.container}>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 80 }]}>CODIGO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>PRODUCTO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>CANTIDAD</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>DESPACHADA</Text>
                                </View>

                                {data_detalle_dt.map((item, index) => (
                                    <View style={[styles.row, { backgroundColor: item.PARCIAL == 1 ? '#FADBD8' : '#EAFAF1' }]} key={index}>
                                        <Text style={[styles.cell, { width: 80, fontWeight: "bold" }]}>{item.CODIGO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.CLIENTE_NOMBRE}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.SERVICIO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.DESTINO}</Text>

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

