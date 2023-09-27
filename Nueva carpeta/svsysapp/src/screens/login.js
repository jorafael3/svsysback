import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import fetchData from "../config/config"

export default function LoginScreen({ navigation }) {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');

    const handleLogin = () => {
        Validar_usuario()
    };

    function Validar_usuario() {
        let url = "usuarios/Validar_Usuario_movil"
        const param = {
            USUARIO: username.toUpperCase(),
            PASS: password,
        };
        console.log('param: ', param);
        // navigation.navigate('Guias', { username: 'jorge.' });

        fetchData(url, param, function (x) {
            console.log('x: ', x);
            // Alert.alert("sesion inciada", JSON.stringify(x));
            if (x[0] == true) {
                let datos = x[1][0]
                let datos_sesion = {
                    Usuario: datos["Usuario"],
                    Usuario_ID: datos["Usuario_ID"],
                    Acceso: 1
                }
                console.log('datos_sesion: ', datos_sesion);

                // navigation.navigate('Guias', datos_sesion);
            } else {
                Alert.alert("Error de inicio de secion", x[1]);
            }
        })
    }

    return (
        <View style={styles.container}>
            <Text style={styles.title}>Iniciar Sesión</Text>
            <TextInput
                style={styles.input}
                placeholder="Nombre de usuario"
                onChangeText={(text) => setUsername(text)}
            />
            <TextInput
                style={styles.input}
                placeholder="Contraseña"
                secureTextEntry
                onChangeText={(text) => setPassword(text)}
            />
            <TouchableOpacity style={styles.loginButton} onPress={handleLogin}>
                <Text style={styles.loginButtonText}>Iniciar Sesión</Text>
            </TouchableOpacity>
        </View>
    );
}


const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#fff',
    },
    title: {
        fontSize: 24,
        marginBottom: 20,
    },
    input: {
        width: '80%',
        height: 40,
        borderColor: 'gray',
        borderWidth: 1,
        borderRadius: 5,
        marginBottom: 10,
        paddingHorizontal: 10,
    },
    loginButton: {
        backgroundColor: 'blue',
        width: '80%',
        height: 40,
        justifyContent: 'center',
        alignItems: 'center',
        borderRadius: 5,
    },
    loginButtonText: {
        color: 'white',
        fontSize: 18,
    },
});

