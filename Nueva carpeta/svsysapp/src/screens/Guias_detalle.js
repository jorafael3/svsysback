import React, { useState, Component, useEffect } from 'react';
import { View, TouchableOpacity, StyleSheet, Text, ScrollView } from 'react-native';
import ModalSelector from 'react-native-modal-selector';
import fetchData from "../config/config"

export default function Guias_detalle({ route, navigation }) {
    const datos_sesion = route.params;
    console.log('datos_sesion: ', datos_sesion);

    return (
        <Text >Filtrar por estado</Text>
    )
}
