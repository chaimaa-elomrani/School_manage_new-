// this file is made for the connection with the backend
import axios from 'axios';     

// creating axious instance with base configuration 
// axious is a library that allows us to make http requests
// we are creating an instance of axious with a base url and a timeout
// the base url is the url of our backend
// the timeout is the time after which the request will be cancelled
// we are also adding a header to our request
// the header is the authorization header
// the authorization header is used to send the token to the backend
// the token is used to authenticate the user


const api = axios.create({
    baseURL: 'http://localhost:8000',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Add request interceptor
api.interceptors.request.use(
    (config) => {
        console.log('API Request:', config);
        return config;
    },
    (error) => {
        console.error('API Request Error:', error);
        return Promise.reject(error);
    }
);

// Add response interceptor
api.interceptors.response.use(
    (response) => {
        console.log('API Response:', response);
        return response;
    },
    (error) => {
        console.error('API Response Error:', error);
        return Promise.reject(error);
    }
);

export default api;

