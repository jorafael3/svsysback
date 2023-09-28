import { View, Text, Modal, TouchableOpacity, StyleSheet } from 'react-native';
import React, { useState, Component, useEffect } from 'react';

export default function Menu({ navigation }) {
    const [isMenuVisible, setIsMenuVisible] = useState(false);

    function redirectToView1() {
        navigation.navigate('Guias', { Usuario: 'jorge', Usuario_ID: 1, Acceso: 1 });

    }

    function redirectToView2() {

    }

    return (
        <View>
            <TouchableOpacity onPress={() => setIsMenuVisible(true)}>
                <Text style={menuButtonStyle}>Menú</Text>
            </TouchableOpacity>
            <Modal
                transparent={true}
                animationType="slide"
                visible={isMenuVisible}
                onRequestClose={() => setIsMenuVisible(false)}
            >
                <View style={menuContainerStyle}>
                    <TouchableOpacity onPress={redirectToView1}>
                        <Text style={menuItemStyle}>Ir a Vista 1</Text>
                    </TouchableOpacity>
                    <TouchableOpacity onPress={redirectToView2}>
                        <Text style={menuItemStyle}>Ir a Vista 2</Text>
                    </TouchableOpacity>
                    {/* Agrega más elementos de menú según tus necesidades */}
                </View>
            </Modal>
        </View>

    );
};
const menuButtonStyle = {
    fontSize: 16,
    fontWeight: 'bold',
    marginHorizontal: 20,
    marginTop: 10,
  };
  
  const menuContainerStyle = {
    backgroundColor: 'white',
    padding: 20,
    marginTop: 30,
  };
  
  const menuItemStyle = {
    fontSize: 16,
    marginVertical: 10,
  };
