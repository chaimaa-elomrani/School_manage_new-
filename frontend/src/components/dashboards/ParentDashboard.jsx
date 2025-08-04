"use client"

import { useState, useEffect } from "react"
import { useAuth } from "../../contexts/AuthContext"
import api from "../../services/api"
import {
  UserIcon,
  AcademicCapIcon,
  CurrencyDollarIcon,
  CalendarIcon,
  ChatBubbleLeftRightIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  ClockIcon,
} from "@heroicons/react/24/outline"
import { Line, Bar } from "react-chartjs-2"

const ParentDashboard = () => {
  const { user } = useAuth()
  const [children, setChildren] = useState([])
  const [selectedChild, setSelectedChild] = useState(null)
  const [childStats, setChildStats] = useState({
    averageGrade: 0,
    attendanceRate: 0,
    assignmentsDue: 0,
    upcomingExams: 0,
  })
  const [grades, setGrades] = useState([])
  const [payments, setPayments] = useState([])
  const [messages, setMessages] = useState([])
  const [announcements, setAnnouncements] = useState([])
  const [schedule, setSchedule] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [activeTab, setActiveTab] = useState("overview")

  useEffect(() => {
    if (user?.id) {
      fetchParentData()
    }
  }, [user])

  useEffect(() => {
    if (selectedChild) {
      fetchChildData(selectedChild.id)
    }
  }, [selectedChild])

  const fetchParentData = async () => {
    try {
      setLoading(true)

      const [childrenRes, paymentsRes, messagesRes, announcementsRes] = await Promise.all([
        api.get(`/parent/children/${user.id}`),
        api.get(`/parent/payments/${user.id}`),
        api.get(`/parent/messages/${user.id}`),
        api.get(`/parent/announcements/${user.id}`),
      ])

      const childrenData = childrenRes.data?.data || []
      const paymentsData = paymentsRes.data?.data || []
      const messagesData = messagesRes.data?.data || []
      const announcementsData = announcementsRes.data?.data || []

      setChildren(childrenData)
      setPayments(paymentsData)
      setMessages(messagesData)
      setAnnouncements(announcementsData)

      // Set first child as selected by default
      if (childrenData.length > 0) {
        setSelectedChild(childrenData[0])
      }
    } catch (err) {
      console.error("Error fetching parent data:", err)
      setError(err.message || "Failed to fetch parent data")
    } finally {
      setLoading(false)
    }
  }

  const fetchChildData = async (childId) => {
    try {
      const [gradesRes, scheduleRes, statsRes] = await Promise.all([
        api.get(`/parent/child/grades/${childId}`),
        api.get(`/parent/child/schedule/${childId}`),
        api.get(`/parent/child/stats/${childId}`),
      ])

      const gradesData = gradesRes.data?.data || []
      const scheduleData = scheduleRes.data?.data || []
      const statsData = statsRes.data?.data || {}

      setGrades(gradesData)
      setSchedule(scheduleData)
      setChildStats({
        averageGrade: statsData.average_grade || 0,
        attendanceRate: statsData.attendance_rate || 0,
        assignmentsDue: statsData.assignments_due || 0,
        upcomingExams: statsData.upcoming_exams || 0,
      })
    } catch (err) {
      console.error("Error fetching child data:", err)
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
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    datasets: [
      {
        label: "Attendance %",
        data: [95, 92, 98, 90, 94, 96],
        backgroundColor: "rgba(34, 197, 94, 0.8)",
      },
    ],
  }

  const renderOverviewTab = () => (
    <div className="space-y-6">
      {/* Child Selector */}
      <div className="bg-white rounded-lg shadow-md p-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Select Child</h3>
        <div className="flex space-x-4">
          {children.map((child) => (
            <button
              key={child.id}
              onClick={() => setSelectedChild(child)}
              className={`flex items-center space-x-3 p-3 rounded-lg border-2 transition-colors ${
                selectedChild?.id === child.id ? "border-blue-500 bg-blue-50" : "border-gray-200 hover:border-gray-300"
              }`}
            >
              <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span className="text-blue-600 font-medium">
                  {child.first_name?.charAt(0)}
                  {child.last_name?.charAt(0)}
                </span>
              </div>
              <div className="text-left">
                <p className="text-sm font-medium text-gray-900">
                  {child.first_name} {child.last_name}
                </p>
                <p className="text-xs text-gray-500">Grade {child.grade || "N/A"}</p>
              </div>
            </button>
          ))}
        </div>
      </div>

      {selectedChild && (
        <>
          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              {
                title: "Average Grade",
                value: `${childStats.averageGrade.toFixed(1)}/20`,
                icon: AcademicCapIcon,
                color: "bg-blue-500",
                status: childStats.averageGrade >= 12 ? "good" : "warning",
              },
              {
                title: "Attendance Rate",
                value: `${childStats.attendanceRate}%`,
                icon: CalendarIcon,
                color: "bg-green-500",
                status: childStats.attendanceRate >= 90 ? "good" : "warning",
              },
              {
                title: "Assignments Due",
                value: childStats.assignmentsDue,
                icon: ClockIcon,
                color: "bg-orange-500",
                status: childStats.assignmentsDue === 0 ? "good" : "warning",
              },
              {
                title: "Upcoming Exams",
                value: childStats.upcomingExams,
                icon: ExclamationTriangleIcon,
                color: "bg-red-500",
                status: "info",
              },
            ].map((stat, index) => {
              const Icon = stat.icon
              return (
                <div key={index} className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                      <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                      <div className="flex items-center mt-1">
                        {stat.status === "good" && <CheckCircleIcon className="h-4 w-4 text-green-500 mr-1" />}
                        {stat.status === "warning" && (
                          <ExclamationTriangleIcon className="h-4 w-4 text-yellow-500 mr-1" />
                        )}
                        <span
                          className={`text-xs ${
                            stat.status === "good"
                              ? "text-green-600"
                              : stat.status === "warning"
                                ? "text-yellow-600"
                                : "text-gray-600"
                          }`}
                        >
                          {stat.status === "good" ? "Good" : stat.status === "warning" ? "Needs attention" : "Monitor"}
                        </span>
                      </div>
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
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Academic Performance</h3>
              <div className="h-64">
                <Line data={gradeChartData} options={{ responsive: true, maintainAspectRatio: false }} />
              </div>
            </div>
            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Attendance Trend</h3>
              <div className="h-64">
                <Bar data={attendanceChartData} options={{ responsive: true, maintainAspectRatio: false }} />
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  )

  const renderAcademicTab = () => (
    <div className="space-y-6">
      {selectedChild && (
        <>
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

            <div className="bg-white rounded-lg shadow-md p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Class Schedule</h3>
              <div className="space-y-3">
                {schedule.map((item) => (
                  <div key={item.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div className="text-sm font-medium text-blue-600">
                      {item.start_time} - {item.end_time}
                    </div>
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-900">{item.course_name}</p>
                      <p className="text-xs text-gray-500">
                        Room {item.room_number} • {item.teacher_name}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  )

  const renderFinancialTab = () => (
    <div className="space-y-6">
      <div className="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg shadow-md p-6 border border-green-200">
        <div className="flex items-center justify-between mb-4">
          <div>
            <h3 className="text-xl font-bold text-green-900">Payment Overview</h3>
            <p className="text-green-600">Manage your child's school fees</p>
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
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-lg font-semibold text-gray-900">Payment History</h3>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Make Payment</button>
        </div>
        <div className="space-y-3">
          {payments.map((payment) => (
            <div key={payment.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <p className="text-sm font-medium text-gray-900">{payment.description}</p>
                <p className="text-xs text-gray-500">
                  {payment.child_name} • {new Date(payment.date).toLocaleDateString()}
                </p>
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

  const renderCommunicationTab = () => (
    <div className="space-y-6">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow-md p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold text-gray-900">Messages</h3>
            <button className="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">New Message</button>
          </div>
          <div className="space-y-3">
            {messages.map((message) => (
              <div key={message.id} className="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div className="flex items-center justify-between mb-2">
                  <p className="text-sm font-medium text-gray-900">{message.sender_name}</p>
                  <p className="text-xs text-gray-500">{new Date(message.date).toLocaleDateString()}</p>
                </div>
                <p className="text-sm text-gray-700">{message.subject}</p>
                <p className="text-xs text-gray-500 mt-1">{message.preview}</p>
              </div>
            ))}
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-md p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">School Announcements</h3>
          <div className="space-y-3">
            {announcements.map((announcement) => (
              <div key={announcement.id} className="p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div className="flex items-center justify-between mb-2">
                  <p className="text-sm font-medium text-blue-900">{announcement.title}</p>
                  <p className="text-xs text-blue-600">{new Date(announcement.date).toLocaleDateString()}</p>
                </div>
                <p className="text-sm text-blue-800">{announcement.content}</p>
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
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500" />
      </div>
    )
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-4">
        <p className="text-red-600">{error}</p>
        <button onClick={fetchParentData} className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Retry
        </button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Parent Dashboard</h1>
        <p className="text-gray-600">Monitor your child's academic progress and school activities</p>
      </div>

      {/* Tab Navigation */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: "overview", name: "Overview", icon: UserIcon },
            { id: "academic", name: "Academic", icon: AcademicCapIcon },
            { id: "financial", name: "Financial", icon: CurrencyDollarIcon },
            { id: "communication", name: "Communication", icon: ChatBubbleLeftRightIcon },
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
      {activeTab === "communication" && renderCommunicationTab()}
    </div>
  )
}

export default ParentDashboard
