import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ScrollView, Image, ActivityIndicator } from 'react-native';
import fetchData from "../config/config"

export default function LoginScreen({ navigation }) {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [valor1, setvalor1] = useState('');
    const [valor2, setvalor2] = useState('');
    const [valor3, setvalor3] = useState('');

    const [loading, setLoading] = useState(false);

    const handleLogin = () => {
        Validar_usuario()
    };

    function Validar_usuario() {
        let url = "usuarios/Validar_Usuario_movil"
        // let url = "prueba.php"

        // 
        // navigation.navigate('Menu_opciones', { Usuario: 'jorge', Usuario_ID: 1, Acceso: 1, PLACA: "GBO-7782" });

        // Alert.alert(
        //     'Dummy Title For Alert',
        //     'Dummy Message For Alert',
        //     [
        //         {
        //             text: 'Cancelar',
        //             onPress: () => ,
        //             style: 'cancel',
        //         },
        //         { text: 'OK', onPress: () =>  },
        //     ],
        //     { cancelable: false },
        // );

        if (username == "") {
            Alert.alert("Ingrese un nombre de usuario", "");

        } else if (password == "") {
            Alert.alert("Ingrese una contraseña", "");

        } else {
            const param = {
                USUARIO: username.toUpperCase(),
                PASS: password,
            };
            setLoading(true);
            fetchData(url, param, function (x) {
                console.log('x: ', x);
                setLoading(false);
                if (x == -1) {
                    Alert.alert("Error de conexion", "Asegurese que este conectado a internet");
                } else {
                    if (x[0] == true) {
                        let datos = x[1][0]
                        console.log('x: ', datos);
                        if (datos["ischofer"] == null) {
                            Alert.alert("El usuario no esta asignado como chofer", "o no tiene placa asignada");
                        } else if (datos["ESTADO_USUARIO"] == 0) {
                            Alert.alert("El usuario está inactivo", "comuniquese con administracion para ser activado");
                        } else if (datos["ESTADO_CHOFER"] == 0) {
                            Alert.alert("El usuario no esta activado como chofer", "comuniquese con administración para ser activado");
                        } else {
                            let datos_sesion = {
                                Usuario: datos["Usuario"],
                                Usuario_ID: datos["Usuario_ID"],
                                Acceso: 1,
                                PLACA: datos["PLACA"]
                            }
                            navigation.navigate('Menu_opciones', datos_sesion);
                        }
                    } else {
                        Alert.alert("Error de inicio de sesion", x[1].toString());
                    }
                }
            })
        }
    }

    return (

        <View style={styles.container}>

            <Image
                source={require('../../assets/logo.png')}
                style={styles.logo}
            />

            <Text style={styles.title}>Salvacero</Text>
            <TextInput
                style={styles.input}
                placeholder="Usuario"
                onChangeText={(text) => setUsername(text)}
            />
            <TextInput
                style={styles.input}
                placeholder="Contraseña"
                secureTextEntry
                onChangeText={(text) => setPassword(text)}
            />
            <TouchableOpacity style={styles.loginButton} onPress={handleLogin} disabled={loading}>
                <Text style={styles.loginButtonText}>Iniciar sesión</Text>
            </TouchableOpacity>
            {loading && <ActivityIndicator size="large" color="black" style={{ marginTop: 10 }} />}
            <Text style={styles.versionText}>Versión 1.0.0</Text>

        </View >
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
        fontWeight: 'bold'
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
        backgroundColor: 'black',
        width: '80%',
        height: 40,
        justifyContent: 'center',
        alignItems: 'center',
        borderRadius: 20,
        fontWeight: 'bold'
    },
    loginButtonText: {
        color: 'white',
        fontSize: 18,
        fontWeight: 'bold'
    },
    logo: {
        width: 100, // Ancho del logotipo
        height: 100, // Alto del logotipo
    },
    versionText: {
        fontSize: 12, // Tamaño del texto de la versión
        textAlign: 'center', // Alineación centrada
        color: 'gray', // Color del texto
        marginTop: 20
    },
});

