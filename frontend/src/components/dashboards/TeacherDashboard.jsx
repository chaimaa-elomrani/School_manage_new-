"use client"

import { useState, useEffect } from "react"
import { useAuth } from "../../contexts/AuthContext"
import {
  AcademicCapIcon,
  UserGroupIcon,
  CalendarIcon,
  ChartBarIcon,
  ClockIcon,
  BookOpenIcon,
} from "@heroicons/react/24/outline"

const TeacherDashboard = () => {
  const { user } = useAuth()
  const [dashboardData, setDashboardData] = useState({
    classes: [],
    students: [],
    assignments: [],
    schedule: [],
  })
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Simulate loading teacher data
    setTimeout(() => {
      setDashboardData({
        classes: [
          { id: 1, name: "Mathematics 101", students: 25, room: "A101" },
          { id: 2, name: "Advanced Calculus", students: 18, room: "A102" },
        ],
        students: [
          { id: 1, name: "John Doe", grade: "A", status: "active" },
          { id: 2, name: "Jane Smith", grade: "B+", status: "active" },
        ],
        assignments: [
          { id: 1, title: "Homework 1", dueDate: "2024-01-20", submitted: 20, total: 25 },
          { id: 2, title: "Quiz 2", dueDate: "2024-01-22", submitted: 15, total: 25 },
        ],
        schedule: [
          { id: 1, class: "Math 101", time: "09:00 AM", room: "A101" },
          { id: 2, class: "Calculus", time: "11:00 AM", room: "A102" },
        ],
      })
      setLoading(false)
    }, 1000)
  }, [])

  const StatCard = ({ title, value, icon: Icon, color, description }) => (
    <div className={`bg-white rounded-lg shadow p-6 border-l-4 ${color}`}>
      <div className="flex items-center justify-between">
        <div>
          <p className="text-gray-600 text-sm font-medium">{title}</p>
          <p className="text-3xl font-bold text-gray-900">{value}</p>
          {description && <p className="text-gray-500 text-sm mt-1">{description}</p>}
        </div>
        <Icon className="h-12 w-12 text-gray-400" />
      </div>
    </div>
  )

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        <span className="ml-3 text-gray-600">Loading teacher dashboard...</span>
      </div>
    )
  }

  return (
    <div className="space-y-6 p-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Teacher Dashboard</h1>
          <p className="text-gray-600">
            Welcome back, {user?.first_name} {user?.last_name}!
          </p>
        </div>
        <div className="text-sm text-gray-500">
          Role: <span className="font-medium capitalize">{user?.role}</span>
        </div>
      </div>

      {/* Statistics Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="My Classes"
          value={dashboardData.classes.length}
          icon={AcademicCapIcon}
          color="border-blue-500"
          description="Active courses"
        />
        <StatCard
          title="Total Students"
          value={dashboardData.classes.reduce((sum, cls) => sum + cls.students, 0)}
          icon={UserGroupIcon}
          color="border-green-500"
          description="Across all classes"
        />
        <StatCard
          title="Assignments"
          value={dashboardData.assignments.length}
          icon={BookOpenIcon}
          color="border-purple-500"
          description="Active assignments"
        />
        <StatCard
          title="Today's Classes"
          value={dashboardData.schedule.length}
          icon={CalendarIcon}
          color="border-orange-500"
          description="Scheduled for today"
        />
      </div>

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* My Classes */}
        <div className="bg-white rounded-lg shadow">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-900">My Classes</h3>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              {dashboardData.classes.map((cls) => (
                <div key={cls.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div className="flex items-center">
                    <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <AcademicCapIcon className="h-5 w-5 text-blue-600" />
                    </div>
                    <div className="ml-4">
                      <p className="text-sm font-medium text-gray-900">{cls.name}</p>
                      <p className="text-xs text-gray-500">Room {cls.room}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-medium text-gray-900">{cls.students} students</p>
                    <p className="text-xs text-gray-500">Enrolled</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Today's Schedule */}
        <div className="bg-white rounded-lg shadow">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-900">Today's Schedule</h3>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              {dashboardData.schedule.map((item) => (
                <div key={item.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div className="flex items-center">
                    <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                      <ClockIcon className="h-5 w-5 text-green-600" />
                    </div>
                    <div className="ml-4">
                      <p className="text-sm font-medium text-gray-900">{item.class}</p>
                      <p className="text-xs text-gray-500">Room {item.room}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-medium text-gray-900">{item.time}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Recent Assignments */}
        <div className="bg-white rounded-lg shadow">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-900">Recent Assignments</h3>
          </div>
          <div className="p-6">
            <div className="space-y-4">
              {dashboardData.assignments.map((assignment) => (
                <div key={assignment.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div className="flex items-center">
                    <div className="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                      <BookOpenIcon className="h-5 w-5 text-purple-600" />
                    </div>
                    <div className="ml-4">
                      <p className="text-sm font-medium text-gray-900">{assignment.title}</p>
                      <p className="text-xs text-gray-500">Due: {assignment.dueDate}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-medium text-gray-900">
                      {assignment.submitted}/{assignment.total}
                    </p>
                    <p className="text-xs text-gray-500">Submitted</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Quick Actions */}
        <div className="bg-white rounded-lg shadow">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-semibold text-gray-900">Quick Actions</h3>
          </div>
          <div className="p-6">
            <div className="grid grid-cols-2 gap-4">
              <button className="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg text-center transition-colors">
                <AcademicCapIcon className="h-8 w-8 text-blue-600 mx-auto mb-2" />
                <p className="text-sm font-medium text-blue-900">Grade Assignments</p>
              </button>
              <button className="p-4 bg-green-50 hover:bg-green-100 rounded-lg text-center transition-colors">
                <CalendarIcon className="h-8 w-8 text-green-600 mx-auto mb-2" />
                <p className="text-sm font-medium text-green-900">View Schedule</p>
              </button>
              <button className="p-4 bg-purple-50 hover:bg-purple-100 rounded-lg text-center transition-colors">
                <UserGroupIcon className="h-8 w-8 text-purple-600 mx-auto mb-2" />
                <p className="text-sm font-medium text-purple-900">Manage Students</p>
              </button>
              <button className="p-4 bg-orange-50 hover:bg-orange-100 rounded-lg text-center transition-colors">
                <ChartBarIcon className="h-8 w-8 text-orange-600 mx-auto mb-2" />
                <p className="text-sm font-medium text-orange-900">View Reports</p>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default TeacherDashboard
