import React from 'react'
import { Alert } from 'react-native';
import axios from 'axios';


const host = "192.168.0.122";
// const host = "10.5.0.238";
// const host = "192.168.0.104";
// const host = "186.3.23.4";
// const host = "www.cartimex.com";
const protocol = "http:";
const port = ":80";
const token = "NLJwd=twVjJZ5!caOx!Cuh2XfjbLmcKXBr3R0F07DF8U?bDN1/i9omfIALwsVTZSGR0EhiOeNipl5pk5=s1rxL8RvF6pDxxVlTBmzOL2QCp0qGlPbSv=gs8tKGREhxGds29RXwbAU56nx5K6rotNeCXigeTNUFR5E-Bq!0T?LqoIyqvHkg6S13kv-fxm3e=piDz3k2jhrOuHFOVx-DzwC8I/?F3lPSRuvj0V/!oO2YAgqHGA3p-Kt3YQnpOWM7!6";

const URL = protocol + "//" + host + port + "/svsysback/"

async function fetchData2(url, param, callback) {
    param.TOKEN = token
    // console.log('param: ', param);
    try {
        const response = await fetch(URL + url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(param),
        });
        const data = await response.json();
        callback(JSON.parse(data))
        // console.log(data);
    } catch (error) {
        // console.log('error: ', error);
        // Alert.alert("",error)
        callback(error.message);
    }
}

function fetchData(url, param, callback) {
    if (param.length == 0) {
        param = {
            TOKEN: token
        }
    } else {
        param.TOKEN = token
    }
    // console.log('param: ', param);

    axios.post(URL + url, param, {
        headers: {
            'Content-Type': 'application/json',
            // referrerPolicy: "unsafe-url",
            // Accept: "application/json"

        },
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
            referrerPolicy: "unsafe-url",
            Accept: "application/json"
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

export default fetchData