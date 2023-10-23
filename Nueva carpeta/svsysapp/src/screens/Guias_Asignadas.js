import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView, Alert } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"
import Icon from 'react-native-vector-icons/FontAwesome'; // Puedes cambiar 'FontAwesome' por el conjunto de íconos que estés usando
import { useIsFocused } from '@react-navigation/native'


const itemsPerPage = 10;

let ESTADO_FILTRO = [
    {
        key: "",
        label: "TODO",
    }, {
        key: "V",
        label: "VIGENTES",
    },
    {
        key: "P",
        label: "PARCIAL",
    },
    {
        key: "C",
        label: "COMPLETO",
    }
]

export default function Guias_Asignadas({ route, navigation }) {
    const isFocused = useIsFocused();
    const datos_sesion = route.params;
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');
    const [placa, setplaca] = useState('');

    const [pagina_actual, setpagina_actual] = useState(0);
    const [pagina_final, setpagina_final] = useState(0);

    const [data_detalle, setdata_detalle] = useState([]);
    const [estado_filtro, setestado_filtro] = useState("");

    useEffect(() => {

        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
        setplaca(datos_sesion["PLACA"]);
        Cargar_guias_asignadas(estado_filtro, pagina_actual)

    }, [isFocused]);

    const handleLogout = () => {
        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
    };



    function Cargar_guias_asignadas(estado_filtro, pagina) {
        let param = {
            USUARIO_ID: datos_sesion["Usuario_ID"],
            ESTADO: estado_filtro,
            ITEMS_POR_PAGINA: itemsPerPage,
            PAGINA_ACTUAL: pagina,
            PLACA: datos_sesion["PLACA"]
            // FECHA_INICIO: inicio,
            // FECHA_FIN: fin,
        }
        console.log('param: ', param);

        let url = 'despacho/Cargar_guias_asignadas'
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
                    let cantidad_pag = Math.ceil(CANTDAD_REG / itemsPerPage);
                    setpagina_final(cantidad_pag);

                    let datafiltro;
                    if (estado_filtro == "") {
                        datafiltro = datos;
                    } else if (estado_filtro == "C") {
                        datafiltro = datos.filter(item => item.ESTADO_DESPACHO == 0);
                    } else if (estado_filtro == "P") {
                        datafiltro = datos.filter(item => item.ESTADO_DESPACHO == 1);
                    } else if (estado_filtro == "V") {
                        datafiltro = datos.filter(item => item.ESTADO_DESPACHO == null);
                    }

                    setdata_detalle(datafiltro);
                }
            }


        })

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
                    <TouchableOpacity onPress={handleLogout}>
                        <Text style={styles.logoutButton}>Salir</Text>
                    </TouchableOpacity>
                </View>


                <View style={styles.card}>

                    <View style={styles.formContainer}>
                        <View style={styles.rowContainer}>
                            <View style={styles.column}>

                            </View>
                            <View style={styles.column}>

                            </View>
                        </View>

                        <View style={[styles.clientSelector, { marginTop: 5 }]}>
                            <Text style={{ fontSize: 16, fontWeight: 'bold', marginBottom: 5 }}>Filtrar por estado</Text>
                            <ModalSelector
                                // keyExtractor={(item) => item.key}
                                data={ESTADO_FILTRO}
                                initValue="Seleccione estado"
                                onChange={(option) => {
                                    console.log('option: ', option);

                                    setestado_filtro(option.key);
                                    setTimeout(() => {
                                        Cargar_guias_asignadas(option.key, pagina_actual);
                                    }, 1);
                                    // handleOptionChange(option);
                                    // Handle the selected option here
                                }}
                            />
                        </View>
                        <View style={[styles.clientSelector, { marginTop: 5 }]}>
                            {/* <TouchableOpacity
                                style={[styles.cell, {
                                    width: 50, margin: 1,
                                    backgroundColor: "#3498DB",
                                    borderRadius: 10,
                                    justifyContent: 'center',
                                    alignItems: 'center'
                                }]}

                                onPress={() => {

                                }}
                            >
                                <Text>
                                    <Icon name="refresh" size={30} color="#fff" />
                                </Text>
                            </TouchableOpacity> */}
                        </View>

                        <ScrollView horizontal={true} style={{ marginTop: 5 }}>
                            <View style={styles.container}>
                                {/* Encabezados de la tabla */}
                                <View style={styles.row}>
                                    <Text style={[styles.columnHeader, { width: 80 }]}>ESTADO</Text>
                                    <Text style={[styles.columnHeader, { width: 100 }]}>PEDIDO #</Text>
                                    <Text style={[styles.columnHeader, { width: 80, margin: 2 }]}>DETALLE</Text>
                                    <Text style={[styles.columnHeader, { width: 70 }]}>COM</Text>
                                </View>

                                {data_detalle.map((item, index) => (
                                    <View style={styles.row} key={index}>
                                        <Text style={[styles.cell, { width: 80, fontWeight: "bold", backgroundColor: item.ESTADO_DESPACHO == 1 ? '#EC7063' : item.ESTADO_DESPACHO == 0 ? '#2ECC71' : 'blue', color: 'white' }]}>{item.ESTADO_DESPACHO_TEXTO == null ? "VIGENTE" : item.ESTADO_DESPACHO_TEXTO}</Text>
                                        <Text style={[styles.cell, { width: 100, fontWeight: "bold", fontSize: 15 }]}>{item.PEDIDO_INTERNO}</Text>
                                        <TouchableOpacity
                                            style={[styles.cell, { width: 60, margin: 8, backgroundColor: '#F2F4F4', borderRadius: 5, justifyContent: 'center', alignItems: 'center' }]}
                                            // onPress={(text) => Ver_Detalle(text, index, item)}
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
                                            Cargar_guias_asignadas(estado_filtro, pagina_actual - 1);
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
                                            Cargar_guias_asignadas(estado_filtro, pagina_actual + 1);

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