import { View, Text, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import React, { useState, Component, useEffect } from 'react';
import fetchData from "../config/config"


export default function Guias({ route, navigation }) {

    const handleLogout = () => {

        // Agrega la lógica para cerrar sesión aquí y navegar de regreso a la pantalla de inicio de sesión.
    };

    useEffect(() => {

        const username = route.params;
        console.log('username: ', username["username"]);



    }, []);
    return (
        <View style={styles.container}>
            {/* Barra superior con nombre de usuario y botón de salida */}
            <View style={styles.header}>
                <Text style={styles.username}>Usuario:</Text>
                <TouchableOpacity onPress={handleLogout}>
                    <Text style={styles.logoutButton}>Salir</Text>
                </TouchableOpacity>
            </View>

            {/* Tarjeta para mostrar contenido adicional */}
            <View style={styles.card}>
                {/* Agrega aquí el contenido que deseas mostrar en la tarjeta */}
                <Text>Contenido de la tarjeta</Text>
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
        paddingTop: 10,
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
});
