import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"
import Icon from 'react-native-vector-icons/FontAwesome'; // Puedes cambiar 'FontAwesome' por el conjunto de íconos que estés usando
import { useIsFocused } from '@react-navigation/native'

export default function Guias_detalle({ route, navigation }) {
    const isFocused = useIsFocused()
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');
    const [pedido_interno, setpedido_interno] = useState('');

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

