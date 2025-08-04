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

// Update the response interceptor
api.interceptors.response.use(
    (response) => {
        if (response.config.url?.includes('/communication/email')) {
            // Check if the response indicates success
            if (response.data?.success) {
                console.log('Email sent successfully:', response.data);
                return response;
            } else {
                // If the backend indicates failure despite 200 status
                throw new Error(response.data?.error || 'Failed to send email');
            }
        }
        return response;
    },
    (error) => {
        if (error.config?.url?.includes('/communication/email')) {
            console.error('Email Send Error:', {
                status: error.response?.status,
                message: error.response?.data?.error || error.message,
                details: error.response?.data
            });
        }
        return Promise.reject(error);
    }
);

export default api;

