import React, { useState } from 'react'
import { TouchableOpacity, StyleSheet, View, Alert } from 'react-native'
import { Text } from 'react-native-paper'
import Background from '../components/Background'
import Logo from '../components/Logo'
import Header from '../components/Header'
import Button from '../components/Button'
import TextInput from '../components/TextInput'
import BackButton from '../components/BackButton'
import { theme } from '../core/theme'
import AsyncStorage from '@react-native-async-storage/async-storage';

import fetchData from "../config/config"

export default function LoginScreen({ navigation }) {
  const [email, setEmail] = useState({ value: '', error: '' })
  const [password, setPassword] = useState({ value: '', error: '' })

  const onLoginPressed = () => {
    // const emailError = emailValidator(email.value)
    // const passwordError = passwordValidator(password.value)
    // if (emailError || passwordError) {
    //   setEmail({ ...email, error: emailError })
    //   setPassword({ ...password, error: passwordError })
    //   return
    // }

    Validar_usuario();
  }

  function Validar_usuario() {
    // if (email.value == "jorge") {
    //   navigation.reset({
    //     index: 0,
    //     routes: [{ name: 'Dashboard' }],
    //   })
    // } else {
    //   Alert.alert("Error al iniciar sesion", "Usuario incorrecto")
    // }
    let url = "usuarios/Validar_Usuario_movil"
    const param = {
      USUARIO: (email.value).toUpperCase(),
      PASS: password.value,
    };
    navigation.reset({
      index: 0,
      routes: [{ name: 'Dashboard' }],
    })
    const sesion = {
      usuario: email.value,
      acceso:1,
      placa:"GHS-1233"
    };
    const datos_sesion = ["datos_usuario", JSON.stringify(sesion)];
    saveData(datos_sesion)
    // fetchData(url, param, function (x) {
    //   // Alert.alert("sesion inciada", JSON.stringify(x));
    //   if (x[0] == true) {
    //     let usu = x[1][0]["Usuario"]
    //     const sesion = {
    //       usuario: usu,
    //     };
    //     const datos_sesion = ["datos_usuario", JSON.stringify(sesion)];
    //     saveData(datos_sesion)
    //     navigation.reset({
    //       index: 0,
    //       routes: [{ name: 'Dashboard' }],
    //     })
    //   } else {
    //     Alert.alert("Error de inicio de secion", x[1]);
    //   }
    // })
  }


  // const getData = async (key) => {
  //   try {
  //     const value = JSON.parse(await AsyncStorage.getItem("usuario"))
  //     return value;
  //     // if (value !== null) {
  //     //   return JSON.parse(value);
  //     // }
  //   } catch (error) {
  //     console.error('Error retrieving data:', error);
  //   }
  // };
  const saveData = async (datos) => {
    try {
      // await AsyncStorage.setItem(key, JSON.stringify(value));
      await AsyncStorage.multiSet([datos]);

    } catch (error) {
      console.error('Error saving data:', error);
    }
  };

  
  return (
    <Background>
      {/* <BackButton goBack={navigation.goBack} /> */}
      <Logo />
      <Header>Bienvenido</Header>
      <TextInput
        label="Usuario"
        returnKeyType="next"
        value={email.value}
        onChangeText={(text) => setEmail({ value: text, error: '' })}
        // error={!!email.error}
        // errorText={email.error}
        autoCapitalize="none"
        autoCompleteType="email"
        textContentType="emailAddress"
        keyboardType="email-address"
      />
      <TextInput
        label="Contraseña"
        returnKeyType="done"
        value={password.value}
        onChangeText={(text) => setPassword({ value: text, error: '' })}
        error={!!password.error}
        errorText={password.error}
        secureTextEntry
      />
      <View style={styles.forgotPassword}>
        <TouchableOpacity
          onPress={() => navigation.navigate('ResetPasswordScreen')}
        >
          {/* <Text style={styles.forgot}>Forgot your password?</Text> */}
        </TouchableOpacity>
      </View>
      <Button mode="contained" onPress={onLoginPressed}>
        Iniciar sesion
      </Button>
      {/* <View style={styles.row}>
        <Text>Don’t have an account? </Text>
        <TouchableOpacity onPress={() => navigation.replace('RegisterScreen')}>
          <Text style={styles.link}>Sign up</Text>
        </TouchableOpacity>
      </View> */}
    </Background>
  )
}

const styles = StyleSheet.create({
  forgotPassword: {
    width: '100%',
    alignItems: 'flex-end',
    marginBottom: 24,
  },
  row: {
    flexDirection: 'row',
    marginTop: 4,
  },
  forgot: {
    fontSize: 13,
    color: theme.colors.secondary,
  },
  link: {
    fontWeight: 'bold',
    color: theme.colors.primary,
  },
})
