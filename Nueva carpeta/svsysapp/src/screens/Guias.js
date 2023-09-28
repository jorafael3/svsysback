import { View, Text, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import React, { useState, Component, useEffect } from 'react';
import fetchData from "../config/config"


export default function Guias({ route, navigation }) {
    const [usuario, setusuario] = useState('');
    const [usuarioid, setusuarioid] = useState('');

    const handleLogout = () => {

        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
    };

    useEffect(() => {
        const datos_sesion = route.params;
        setusuario(datos_sesion["Usuario"]);
        setusuarioid(datos_sesion["Usuario_ID"]);
    }, []);

    function scanner() {
        // setIsScannerVisible(true);
        // setScanned(false);
        // setIsManualInputVisible(false);
        // setisFormVisible(false);
        // (async () => {
        //     const { status } = await BarCodeScanner.requestPermissionsAsync();
        //     setHasPermission(status === 'granted');
        // })();
    }

    function manual() {
        // setIsManualInputVisible(true);
        // setIsScannerVisible(false);
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
