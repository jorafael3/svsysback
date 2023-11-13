import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView, Alert, Button } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"
import Icon from 'react-native-vector-icons/FontAwesome'; // Puedes cambiar 'FontAwesome' por el conjunto de íconos que estés usando
import { useIsFocused } from '@react-navigation/native'
import DateTimePicker from '@react-native-community/datetimepicker';
import moment from 'moment';

require('moment/locale/es'); // Importa el idioma español


export default function Guias_Asignadas_Detalle({ route, navigation }) {
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
        setplaca(datos_sesion["PLACA"]);
        Cargar_guia_cabecera();

    }, [isFocused]);

    function Cargar_guia_cabecera() {
        let param = {
            PEDIDO_INTERNO: datos_sesion.PEDIDO_INTERNO,
            // USUARIO: usuario,
        }

        let url = 'despacho/Cargar_guias_asignadas_detalle'
        fetchData(url, param, function (x) {
            console.log('x: ', x);
            let cab = x[0];
            let det = x[1];
            setfecha_emision(cab[0]["FECHA_DE_EMISION"]);
            setpedido_interno(datos_sesion.PEDIDO_INTERNO);
            setdata_detalle(det);
        });
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
                    <Text style={{fontSize:24}}>Datos generales de la guia</Text>

                        <View style={[styles.rowContainer,{marginTop:25}]}>
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
                        <ScrollView horizontal={true}>
                            <View style={[styles.container,{marginTop:25}]}>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 50 }]}>ORD</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>CODIGO</Text>
                                    <Text style={[styles.columnHeader, { width: 300 }]}>DESCRIPCION</Text>
                                    <Text style={[styles.columnHeader, { width: 80 }]}>UNIDAD</Text>
                                    <Text style={[styles.columnHeader, { width: 110 }]}>POR DESPACHAR</Text>
        
                                </View>

                                {data_detalle.map((item, index) => (
                                    <View style={styles.row} key={index}>
                                        <Text style={[styles.cell, { width: 50 }]}>{item.ORD}</Text>
                                        <Text style={[styles.cell, { width: 100 }]}>{item.CODIGO}</Text>
                                        <Text style={[styles.cell, { width: 300 }]}>{item.DESCRIPCION}</Text>
                                        <Text style={[styles.cell, { width: 80 }]}>{item.UNIDAD}</Text>
                                        <Text style={[styles.cell, { width: 110, fontWeight: "bold" }]}>{item.POR_DESPACHAR}</Text>
                                       
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