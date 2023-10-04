// import { StatusBar } from 'expo-status-bar';
import { StyleSheet, Text, View } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import { Provider } from 'react-native-paper'

import {
  LoginScreen,
  Guias,
  Menu_opciones,
  Mis_Guias,
  Guias_detalle
} from './src/screens'
// import Mis_guias from './src/screens/Mis_Guias';


const Stack = createStackNavigator()


export default function App() {
  return (
    <Provider>
      <NavigationContainer>
        <Stack.Navigator
          initialRouteName="LoginScreen"
          screenOptions={{
            headerShown: false,
          }}
        >
          <Stack.Screen name="LoginScreen" component={LoginScreen} />
          <Stack.Screen name="Menu_opciones" component={Menu_opciones} />
          <Stack.Screen name="Guias" component={Guias} />
          <Stack.Screen name="Mis_Guias" component={Mis_Guias} />
          <Stack.Screen name="Guias_detalle" component={Guias_detalle} />
        </Stack.Navigator>
      </NavigationContainer>
    </Provider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
});
