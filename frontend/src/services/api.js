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
    const token = localStorage.getItem("authToken")
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    // Add special handling for email requests (existing logic)
    if (config.url?.includes("/sendEmail")) {
      console.log("Preparing email request:", {
        url: config.url,
        data: config.data,
      })
    }
    return config
  },
  (error) => {
    console.error("API Request Error:", error)
    return Promise.reject(error)
  },
);
// Update the response interceptor
api.interceptors.response.use(
  (response) => {
    if (response.config.url?.includes("/communication/email")) {
      if (response.data?.success) {
        console.log("Email sent successfully:", response.data)
        return response
      } else {
        throw new Error(response.data?.error || "Failed to send email")
      }
    }
    return response
    },
    (error) => {
        if (error.config?.url?.includes('/communication/email')) {
            console.error('Email Send Error:', {
                status: error.response?.status,
                message: error.response?.data?.error || error.message,
                details: error.response?.data
            });
            if (error.response && error.response.status === 401) {
             console.warn("Unauthorized request. Redirecting to login.")
             localStorage.removeItem("authToken")
             localStorage.removeItem("user")
             // You might want to use window.location.href or a router history push here
             // For React Router v6, you'd typically handle this in AuthContext or a higher-order component
             // For now, a simple redirect:
             window.location.href = "/login"
           }
        }
        return Promise.reject(error);
    }
);

export default api;

