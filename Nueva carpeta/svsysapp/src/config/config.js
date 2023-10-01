import React from 'react'
import { Alert } from 'react-native';
import axios from 'axios';

// const host = "10.5.0.238";
const host = "192.168.0.104";
// const host = "186.3.23.4";
const protocol = "http:";
const port = ":8080";


const URL = protocol + "//" + host + port + "/svsysback/"

async function fetchData2(url, param, callback) {

    try {
        const response = await fetch(URL + url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(param),
        });
        const data = await response.json();
        callback(data)
        // console.log(data);
    } catch (error) {
        // console.log('error: ', error);
        // Alert.alert("",error)
        callback(error.message);
    }
}

function fetchData(url, param, callback) {
    axios.post(URL + url, param, {
        headers: {
            'Content-Type': 'application/json',
        }
    })
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

function fetchData3(url, callback) {
    axios.get(URL + url, {
        headers: {
            'Content-Type': 'application/json',
        }
    })
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

export { fetchData, fetchData2, fetchData3 } 