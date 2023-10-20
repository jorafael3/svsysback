import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ScrollView } from 'react-native';
import fetchData from "../config/config"

export default function LoginScreen({ navigation }) {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [valor1, setvalor1] = useState('');
    const [valor2, setvalor2] = useState('');
    const [valor3, setvalor3] = useState('');

    const handleLogin = () => {
        Validar_usuario()
    };



    function Validar_usuario() {
        let url = "usuarios/Validar_Usuario_movil"
        // let url = "prueba.php"
        const param = {
            USUARIO: username.toUpperCase(),
            PASS: password,
        };
        // 
        // navigation.navigate('Menu_opciones', { Usuario: 'jorge', Usuario_ID: 1, Acceso: 1, PLACA: "GMC-7889" });
        // navigation.navigate('Guias', { Usuario: 'jorge', Usuario_ID: 1, Acceso: 1, PLACA: "GSA-115" });

        fetchData(url, param, function (x) {
            console.log('x: ', x[1]);

            // Alert.alert("sesion inciadaa", JSON.stringify(x));
            // console.log('JSON.stringify(x): ', JSON.stringify(x));
            // setvalor1(JSON.stringify(x))
            if (x[0] == true) {
                let datos = x[1][0]

                if (datos["ischofer"] == null) {
                    Alert.alert("El usuario no esta asignado como chofer", "o no tiene placa asignada");
                } else {
                    let datos_sesion = {
                        Usuario: datos["Usuario"],
                        Usuario_ID: datos["Usuario_ID"],
                        Acceso: 1,
                        PLACA: datos["PLACA"]
                    }
                    navigation.navigate('Menu_opciones', datos_sesion);

                }


                // 
            } else {
                Alert.alert("Error de inicio de sesion", x[1]);
            }
        })

        // fetchData.fetchData2(url, param, function (x) {

        //     // Alert.alert("sesion inciadaa", JSON.stringify(x));
        //     console.log('JSON.stringify(x): ', JSON.stringify(x));
        //     setvalor2(JSON.stringify(x))
        // })
        // let url2 = 'prueba.php?param1=jorgealav'

        // fetchData.fetchData3(url2, function (x) {

        //     // Alert.alert("sesion inciadaa", JSON.stringify(x));
        //     console.log('JSON.stringify(x): ', JSON.stringify(x));
        //     setvalor3(JSON.stringify(x))
        // })
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

