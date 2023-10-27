import React from 'react'
import * as fun from "./config"
import axios from 'axios';

// const host = "192.168.0.122";
// const host = "10.5.0.238";
const host = "192.168.0.122";
// const host = "186.3.23.4";
// const host = "10.5.2.62";
// const host = "www.cartimex.com";
// const host = "www.lcaimport.com";
const protocol = "http" + ":";
const port = ":80";
const token = "NLJwd=twVjJZ5!caOx!Cuh2XfjbLmcKXBr3R0F07DF8U?bDN1/i9omfIALwsVTZSGR0EhiOeNipl5pk5=s1rxL8RvF6pDxxVlTBmzOL2QCp0qGlPbSv=gs8tKGREhxGds29RXwbAU56nx5K6rotNeCXigeTNUFR5E-Bq!0T?LqoIyqvHkg6S13kv-fxm3e=piDz3k2jhrOuHFOVx-DzwC8I/?F3lPSRuvj0V/!oO2YAgqHGA3p-Kt3YQnpOWM7!6";

const URL = protocol + "//" + host + port + "/svsysback/"

async function fetchimagenes(url, formData, callback) {

    const instance = axios.create({
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });

    axios.post(URL + url, formData, {
        headers: {
            'Content-Type': 'application/json'
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


export default fetchimagenes