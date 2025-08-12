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
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem("authToken")
      localStorage.removeItem("user")
      window.location.href = "/login"
    }
    return Promise.reject(error)
  }
)

// Function to fetch all student data in parallel
export async function fetchStudentData(studentId) {
  try {
    // For demo purposes, we'll return mock data
    // In production, uncomment the API calls below
    
  const [
      profileRes,
      scheduleRes,
      teachersRes,
      gradesRes,
      paymentsRes,
      assignmentsRes,
      announcementsRes
    ] = await Promise.all([
      api.get(`/showStudent/${studentId}`),
      api.get(`//showStudent/{id}/${studentId}`),
      api.get(`/deleteStudent/${studentId}`),
      api.get(`/showStudent/${studentId}`),
      api.get(`//showStudent/{id}/${studentId}`),
      api.get(`/deleteStudent/${studentId}`),
    ])
    
    return {
      profile: profileRes.data?.data || {},
      schedule: scheduleRes.data?.data || [],
      teachers: teachersRes.data?.data || [],
      grades: gradesRes.data?.data || [],
      payments: paymentsRes.data?.data || [],
      assignments: assignmentsRes.data?.data || [],
      announcements: announcementsRes.data?.data || []
    }
    
    
    
    
  } catch (error) {
    console.error("Error fetching student data:", error)
    throw new Error("Failed to fetch student data")
  }
};

export default api;

