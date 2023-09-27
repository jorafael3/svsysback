import React from 'react'


// const host = "192.168.0.122";
const host = "186.3.23.4";
const protocol = "http:";
const port = ":82";
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