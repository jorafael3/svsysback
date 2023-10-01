import React from 'react'
import { Alert } from 'react-native';
import axios from 'axios';

// const host = "10.5.0.238";
const host = "cartimex.com";
const protocol = "https:";
const port = "";
const URL = protocol + "//" + host + port + "/svsysback/"

// async function fetchData(url, param, callback) {

//     try {
//         const response = await fetch(URL + url, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//             },
//             body: JSON.stringify(param),
//         });
//         const data = await response.json();
//         callback(data)
//         // console.log(data);
//     } catch (error) {
//         // console.log('error: ', error);
//         // Alert.alert("",error)
//         callback(error.message);
//     }
// }

function fetchData(url, param, callback) {
    axios.post(URL + url, param)
        .then(function (response) {
            // Manejar la respuesta exitosa aquí
            callback(response.data)
        })
        .catch(function (error) {
            callback(error)
            // Manejar el error aquí
            // console.error('Error en la solicitud:', error);
        });
}

export default fetchData