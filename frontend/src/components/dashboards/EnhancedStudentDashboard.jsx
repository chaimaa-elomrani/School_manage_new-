"use client"

import { useState, useEffect } from "react"
import { useAuth } from "../../contexts/AuthContext"
import api from "../../services/api"
import {
  BookOpenIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  CalendarIcon,
  ChartBarIcon,
} from "@heroicons/react/24/outline"
import { Line, Bar } from "react-chartjs-2"
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js"

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend)

const EnhancedStudentDashboard = () => {
  const { user } = useAuth()
  const [stats, setStats] = useState({
    myTeachers: 0,
    myClassmates: 0,
    coursesCount: 0,
    evaluationsCount: 0,
    attendanceRate: 0,
    averageGrade: 0,
  })

  const [schedules, setSchedules] = useState([])
  const [teachers, setTeachers] = useState([])
  const [grades, setGrades] = useState([])
  const [payments, setPayments] = useState([])
  const [assignments, setAssignments] = useState([])
  const [announcements, setAnnouncements] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [activeTab, setActiveTab] = useState("overview")

  useEffect(() => {
    if (user?.id) {
      fetchStudentData()
    }
  }, [user])

  const fetchStudentData = async () => {
    try {
      setLoading(true)

      const [studentRes, scheduleRes, teachersRes, gradesRes, paymentsRes, assignmentsRes, announcementsRes] =
        await Promise.all([
          api.get(`/student/profile/${user.id}`),
          api.get(`/student/schedule/${user.id}`),
          api.get(`/student/teachers/${user.id}`),
          api.get(`/student/grades/${user.id}`),
          api.get(`/student/payments/${user.id}`),
          api.get(`/student/assignments/${user.id}`),
          api.get(`/student/announcements/${user.id}`),
        ])

      const studentData = studentRes.data?.data || {}
      const scheduleData = scheduleRes.data?.data || []
      const teacherData = teachersRes.data?.data || []
      const gradeData = gradesRes.data?.data || []
      const paymentData = paymentsRes.data?.data || []
      const assignmentData = assignmentsRes.data?.data || []
      const announcementData = announcementsRes.data?.data || []

      setSchedules(scheduleData)
      setTeachers(teacherData)
      setGrades(gradeData)
      setPayments(paymentData)
      setAssignments(assignmentData)
      setAnnouncements(announcementData)

      // Calculate stats
      const avgGrade =
        gradeData.length > 0
          ? gradeData.reduce((sum, grade) => sum + Number.parseFloat(grade.note || 0), 0) / gradeData.length
          : 0

      setStats({
        myTeachers: teacherData.length,
        myClassmates: studentData.classmates_count || 0,
        coursesCount: new Set(scheduleData.map((s) => s.course_id)).size,
        evaluationsCount: gradeData.length,
        attendanceRate: studentData.attendance_rate || 95,
        averageGrade: avgGrade,
      })
    } catch (err) {
      console.error("Error fetching student data:", err)
      setError(err.message || "Failed to fetch student data")
    } finally {
      setLoading(false)
    }
  }

  const gradeChartData = {
    labels: grades.map((grade) => grade.subject_name || "Subject"),
    datasets: [
      {
        label: "Grades",
        data: grades.map((grade) => Number.parseFloat(grade.note || 0)),
        borderColor: "rgb(59, 130, 246)",
        backgroundColor: "rgba(59, 130, 246, 0.1)",
        tension: 0.4,
      },
    ],
  }

  const attendanceChartData = {
    labels: ["Present", "Absent", "Late"],
    datasets: [
      {
        label: "Attendance",
        data: [stats.attendanceRate, 100 - stats.attendanceRate, 5],
        backgroundColor: ["rgba(34, 197, 94, 0.8)", "rgba(239, 68, 68, 0.8)", "rgba(245, 158, 11, 0.8)"],
      },
    ],
  }

  const renderOverviewTab = () => (
    <div className="space-y-6">
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {[
          {
            title: "Average Grade",
            value: `${stats.averageGrade.toFixed(1)}/20`,
            icon: ChartBarIcon,
            color: "bg-blue-500",
            trend: "+2.1",
          },
          {
            title: "Attendance Rate",
            value: `${stats.attendanceRate}%`,
            icon: CalendarIcon,
            color: "bg-green-500",
            trend: "+1.2",
          },
          {
            title: "My Courses",
            value: stats.coursesCount,
            icon: BookOpenIcon,
            color: "bg-purple-500",
            trend: "0",
          },
          {
            title: "Assignments Due",
            value: assignments.filter((a) => !a.submitted).length,
            icon: DocumentTextIcon,
            color: "bg-orange-500",
            trend: "-3",
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
                    {stat.trend}% from last month
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

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Grade Performance</h3>
          <div className="h-64">
            <Line data={gradeChartData} options={{ responsive: true, maintainAspectRatio: false }} />
          </div>
        </div>
        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Attendance Overview</h3>
          <div className="h-64">
            <Bar data={attendanceChartData} options={{ responsive: true, maintainAspectRatio: false }} />
          </div>
        </div>
      </div>
    </div>
  )

  const renderAcademicTab = () => (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">My Schedule</h3>
        <div className="space-y-3">
          {schedules.map((schedule) => (
            <div
              key={schedule.id}
              className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div className="text-sm font-medium text-blue-600">
                {schedule.start_time} - {schedule.end_time}
              </div>
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">{schedule.course_name}</p>
                <p className="text-xs text-gray-500">
                  Room {schedule.room_number} • {schedule.teacher_name}
                </p>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Recent Grades</h3>
        <div className="space-y-3">
          {grades.slice(0, 5).map((grade) => (
            <div key={grade.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
              <div
                className={`w-3 h-3 rounded-full ${
                  grade.note >= 16 ? "bg-green-500" : grade.note >= 12 ? "bg-yellow-500" : "bg-red-500"
                }`}
              />
              <div className="flex-1">
                <p className="text-sm font-medium text-gray-900">{grade.subject_name}</p>
                <p className="text-xs text-gray-500">{grade.evaluation_type}</p>
              </div>
              <div className="text-right">
                <p className="text-sm font-bold text-gray-900">{grade.note}/20</p>
                <p className="text-xs text-gray-500">{new Date(grade.date).toLocaleDateString()}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )

  const renderFinancialTab = () => (
    <div className="space-y-6">
      <div className="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg shadow-md p-6 border border-green-200">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h3 className="text-xl font-bold text-green-900">Payment Status</h3>
            <p className="text-green-600">Your financial overview</p>
          </div>
          <CurrencyDollarIcon className="h-8 w-8 text-green-600" />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="bg-white rounded-lg p-4">
            <p className="text-sm text-gray-600">Total Paid</p>
            <p className="text-2xl font-bold text-green-600">
              ${payments.filter((p) => p.status === "paid").reduce((sum, p) => sum + Number.parseFloat(p.amount), 0)}
            </p>
          </div>
          <div className="bg-white rounded-lg p-4">
            <p className="text-sm text-gray-600">Outstanding</p>
            <p className="text-2xl font-bold text-red-600">
              ${payments.filter((p) => p.status === "pending").reduce((sum, p) => sum + Number.parseFloat(p.amount), 0)}
            </p>
          </div>
          <div className="bg-white rounded-lg p-4">
            <p className="text-sm text-gray-600">Next Due</p>
            <p className="text-2xl font-bold text-orange-600">
              ${payments.find((p) => p.status === "pending")?.amount || 0}
            </p>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
        <div className="space-y-3">
          {payments.map((payment) => (
            <div key={payment.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p className="text-sm font-medium text-gray-900">{payment.description}</p>
                <p className="text-xs text-gray-500">{new Date(payment.date).toLocaleDateString()}</p>
              </div>
              <div className="text-right">
                <p className="text-sm font-bold text-gray-900">${payment.amount}</p>
                <span
                  className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${
                    payment.status === "paid" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
                  }`}
                >
                  {payment.status}
                </span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500" />
      </div>
    )
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-4">
        <p className="text-red-600">{error}</p>
        <button onClick={fetchStudentData} className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Retry
        </button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Student Dashboard</h1>
        <p className="text-gray-600">Welcome back, {user?.first_name}!</p>
      </div>

      {/* Tab Navigation */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: "overview", name: "Overview", icon: ChartBarIcon },
            { id: "academic", name: "Academic", icon: BookOpenIcon },
            { id: "financial", name: "Financial", icon: CurrencyDollarIcon },
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
      {activeTab === "academic" && renderAcademicTab()}
      {activeTab === "financial" && renderFinancialTab()}
    </div>
  )
}

export default EnhancedStudentDashboard
