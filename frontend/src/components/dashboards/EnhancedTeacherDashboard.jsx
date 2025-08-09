"use client"

import { useState, useEffect } from "react"
import { useAuth } from "../../contexts/AuthContext"
import api from "../../services/api"
import {
  UsersIcon,
  BookOpenIcon,
  ClockIcon,
  CheckCircleIcon,
  ChartBarIcon,
  CalendarIcon,
} from "@heroicons/react/24/outline"
import { Doughnut } from "react-chartjs-2"
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  ArcElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js"

ChartJS.register(CategoryScale, LinearScale, ArcElement, Title, Tooltip, Legend)


const EnhancedTeacherDashboard = () => {
  const { user } = useAuth()
  const [stats, setStats] = useState({
    myStudents: 0,
    myCourses: 0,
    todayClasses: 0,
    pendingGrades: 0,
    averageGrade: 0,
    attendanceRate: 0,
  })

  const [todaySchedule, setTodaySchedule] = useState([])
  const [students, setStudents] = useState([])
  const [courses, setCourses] = useState([])
  const [grades, setGrades] = useState([])
  const [assignments, setAssignments] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [activeTab, setActiveTab] = useState("overview")

  useEffect(() => {
    if (user?.id) {
      fetchTeacherData()
    }
  }, [user])

  const fetchTeacherData = async () => {
    try {
      setLoading(true)

      const [coursesRes, schedulesRes, studentsRes, gradesRes, assignmentsRes] = await Promise.all([
        api.get(`/teacher/courses/${user.id}`),
        api.get(`/teacher/schedule/${user.id}`),
        api.get(`/teacher/students/${user.id}`),
        api.get(`/teacher/grades/${user.id}`),
        api.get(`/teacher/assignments/${user.id}`),
      ])

      const coursesData = coursesRes.data?.data || []
      const schedulesData = schedulesRes.data?.data || []
      const studentsData = studentsRes.data?.data || []
      const gradesData = gradesRes.data?.data || []
      const assignmentsData = assignmentsRes.data?.data || []

      setCourses(coursesData)
      setStudents(studentsData)
      setGrades(gradesData)
      setAssignments(assignmentsData)

      // Filter today's schedule
      const today = new Date().toISOString().split("T")[0]
      const todayClasses = schedulesData.filter((schedule) => schedule.date === today)
      setTodaySchedule(todayClasses)

      // Calculate stats
      const avgGrade =
        gradesData.length > 0
          ? gradesData.reduce((sum, grade) => sum + Number.parseFloat(grade.note || 0), 0) / gradesData.length
          : 0

      const pendingGrades = assignmentsData.filter((assignment) => !assignment.graded).length

      setStats({
        myStudents: studentsData.length,
        myCourses: coursesData.length,
        todayClasses: todayClasses.length,
        pendingGrades: pendingGrades,
        averageGrade: avgGrade,
        attendanceRate: 92, // This would come from attendance data
      })
    } catch (err) {
      setError("Failed to fetch teacher data")
      console.error("Teacher dashboard error:", err)
    } finally {
      setLoading(false)
    }
  }

  const gradeDistributionData = {
    labels: ["A (16-20)", "B (12-15)", "C (8-11)", "D (0-7)"],
    datasets: [
      {
        data: [
          grades.filter((g) => g.note >= 16).length,
          grades.filter((g) => g.note >= 12 && g.note < 16).length,
          grades.filter((g) => g.note >= 8 && g.note < 12).length,
          grades.filter((g) => g.note < 8).length,
        ],
        backgroundColor: [
          "rgba(34, 197, 94, 0.8)",
          "rgba(59, 130, 246, 0.8)",
          "rgba(245, 158, 11, 0.8)",
          "rgba(239, 68, 68, 0.8)",
        ],
      },
    ],
  }

  const renderOverviewTab = () => (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {[
          {
            title: "My Students",
            value: stats.myStudents,
            icon: UsersIcon,
            color: "bg-blue-500",
            trend: "+5",
          },
          {
            title: "My Courses",
            value: stats.myCourses,
            icon: BookOpenIcon,
            color: "bg-green-500",
            trend: "0",
          },
          {
            title: "Today's Classes",
            value: stats.todayClasses,
            icon: ClockIcon,
            color: "bg-purple-500",
            trend: "0",
          },
          {
            title: "Pending Grades",
            value: stats.pendingGrades,
            icon: CheckCircleIcon,
            color: "bg-orange-500",
            trend: "-12",
          },
        ].map((stat, index) => {
          const Icon = stat.icon
          return (
            <div key={index} className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                  <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                  <p className={`text-sm ${Number.parseFloat(stat.trend) >= 0 ? "text-green-600" : "text-red-600"}`}>
                    {stat.trend > 0 ? "+" : ""}
                    {stat.trend}% from last week
                  </p>
                </div>
                <div className={`${stat.color} p-3 rounded-full`}>
                  <Icon className="h-6 w-6 text-white" />
                </div>
              </div>
            </div>
          )
        })}
      </div>

      {/* Charts and Today's Schedule */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Grade Distribution</h3>
          <div className="h-64">
            <Doughnut data={gradeDistributionData} options={{ responsive: true, maintainAspectRatio: false }} />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Today's Schedule</h3>
          <div className="space-y-3">
            {todaySchedule.length > 0 ? (
              todaySchedule.map((schedule) => (
                <div
                  key={schedule.id}
                  className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                >
                  <div className="text-sm font-medium text-blue-600">
                    {schedule.start_time} - {schedule.end_time}
                  </div>
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{schedule.course_name}</p>
                    <p className="text-xs text-gray-500">Room {schedule.room_number}</p>
                  </div>
                  <button className="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200">
                    Take Attendance
                  </button>
                </div>
              ))
            ) : (
              <p className="text-gray-500 text-center py-8">No classes scheduled for today</p>
            )}
          </div>
        </div>
      </div>
    </div>
  )

  const renderClassManagementTab = () => (
    <div className="space-y-6">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold text-gray-900">My Students</h3>
            <span className="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
              {students.length} Students
            </span>
          </div>
          <div className="space-y-3 max-h-96 overflow-y-auto">
            {students.map((student) => (
              <div
                key={student.id}
                className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-blue-600 font-medium">
                    {student.first_name?.charAt(0)}
                    {student.last_name?.charAt(0)}
                  </span>
                </div>
                <div className="flex-1">
                  <p className="text-sm font-medium text-gray-900">
                    {student.first_name} {student.last_name}
                  </p>
                  <p className="text-xs text-gray-500">Avg: {student.average_grade || "N/A"}/20</p>
                </div>
                <button className="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200">
                  View Profile
                </button>
              </div>
            ))}
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold text-gray-900">Pending Assignments</h3>
            <span className="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
              {stats.pendingGrades} To Grade
            </span>
          </div>
          <div className="space-y-3">
            {assignments
              .filter((a) => !a.graded)
              .slice(0, 5)
              .map((assignment) => (
                <div key={assignment.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="text-sm font-medium text-gray-900">{assignment.title}</p>
                    <p className="text-xs text-gray-500">Due: {new Date(assignment.due_date).toLocaleDateString()}</p>
                  </div>
                  <button className="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                    Grade Now
                  </button>
                </div>
              ))}
          </div>
        </div>
      </div>
    </div>
  )

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    )
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-4">
        <p className="text-red-600">{error}</p>
        <button onClick={fetchTeacherData} className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Retry
        </button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Teacher Dashboard</h1>
        <p className="text-gray-600">
          Welcome back, {user?.first_name}! Manage your classes and track student progress.
        </p>
      </div>

      {/* Tab Navigation */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: "overview", name: "Overview", icon: ChartBarIcon },
            { id: "classes", name: "Class Management", icon: UsersIcon },
            { id: "schedule", name: "Schedule", icon: CalendarIcon },
          ].map((tab) => {
            const Icon = tab.icon
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`flex items-center space-x-2 py-2 px-1 border-b-2 font-medium text-sm ${
                  activeTab === tab.id
                    ? "border-blue-500 text-blue-600"
                    : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                }`}
              >
                <Icon className="h-5 w-5" />
                <span>{tab.name}</span>
              </button>
            )
          })}
        </nav>
      </div>

      {/* Tab Content */}
      {activeTab === "overview" && renderOverviewTab()}
      {activeTab === "classes" && renderClassManagementTab()}
      {activeTab === "schedule" && (
        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Weekly Schedule</h3>
          <p className="text-gray-500">Schedule management coming soon...</p>
        </div>
      )}
    </div>
  )
}

export default EnhancedTeacherDashboard
