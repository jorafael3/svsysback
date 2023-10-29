
import React from 'react';
import { View, TouchableOpacity, StyleSheet, Text, Image } from 'react-native';
import Icon from 'react-native-vector-icons/FontAwesome'; // Importa el icono


export default function Menu_opciones({ route, navigation }) {
    // Funciones para manejar las acciones al presionar los botones
    const datos_sesion = route.params;
    console.log('datos_sesion: ', datos_sesion);

    const handleButton1Press = () => {
        // Lógica para el botón 1
        navigation.navigate('Guias', datos_sesion);

    };

    const handleButton2Press = () => {
        // Lógica para el botón 2
        navigation.navigate('Mis_Guias', datos_sesion);

    };

    const handleButton3Press = () => {
        // Lógica para el botón 2
        navigation.navigate('Guias_Asignadas', datos_sesion);

    };

    return (
        <View style={styles.container}>
            <Image
                source={require('../../assets/logo.png')}
                style={styles.logo}
            />
            <Text style={styles.welcomeText}>Bienvenido</Text>
            <Text style={styles.usernameText}>{datos_sesion.Usuario}</Text>
            <Text style={styles.usernameText}>{datos_sesion.PLACA}</Text>
            <View style={styles.buttonsContainer}>
                <TouchableOpacity
                    style={styles.button}
                    onPress={handleButton1Press}
                >
                    <Icon name="barcode" size={30} color="white" />
                    <Text style={styles.buttonText}>Scanear Guías</Text>
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.button}
                    onPress={handleButton2Press}
                >
                    <Icon name="truck" size={30} color="white" />
                    <Text style={styles.buttonText}>Guías Despachadas</Text>
                </TouchableOpacity>
                <TouchableOpacity
                    style={styles.button}
                    onPress={handleButton3Press}
                >
                    <Icon name="tasks" size={30} color="white" />
                    <Text style={styles.buttonText}>Guías Asignadas</Text>
                </TouchableOpacity>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor:"white"
    },
    welcomeText: {
        fontSize: 36,
        marginBottom: 10, // Espacio entre el mensaje de bienvenida y el nombre de usuario
        fontWeight: "bold"
    },
    usernameText: {
        fontSize: 18,
        marginBottom: 20, // Espacio entre el nombre de usuario y los botones
        fontWeight: "bold"

    },
    buttonsContainer: {
        alignItems: 'center',
    },
    button: {
        backgroundColor: '#000000',
        borderRadius: 30,
        padding: 20,
        width: 280,
        flexDirection: 'row',
        alignItems: 'center',
        marginBottom: 20,
        textAlign:'center'
        
    },
    buttonText: {
        color: 'white',
        fontSize: 20,
        marginLeft: 10,
        fontWeight: "bold"

    },
    logo: {
        width: 100, // Ancho del logotipo
        height: 100, // Alto del logotipo
    },
});
// const styles = StyleSheet.create({
//     container: {
//         flex: 1,
//         justifyContent: 'center',
//         alignItems: 'center',
//         backgroundColor: '#F5FCFF',
//     },
//     button: {
//         backgroundColor: '#007AFF',
//         padding: 20,
//         borderRadius: 10,
//         marginTop: 10,
//         width: 300,
//     },
//     buttonText: {
//         color: 'white',
//         textAlign: 'center',
//         fontWeight: 'bold',
//         fontSize: 20
//     },
// });
