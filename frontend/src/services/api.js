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


// Add timeout and better error handling
const api = axios.create({
    baseURL: 'http://localhost:8000',
    timeout: 30000,  // Increased timeout for email sending
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Add request interceptor
api.interceptors.request.use(
    (config) => {
        // Add special handling for email requests
        if (config.url?.includes('/sendEmail')) {
            console.log('Preparing email request:', {
                url: config.url,
                data: config.data
            });
        }
        return config;
    },
    (error) => {
        console.error('API Request Error:', error);
        return Promise.reject(error);
    }
);

// Improve error interceptor
api.interceptors.response.use(
    (response) => {
        if (response.config.url?.includes('/sendEmail')) {
            console.log('Email sent successfully:', response.data);
        }
        return response;
    },
    (error) => {
        // Detailed error logging for email failures
        if (error.config?.url?.includes('/sendEmail')) {
            console.error('Email Send Error:', {
                status: error.response?.status,
                message: error.response?.data?.error || error.message,
                details: error.response?.data?.details,
                rawError: error.response?.data
            });
            throw new Error(`Email sending failed: ${error.response?.data?.error || error.message}`);
        }
        
        throw error;
    }
);

export default api;

