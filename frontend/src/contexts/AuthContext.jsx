"use client"

import { createContext, useContext, useState, useEffect } from "react"
import { authService } from "../services/authService"

const AuthContext = createContext()

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error("useAuth must be used within an AuthProvider")
  }
  return context
}

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)
  const [isAuthenticated, setIsAuthenticated] = useState(false)

  useEffect(() => {
    const checkAuth = async () => {
      try {
        if (authService.isAuthenticated()) {
          const userData = authService.getUser()
          console.log("Retrieved user data from localStorage:", userData) // Debug log

          if (userData) {
            setUser(userData)
            setIsAuthenticated(true)
          } else {
            // Clear invalid data
            localStorage.removeItem("authToken")
            localStorage.removeItem("user")
          }
        }
      } catch (error) {
        console.error("Auth check failed:", error)
        localStorage.removeItem("authToken")
        localStorage.removeItem("user")
        setUser(null)
        setIsAuthenticated(false)
      } finally {
        setLoading(false)
      }
    }

    checkAuth()
  }, [])

  const login = async (email, password) => {
    try {
      const response = await authService.login({ email, password })
      console.log("AuthContext login response:", response) // Debug log

      // Handle the nested response structure
      const responseData = response.data || response
      const userData = responseData.user

      console.log("Extracted user data:", userData) // Debug log

      if (userData) {
        setUser(userData)
        setIsAuthenticated(true)
        console.log("User role set to:", userData.role) // Debug log
      } else {
        throw new Error("No user data received")
      }

      return response
    } catch (error) {
      console.error("AuthContext login error:", error) // Debug log
      setUser(null)
      setIsAuthenticated(false)
      throw error
    }
  }

  const register = async (userData) => {
    try {
      const response = await authService.register(userData)
      const responseData = response.data || response
      const user = responseData.user

      if (user) {
        setUser(user)
        setIsAuthenticated(true)
      }

      return response
    } catch (error) {
      setUser(null)
      setIsAuthenticated(false)
      throw error
    }
  }

  const logout = async () => {
    try {
      await authService.logout()
    } catch (error) {
      console.error("Logout error:", error)
    } finally {
      setUser(null)
      setIsAuthenticated(false)
    }
  }

  const value = {
    user,
    isAuthenticated,
    loading,
    login,
    register,
    logout,
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}
  