"use client"

import { useState, useEffect } from "react"
import { Users, GraduationCap, BookOpen, DollarSign, CheckCircle, TrendingUp } from "lucide-react"


// Mock API data for demonstration purposes


const AdminDashboard = () => {
  const [stats, setStats] = useState({
    totalStudents: 0,
    totalTeachers: 0,
    totalCourses: 0,
    totalRevenue: 0,
  })
  const [recentEnrollments, setRecentEnrollments] = useState([])
  const [recentPayments, setRecentPayments] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchAdminData()
  }, [])

  const fetchAdminData = async () => {
    try {
      setLoading(true)
      // Simulate API calls with mock data
      await new Promise((resolve) => setTimeout(resolve, 1000)) // Simulate network delay

      const students = mockStudents
      const teachers = mockTeachers
      const courses = mockCourses
      const payments = mockPayments

      // Set stats
      setStats({
        totalStudents: students.length,
        totalTeachers: teachers.length,
        totalCourses: courses.length,
        totalRevenue: calculateTotalRevenue(payments),
      })

      // Set recent payments
      setRecentPayments(formatRecentPayments(payments))

      // Set recent enrollments
      setRecentEnrollments(formatRecentEnrollments(students))
    } catch (error) {
      console.error("Error fetching admin data:", error)
    } finally {
      setLoading(false)
    }
  }

  // Helper functions
  const calculateTotalRevenue = (payments) => {
    return payments
      .filter((payment) => payment.status === "paid")
      .reduce((sum, payment) => sum + Number.parseFloat(payment.amount || 0), 0)
  }

  const formatRecentPayments = (payments) => {
    return payments
      .filter((payment) => payment.status === "paid")
      .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
      .slice(0, 5)
      .map((payment) => ({
        ...payment,
        initials: payment.studentName?.substring(0, 2).toUpperCase() || "NA",
      }))
  }

  const formatRecentEnrollments = (students) => {
    return students
      .sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
      .slice(0, 5)
      .map((student) => ({
        studentName: `${student.first_name} ${student.last_name}`,
        initials: (student.first_name?.[0] || "") + (student.last_name?.[0] || ""),
        date: student.createdAt || new Date().toISOString(),
        courseName: student.course || "General Enrollment",
      }))
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    )
  }

  const statCards = [
    {
      title: "Total Students",
      value: stats.totalStudents,
      icon: Users,
      color: "bg-blue-500",
      change: "+12%",
    },
    {
      title: "Total Teachers",
      value: stats.totalTeachers,
      icon: GraduationCap,
      color: "bg-green-500",
      change: "+5%",
    },
    {
      title: "Active Courses",
      value: stats.totalCourses,
      icon: BookOpen,
      color: "bg-purple-500",
      change: "+8%",
    },
    {
      title: "Total Revenue",
      value: `$${stats.totalRevenue.toLocaleString()}`,
      icon: DollarSign,
      color: "bg-yellow-500",
      change: "+15%",
    },
  ]

  return (
    <div className="space-y-6 p-6 md:p-10 bg-gray-50 min-h-screen">
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p className="text-gray-600 mt-1">Welcome back! Here's what's happening at your school.</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {statCards.map((stat, index) => {
          const Icon = stat.icon
          return (
            <Card key={index} className="shadow-sm">
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardDescription className="text-sm font-medium">{stat.title}</CardDescription>
                <div className={`${stat.color} p-2 rounded-md`}>
                  <Icon className="h-5 w-5 text-white" />
                </div>
              </CardHeader>
              <CardContent>
                <CardTitle className="text-2xl font-bold">{stat.value}</CardTitle>
                <p className="text-xs text-muted-foreground mt-1 text-green-600">{stat.change} from last month</p>
              </CardContent>
            </Card>
          )
        })}
      </div>

      {/* Financial Overview Section */}
      <Card className="bg-gradient-to-r from-green-50 to-emerald-50 shadow-sm border border-green-200">
        <CardHeader className="flex flex-row items-center justify-between">
          <div>
            <CardTitle className="text-xl font-bold text-green-900">Financial Overview</CardTitle>
            <CardDescription className="text-green-600">Revenue and payment tracking</CardDescription>
          </div>
          <div className="bg-green-500 p-3 rounded-full">
            <DollarSign className="h-8 w-8 text-white" />
          </div>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card className="shadow-sm border border-green-100">
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p className="text-3xl font-bold text-green-600">${stats.totalRevenue.toLocaleString()}</p>
                    <p className="text-sm text-green-500 mt-1">+15% from last month</p>
                  </div>
                  <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <DollarSign className="h-6 w-6 text-green-600" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="shadow-sm border border-blue-100">
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Paid Payments</p>
                    <p className="text-3xl font-bold text-blue-600">{recentPayments.length}</p>
                    <p className="text-sm text-blue-500 mt-1">Recent transactions</p>
                  </div>
                  <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <CheckCircle className="h-6 w-6 text-blue-600" />
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card className="shadow-sm border border-yellow-100">
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm font-medium text-gray-600">Avg Payment</p>
                    <p className="text-3xl font-bold text-yellow-600">
                      ${recentPayments.length > 0 ? (stats.totalRevenue / recentPayments.length).toFixed(0) : "0"}
                    </p>
                    <p className="text-sm text-yellow-500 mt-1">Per transaction</p>
                  </div>
                  <div className="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <TrendingUp className="h-6 w-6 text-yellow-600" />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </CardContent>
      </Card>

      {/* Recent Activity */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle className="text-lg font-semibold text-gray-900">Recent Enrollments</CardTitle>
            <Badge variant="outline" className="bg-blue-100 text-blue-800">
              {recentEnrollments.length} New
            </Badge>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {recentEnrollments.length > 0 ? (
                recentEnrollments.map((enrollment, index) => (
                  <div
                    key={index}
                    className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                  >
                    <Avatar className="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 shadow-md">
                      <AvatarFallback className="text-white text-sm font-bold">{enrollment.initials}</AvatarFallback>
                    </Avatar>
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-900">{enrollment.studentName}</p>
                      <p className="text-xs text-gray-500">Enrolled in {enrollment.courseName}</p>
                    </div>
                    <div className="text-xs text-gray-400">{new Date(enrollment.date).toLocaleDateString()}</div>
                  </div>
                ))
              ) : (
                <div className="text-center py-8">
                  <div className="w-12 h-12 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                    <Users className="h-6 w-6 text-gray-400" />
                  </div>
                  <p className="text-gray-500 text-sm">No recent enrollments</p>
                </div>
              )}
            </div>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle className="text-lg font-semibold text-gray-900">Recent Payments</CardTitle>
            <Badge variant="outline" className="bg-green-100 text-green-800">
              ${stats.totalRevenue.toLocaleString()}
            </Badge>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {recentPayments.length > 0 ? (
                recentPayments.map((payment, index) => (
                  <div
                    key={index}
                    className="flex items-center space-x-3 p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100 hover:shadow-md transition-all"
                  >
                    <Avatar className="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 shadow-md">
                      <AvatarFallback className="text-white text-sm font-bold">{payment.initials}</AvatarFallback>
                    </Avatar>
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-900">{payment.studentName}</p>
                      <div className="flex items-center space-x-2">
                        <span className="text-lg font-bold text-green-600">${payment.amount}</span>
                        <Badge className="bg-green-100 text-green-800">Paid</Badge>
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-xs text-gray-400">{new Date(payment.date).toLocaleDateString()}</div>
                      <div className="text-xs text-green-600 font-medium">Completed</div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-8">
                  <div className="w-12 h-12 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                    <DollarSign className="h-6 w-6 text-gray-400" />
                  </div>
                  <p className="text-gray-500 text-sm">No recent payments</p>
                </div>
              )}
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Quick Actions */}
      <Card className="shadow-sm">
        <CardHeader>
          <CardTitle className="text-lg font-semibold text-gray-900">Quick Actions</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Button
              variant="ghost"
              className="text-left p-4 h-auto bg-blue-50 hover:bg-blue-100 rounded-lg flex flex-col items-start"
            >
              <p className="font-medium text-blue-900">Add New Student</p>
              <p className="text-sm text-blue-600 mt-1">Register a new student to the system</p>
            </Button>
            <Button
              variant="ghost"
              className="text-left p-4 h-auto bg-green-50 hover:bg-green-100 rounded-lg flex flex-col items-start"
            >
              <p className="font-medium text-green-900">Create Course</p>
              <p className="text-sm text-green-600 mt-1">Set up a new course curriculum</p>
            </Button>
            <Button
              variant="ghost"
              className="text-left p-4 h-auto bg-purple-50 hover:bg-purple-100 rounded-lg flex flex-col items-start"
            >
              <p className="font-medium text-purple-900">Generate Report</p>
              <p className="text-sm text-purple-600 mt-1">Create monthly performance reports</p>
            </Button> 
          </div>
        </CardContent>
      </Card>
    </div>
  )
}

export default AdminDashboard
