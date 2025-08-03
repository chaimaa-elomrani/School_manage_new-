import api from './api';

export const authService = {
   // register new user 
register: async(userData) => {
    try{
        const response = await api.post('/auth/register', userData);
        
        if(response.data.data && response.data.data.token){
            localStorage.setItem('authToken', response.data.data.token);
            localStorage.setItem('user', JSON.stringify(response.data.data.user));
        }
        return response.data;

    }catch(error){
        throw error.response?.data || {error: 'Registration failed'};
    }
},
    // login user
    login: async(userData) => {
        try{
            const response = await api.post('/auth/login', userData);
            console.log('Login response:', response.data); // Debug log
            
            if(response.data.data && response.data.data.token){
                localStorage.setItem('authToken', response.data.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.data.user));
            }
            return response.data;
        }catch(error){
            console.error('Login error:', error); // Debug log
            localStorage.removeItem('authToken');
            localStorage.removeItem('user');
            throw error.response?.data || {error: 'Login failed'};
        }
    },
    // logout user
    logout: async() => {
        try{
            await api.post('/auth/logout');
            localStorage.removeItem('authToken');
            localStorage.removeItem('user');
        }catch(error){
            throw error.response.data || {error: 'Logout failed'};
        }
    }, 

    // get current user info 
    getCurrentUser: async() => {
        try{
            const response = await api.get('/auth/me');
            return response.data;
        }catch(error){
            throw error.response.data || {error: 'Get current user failed'};
        }
    },

    // check if user is authenticated
    isAuthenticated: () => {
        return !!localStorage.getItem('authToken');
    }, 

    // get  stored user data 
    getUser: () =>{
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    }
}
