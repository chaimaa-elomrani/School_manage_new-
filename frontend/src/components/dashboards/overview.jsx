"use client"

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { BookOpen, Calendar, BarChartIcon as ChartBar, ClipboardList, CheckCircle, AlertTriangle } from 'lucide-react'
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
  ArcElement 
} from "chart.js"
import { Line, Bar, Doughnut } from "react-chartjs-2"

ChartJS.register(
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  BarElement, 
  ArcElement,
  Title, 
  Tooltip, 
  Legend
)

export function Overview({ data }) {
  // Calculate stats
  const averageGrade = data.grades.length > 0 
    ? data.grades.reduce((sum, grade) => sum + Number(grade.note || 0), 0) / data.grades.length 
    : 0
  
  const attendanceRate = data.profile?.attendance_rate || 95
  const coursesCount = new Set(data.schedule.map(s => s.course_id)).size
  const assignmentsDue = data.evaluations?.filter(e => e.status === "pending").length || 0

  // Chart data
  const gradeChartData = {
    labels: data.grades.slice(0, 10).map(grade => grade.subject_name || "Subject"),
    datasets: [
      {
        label: "Grades",
        data: data.grades.slice(0, 10).map(grade => Number(grade.note || 0)),
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
        data: [attendanceRate, 100 - attendanceRate - 5, 5],
        backgroundColor: [
          "rgba(34, 197, 94, 0.8)",
          "rgba(239, 68, 68, 0.8)",
          "rgba(245, 158, 11, 0.8)",
        ],
      },
    ],
  }

  const stats = [
    {
      title: "Average Grade",
      value: `${averageGrade.toFixed(1)}/20`,
      icon: ChartBar,
      color: "bg-blue-500",
      trend: "+2.1",
      status: averageGrade >= 12 ? "good" : "warning",
    },
    {
      title: "Attendance Rate",
      value: `${attendanceRate}%`,
      icon: Calendar,
      color: "bg-green-500",
      trend: "+1.2",
      status: attendanceRate >= 90 ? "good" : "warning",
    },
    {
      title: "My Courses",
      value: coursesCount,
      icon: BookOpen,
      color: "bg-purple-500",
      trend: "0",
      status: "info",
    },
    {
      title: "Pending Evaluations",
      value: assignmentsDue,
      icon: ClipboardList,
      color: "bg-orange-500",
      trend: "-3",
      status: assignmentsDue === 0 ? "good" : "warning",
    },
  ]

  return (
    <>
      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat, index) => {
          const Icon = stat.icon
          return (
            <Card key={index} className="hover:shadow-md transition-shadow">
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                    <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                    <p className={`text-sm ${Number(stat.trend) >= 0 ? "text-green-600" : "text-red-600"}`}>
                      {Number(stat.trend) > 0 ? "+" : ""}{stat.trend}% from last month
                    </p>
                  </div>
                  <div className={`${stat.color} p-3 rounded-full`}>
                    <Icon className="h-6 w-6 text-white" />
                  </div>
                </div>
                <div className="flex items-center mt-2">
                  {stat.status === "good" && <CheckCircle className="h-4 w-4 text-green-500 mr-1" />}
                  {stat.status === "warning" && <AlertTriangle className="h-4 w-4 text-yellow-500 mr-1" />}
                  <span className={`text-xs ${
                    stat.status === "good" ? "text-green-600" : 
                    stat.status === "warning" ? "text-yellow-600" : "text-gray-600"
                  }`}>
                    {stat.status === "good" ? "Good" : stat.status === "warning" ? "Needs attention" : "Monitor"}
                  </span>
                </div>
              </CardContent>
            </Card>
          )
        })}
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Grade Performance</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-64">
              <Line 
                data={gradeChartData} 
                options={{ 
                  responsive: true, 
                  maintainAspectRatio: false,
                  scales: {
                    y: {
                      beginAtZero: true,
                      max: 20
                    }
                  }
                }} 
              />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Attendance Overview</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-64">
              <Doughnut 
                data={attendanceChartData} 
                options={{ 
                  responsive: true, 
                  maintainAspectRatio: false 
                }} 
              />
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Recent Announcements */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Announcements</CardTitle>
        </CardHeader>
        <CardContent>
          {data.announcements && data.announcements.length > 0 ? (
            <div className="space-y-3">
              {data.announcements.slice(0, 3).map((announcement, index) => (
                <div key={index} className="p-3 bg-blue-50 rounded-lg border border-blue-200">
                  <div className="flex items-center justify-between mb-2">
                    <p className="text-sm font-medium text-blue-900">{announcement.title}</p>
                    <p className="text-xs text-blue-600">
                      {new Date(announcement.created_at).toLocaleDateString()}
                    </p>
                  </div>
                  <p className="text-sm text-blue-800">{announcement.content}</p>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-gray-500 text-center py-4">No recent announcements</p>
          )}
        </CardContent>
      </Card>
    </>
  )
}
