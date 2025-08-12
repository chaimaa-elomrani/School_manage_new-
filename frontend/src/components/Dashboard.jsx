"use client"

import { useAuth } from "../contexts/AuthContext"
import AdminDashboard from "./dashboards/AdminDashboard"
import TeacherDashboard from "./dashboards/TeacherDashboard"
import StudentDashboard from "./dashboards/StudentDashboard"

const Dashboard = () => {
  const { user, loading, isAuthenticated } = useAuth()

  console.log("Dashboard - User:", user) // Debug log
  console.log("Dashboard - User role:", user?.role) // Debug log
  console.log("Dashboard - Is authenticated:", isAuthenticated) // Debug log

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        <span className="ml-3 text-gray-600">Loading dashboard...</span>
      </div>
    )
  }

  if (!isAuthenticated || !user) {
    return (
      <div className="text-center py-12">
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Access Denied</h2>
        <p className="text-gray-600">Please log in to access the dashboard.</p>
      </div>
    )
  }

  const renderDashboard = () => {
    const userRole = user?.role?.toLowerCase()
    console.log("Rendering dashboard for role:", userRole) // Debug log

    switch (userRole) {
      case "admin":
        return <AdminDashboard />
      case "teacher":
        return <TeacherDashboard />
      case "student":
        return <StudentDashboard />
      case "parent":
        return (
          <div className="text-center py-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Parent Dashboard</h2>
            <p className="text-gray-600">Coming Soon...</p>
          </div>
        )
      default:
        return (
          <div className="text-center py-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Welcome, {user.first_name || user.email}</h2>
            <p className="text-gray-600">
              No specific dashboard available for role: <strong>{user.role || "unknown"}</strong>
            </p>
            <p className="text-gray-500 mt-2">Please contact your administrator.</p>
          </div>
        )
    }
  }

  return <div className="min-h-screen bg-gray-50">{renderDashboard()}</div>
}

export default Dashboard
