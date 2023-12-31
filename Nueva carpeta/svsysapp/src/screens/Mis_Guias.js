import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView, Alert } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import Checkbox from 'expo-checkbox';
import fetchData from "../config/config"
import { useIsFocused } from '@react-navigation/native'
import Icon from 'react-native-vector-icons/FontAwesome'; // Puedes cambiar 'FontAwesome' por el conjunto de íconos que estés usando
const itemsPerPage = 5; // Número de elementos por página
import DateTimePicker from '@react-native-community/datetimepicker';

import moment from 'moment';

let ESTADO_FILTRO = [
    {
        key: 2,
        label: "TODO",
    }, {
        key: 0,
        label: "ENTREGADO TOTAL",
    },
    {
        key: 1,
        label: "ENTREGADO PARCIAL",
    }
]


export default function Mis_guias({ route, navigation }) {
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');
    const isFocused = useIsFocused()
    //****** DATE */
    const [fecha_inicio, setfecha_inicio] = useState(new Date());
    const [showDatePicker, setShowDatePicker] = useState(false);

    //**** DATOS TABLA *****/
    const [data_detalle, setdata_detalle] = useState([]);


    const [currentPage, setCurrentPage] = useState(0);
    const [pagina_actual, setpagina_actual] = useState(0);
    const [pagina_final, setpagina_final] = useState(0);
    const [estado_filtro, setestado_filtro] = useState(2);


    const datos_sesion = route.params;

    useEffect(() => {
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"])


        Consultar_guias(estado_filtro, pagina_actual)
    }, [isFocused]);

    const handleDateChange = (event, date) => {
        setShowDatePicker(false);
        if (event.type === 'set') {
            setfecha_inicio(date);
        }
    };


    function Consultar_guias(estado_filtro, pagina) {
        let USUARIO = usuarioid;
        let param = {
            USUARIO_ID: datos_sesion["Usuario_ID"],
            ESTADO: estado_filtro,
            ITEMS_POR_PAGINA: itemsPerPage,
            PAGINA_ACTUAL: pagina,
            FECHA_INICIO: moment(fecha_inicio).format("YYYY-MM-DD"),
            FECHA_FIN: moment().format("YYYY-MM-DD"),
        }

        let url = 'despacho/Guias_Usuario'
        fetchData(url, param, function (x) {
            console.log('x: ', x);

            let datos = x[0];
            if (datos == 0) {
                Alert.alert("Error de conexion intente en un momento", x[1].toString());
            } else {
                if (x.length == 0) {
                    Alert.alert("No hay datos que mostrar", "");
                    setdata_detalle(x);

                } else if (x.length > 0) {
                    let CANTDAD_REG = x[1];


                    datos.sort((a, b) => a.PEDIDO_INTERNO - b.PEDIDO_INTERNO);
                    // // setdata_detalle(x);
                    let cantidad_pag = Math.ceil(CANTDAD_REG / itemsPerPage);

                    setpagina_final(cantidad_pag);
                    // 


                    // const startIndex = (currentPage - 1) * itemsPerPage;
                    // 
                    // // 
                    // setendIndex(startIndex + itemsPerPage);
                    // 
                    // const itemsToDisplay = x.slice(startIndex, endIndex);
                    // 
                    setdata_detalle(datos);
                }
            }


        })

    }

    function Ver_Detalle(text, index, item) {

        let PEDIDO_INTERNO = item.PEDIDO_INTERNO;
        datos_sesion.PEDIDO_INTERNO = PEDIDO_INTERNO;

        navigation.navigate('Guias_detalle', datos_sesion);
    }

    function Completar_Parcial(text, index, item) {
        let PEDIDO_INTERNO = item.PEDIDO_INTERNO;
        datos_sesion.PEDIDO_INTERNO = PEDIDO_INTERNO;
        navigation.navigate('Guias_parcial', datos_sesion);
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
                            <Text style={{ fontWeight: 'bold' }}>Desde</Text>
                                <TouchableOpacity
                                    style={{
                                        flexDirection: 'row',
                                        alignItems: 'center',
                                        marginTop: 10
                                    }}
                                    onPress={() => {
                                        setShowDatePicker(true);
                                    }}
                                >
                                    <Icon name="calendar" size={20} color="#3498DB" style={{ marginRight: 5 }} />
                                    <Text>{moment(fecha_inicio).locale('es').format("LL")}</Text>
                                </TouchableOpacity>
                                {showDatePicker && (
                                    <DateTimePicker
                                        value={fecha_inicio}
                                        mode="date"
                                        display="default"
                                        onChange={handleDateChange}
                                    />
                                )}
                            </View>
                            <View style={styles.column}>
                                <TouchableOpacity
                                    style={[styles.cell, {
                                        width: 50,
                                        margin: 1,
                                        backgroundColor: "#3498DB",
                                        borderRadius: 10,
                                        justifyContent: 'center',
                                        alignItems: 'center',
                                        marginStart: 50
                                    }]}
                                    onPress={() => {
                                        Consultar_guias(estado_filtro, 0)
                                    }}>
                                    <Icon name="refresh" size={20} color="white" />
                                </TouchableOpacity>

                            </View>
                        </View>

                        <View style={[styles.clientSelector, { marginTop: 5 }]}>
                            <Text style={{ fontSize: 16, fontWeight: 'bold', marginBottom: 5 }}>Filtrar por estado</Text>
                            <ModalSelector
                                // keyExtractor={(item) => item.key}
                                data={ESTADO_FILTRO}
                                initValue="Seleccione estado"
                                onChange={(option) => {

                                    setestado_filtro(option.key);
                                    setTimeout(() => {
                                        Consultar_guias(option.key);
                                    }, 1);
                                    // handleOptionChange(option);
                                    // Handle the selected option here
                                }}
                            />
                        </View>
                      
                        <ScrollView horizontal={true} style={{ marginTop: 10 }}>
                            <View style={styles.container}>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>ESTADO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>PEDIDO #</Text>
                                    <Text style={[styles.columnHeader, { width: 80 }]}>DETALLE</Text>
                                    <Text style={[styles.columnHeader, { width: 70 }]}>COM</Text>
                                </View>

                                {data_detalle.map((item, index) => (
                                    <View style={styles.row} key={index}>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", backgroundColor: item.ESTADO_DESPACHO == 1 ? '#EC7063' : '#2ECC71', color: 'white' }]}>{item.ESTADO_DESPACHO_TEXTO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.PEDIDO_INTERNO}</Text>

                                        <TouchableOpacity
                                            style={[styles.cell, { width: 60, margin: 8, backgroundColor: '#F2F4F4', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }]}
                                            onPress={(text) => Ver_Detalle(text, index, item)}
                                        >
                                            <Text style={{ color: 'white', fontWeight: 'bold' }}>
                                                <Icon name="eye" size={30} color="#1C2833" />
                                            </Text>
                                        </TouchableOpacity>
                                        {item.ESTADO_DESPACHO == 1 ? (
                                            <TouchableOpacity
                                                style={[styles.cell, { width: 60, margin: 8, backgroundColor: '#A9DFBF', borderRadius: 10, justifyContent: 'center', alignItems: 'center' }]}
                                                onPress={(text) => Completar_Parcial(text, index, item)}
                                            >
                                                <Text style={{ color: 'white', fontWeight: 'bold' }}>
                                                    <Icon name="check" size={30} color="#1C2833" />

                                                </Text>
                                            </TouchableOpacity>
                                        ) : (
                                            <Text style={{ color: 'white', fontWeight: 'bold' }}>
                                            </Text>
                                        )

                                        }

                                    </View>
                                ))}
                                <View style={{ flexDirection: 'row', justifyContent: 'space-between' }}>
                                    <TouchableOpacity
                                        style={[styles.cell, {
                                            width: 60,
                                            backgroundColor: pagina_actual == 0 ? '#E5E8E8' : "#3498DB",
                                            margin: 8,
                                            borderRadius: 10,
                                            justifyContent: 'center',
                                            alignItems: 'center'
                                        }]}

                                        onPress={() => {

                                            setpagina_actual(pagina_actual - 1);
                                            Consultar_guias(estado_filtro, pagina_actual - 1);
                                        }}
                                        disabled={pagina_actual === 0}
                                    >
                                        <Text>
                                            <Icon name="arrow-left" size={35} color="#fff" />
                                        </Text>
                                    </TouchableOpacity>
                                    <TouchableOpacity
                                        style={[styles.cell, {
                                            width: 60, margin: 8,
                                            backgroundColor: pagina_actual == pagina_final ? '#E5E8E8' : "#3498DB",
                                            borderRadius: 10,
                                            justifyContent: 'center',
                                            alignItems: 'center'
                                        }]}

                                        onPress={() => {
                                            setpagina_actual(pagina_actual + 1);
                                            Consultar_guias(estado_filtro, pagina_actual + 1);

                                        }}
                                        disabled={pagina_actual == pagina_final}
                                    >
                                        <Text>
                                            <Icon name="arrow-right" size={35} color="#fff" />
                                        </Text>
                                    </TouchableOpacity>
                                </View>
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
