import React, { useState, Component, useEffect } from 'react';
import Background from '../components/Background'
import Logo from '../components/Logo'
import Header from '../components/Header'
import Paragraph from '../components/Paragraph'
import Button from '../components/Button'
import { Alert } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage';

var SESION = [];

export default function Dashboard({ navigation }) {
  const [datos_usuario, setdatos_usuario] = useState([]);


  const getMultipleData = async () => {
    try {
      const savedData = await AsyncStorage.multiGet(["datos_usuario"]);
      setdatos_usuario(savedData[0])
      SESION = savedData[0][1]
      console.log(SESION["usuario"])

      // Alert.alert("sesion inciada", JSON.stringify(savedData));
    } catch (error) {
      console.log(error);
    }
  };
  useEffect(() => {
    getMultipleData();
    // console.log(SESION)

  }, []);

  return (
    <Background>
      <Logo />
      <Header>Let’s start</Header>
      <Paragraph>
        Your amazing app starts here. Open you favorite code editor and start
        editing this project.
      </Paragraph>
      <Button
        mode="outlined"
        onPress={() =>
          navigation.reset({
            index: 0,
            routes: [{ name: 'LoginScreen' }],
          })
        }
      >
        Logout
      </Button>
    </Background>
  )
}
