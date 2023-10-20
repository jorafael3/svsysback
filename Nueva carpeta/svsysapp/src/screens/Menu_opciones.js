
import React from 'react';
import { View, TouchableOpacity, StyleSheet, Text } from 'react-native';


export default function Menu_opciones({ route, navigation }) {
    // Funciones para manejar las acciones al presionar los botones
    const datos_sesion = route.params;

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
            <TouchableOpacity
                style={styles.button}
                onPress={handleButton1Press}
            >
                <Text style={styles.buttonText}>Scanear Guías</Text>
            </TouchableOpacity>
            <TouchableOpacity
                style={styles.button}
                onPress={handleButton2Press}
            >
                <Text style={styles.buttonText}>Guias Despachadas</Text>
            </TouchableOpacity>

            <TouchableOpacity
                style={styles.button}
                onPress={handleButton3Press}
            >
                <Text style={styles.buttonText}>Guías Asignadas</Text>
            </TouchableOpacity>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#F5FCFF',
    },
    button: {
        backgroundColor: '#007AFF',
        padding: 20,
        borderRadius: 10,
        marginTop: 10,
        width: 300,
    },
    buttonText: {
        color: 'white',
        textAlign: 'center',
        fontWeight: 'bold',
        fontSize: 20
    },
});
