import React from 'react'


const host = "192.168.0.122";
const protocol = "http:";
const port = ":80";
const URL = protocol + "//" + host + port + "/svsysback/"

async function fetchData(url, param,callback) {
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
        console.error(error);
    }
}

export default fetchData