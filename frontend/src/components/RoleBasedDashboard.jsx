"use client"

import { useAuth } from "../contexts/AuthContext"
import EnhancedStudentDashboard from "./dashboards/EnhancedStudentDashboard"
import EnhancedTeacherDashboard from "./dashboards/EnhancedTeacherDashboard"
import ParentDashboard from "./dashboards/ParentDashboard"
import AdminDashboard from "./dashboards/AdminDashboard" // Your existing admin dashboard

const RoleBasedDashboard = () => {
  const { user } = useAuth()

  // Show loading state while user data is being fetched
  if (!user) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    )
  }

  switch (user?.role) {
    case "student":
      return <EnhancedStudentDashboard />
    case "teacher":
      return <EnhancedTeacherDashboard />
    case "parent":
      return <ParentDashboard />
    case "admin":
      return <AdminDashboard />
    default:
      return (
        <div className="flex items-center justify-center h-64">
          <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
            <h2 className="text-lg font-semibold text-red-800 mb-2">Access Denied</h2>
            <p className="text-red-600">You don't have permission to access this dashboard.</p>
            <p className="text-sm text-red-500 mt-2">Role: {user?.role || "Unknown"}</p>
          </div>
        </div>
      )
  }
}

export default RoleBasedDashboard

