"use client"

import { useState, useEffect } from "react"
import {
  CurrencyDollarIcon,
  BanknotesIcon,
  UserIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  ExclamationTriangleIcon,
} from "@heroicons/react/24/outline"
import api from "../../services/api"

const FinancePage = () => {
  const [payments, setPayments] = useState([])
  const [students, setStudents] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [stats, setStats] = useState({
    totalRevenue: 0,
    paidPayments: 0,
    pendingPayments: 0,
    overduePayments: 0,
    revenueGrowth: 0,
    paymentGrowth: 0,
    avgPayment: 0,
  })
  const [selectedPeriod, setSelectedPeriod] = useState("all")

  useEffect(() => {
    fetchFinanceData()
  }, [])

  const fetchFinanceData = async () => {
    try {
      setLoading(true)
      setError(null)

      // Try multiple possible endpoints for payments
      let paymentsRes
      const paymentEndpoints = [
        "/financial/payments",
        "/payments",
        "/api/payments",
        "/financial/listStudentPayments",
      ]

      for (const endpoint of paymentEndpoints) {
        try {
          paymentsRes = await api.get(endpoint)
          console.log(`Successfully fetched from ${endpoint}:`, paymentsRes.data)
          break
        } catch (err) {
          console.log(`Failed to fetch from ${endpoint}:`, err.response?.status)
          if (err.response?.status !== 404) {
            throw err
          }
          continue
        }
      }

      if (!paymentsRes) {
        throw new Error("No valid payment endpoint found. Please check your backend routes.")
      }

      // Try multiple possible endpoints for students
      let studentsRes
      const studentEndpoints = ["/showStudent", "/students", "/api/students"]

      for (const endpoint of studentEndpoints) {
        try {
          studentsRes = await api.get(endpoint)
          break
        } catch (err) {
          if (err.response?.status !== 404) {
            throw err
          }
          continue
        }
      }

      // Handle different response structures for payments
      let paymentsData = []

      if (paymentsRes.data) {
        if (Array.isArray(paymentsRes.data)) {
          paymentsData = paymentsRes.data
        } else if (paymentsRes.data.payments && Array.isArray(paymentsRes.data.payments)) {
          paymentsData = paymentsRes.data.payments
        } else if (paymentsRes.data.data && Array.isArray(paymentsRes.data.data)) {
          paymentsData = paymentsRes.data.data
        } else if (typeof paymentsRes.data === "object") {
          // Try to find any array in the response
          const values = Object.values(paymentsRes.data)
          const arrayValue = values.find((val) => Array.isArray(val))
          if (arrayValue) {
            paymentsData = arrayValue
          } else {
            // Convert single object to array
            paymentsData = [paymentsRes.data]
          }
        }
      }

      console.log("Extracted payments data:", paymentsData)

      // Format payments with better error handling
      const formattedPayments = paymentsData
        .filter((payment) => payment && typeof payment === "object")
        .map((payment, index) => {
          // Ensure unique ID
          const id = payment.id || payment.payment_id || `payment-${index}`

          return {
            id: id,
            student_id: payment.student_id || payment.studentId || null,
            amount: typeof payment.amount === "string" ? Number.parseFloat(payment.amount) || 0 : payment.amount || 0,
            status: (payment.status || "pending").toLowerCase(),
            payment_date: payment.payment_date || payment.paymentDate || null,
            fee_name: payment.fee_name || payment.feeName || payment.description || "School Fee",
            student_name: payment.student_name || payment.studentName || null,
          }
        })

      setPayments(formattedPayments)

      // Handle students data
      let studentsData = []
      if (studentsRes?.data) {
        if (Array.isArray(studentsRes.data)) {
          studentsData = studentsRes.data
        } else if (studentsRes.data.data && Array.isArray(studentsRes.data.data)) {
          studentsData = studentsRes.data.data
        } else if (studentsRes.data.students && Array.isArray(studentsRes.data.students)) {
          studentsData = studentsRes.data.students
        }
      }

      setStudents(studentsData)

      // Calculate statistics with better error handling
      const totalRevenue = formattedPayments
        .filter((p) => p.status === "paid")
        .reduce((sum, p) => sum + (p.amount || 0), 0)

      const paidCount = formattedPayments.filter((p) => p.status === "paid").length
      const pendingCount = formattedPayments.filter((p) => p.status === "pending").length
      const overdueCount = formattedPayments.filter((p) => p.status === "overdue").length

      // Calculate monthly revenue with better date handling
      const currentDate = new Date()
      const currentMonthPayments = formattedPayments.filter((p) => {
        if (!p.payment_date) return false
        try {
          const paymentDate = new Date(p.payment_date)
          return (
            paymentDate.getMonth() === currentDate.getMonth() &&
            paymentDate.getFullYear() === currentDate.getFullYear() &&
            p.status === "paid"
          )
        } catch (err) {
          return false
        }
      })

      const currentMonthRevenue = currentMonthPayments.reduce((sum, p) => sum + (p.amount || 0), 0)

      const lastMonthRevenue = formattedPayments
        .filter((p) => {
          if (!p.payment_date) return false
          try {
            const paymentDate = new Date(p.payment_date)
            const lastMonth = new Date()
            lastMonth.setMonth(lastMonth.getMonth() - 1)
            return (
              paymentDate.getMonth() === lastMonth.getMonth() &&
              paymentDate.getFullYear() === lastMonth.getFullYear() &&
              p.status === "paid"
            )
          } catch (err) {
            return false
          }
        })
        .reduce((sum, p) => sum + (p.amount || 0), 0)

      // Calculate growth rates
      const revenueGrowth =
        lastMonthRevenue > 0 ? ((currentMonthRevenue - lastMonthRevenue) / lastMonthRevenue) * 100 : 0

      setStats({
        totalRevenue,
        paidPayments: paidCount,
        pendingPayments: pendingCount,
        overduePayments: overdueCount,
        revenueGrowth: Math.round(revenueGrowth * 100) / 100,
        paymentGrowth: paidCount > 0 ? 10 : 0,
        avgPayment: paidCount > 0 ? Math.round((totalRevenue / paidCount) * 100) / 100 : 0,
      })
    } catch (error) {
      console.error("Error fetching finance data:", error)
      let errorMessage = "Failed to load finance data. "

      if (error.message.includes("endpoint")) {
        errorMessage += "Backend API endpoints not found. Please check your server configuration."
      } else if (error.response?.status === 404) {
        errorMessage += "API endpoints not found (404). Please verify your backend routes."
      } else if (error.response?.status >= 500) {
        errorMessage += "Server error. Please try again later."
      } else {
        errorMessage += error.message || "Please try again later."
      }

      setError(errorMessage)
    } finally {
      setLoading(false)
    }
  }

  const getStudentName = (studentId) => {
    if (!studentId) return "Unknown Student"

    const student = students.find((s) => s.id === studentId)
    if (student) {
      const firstName = student.first_name || student.prenom || student.firstName || ""
      const lastName = student.last_name || student.nom || student.lastName || ""
      return `${firstName} ${lastName}`.trim() || `Student ${studentId}`
    }
    return `Student ${studentId}`
  }

  const getStatusColor = (status) => {
    switch (status) {
      case "paid":
        return "bg-green-100 text-green-800"
      case "pending":
        return "bg-yellow-100 text-yellow-800"
      case "overdue":
        return "bg-red-100 text-red-800"
      default:
        return "bg-gray-100 text-gray-800"
    }
  }

  const getStatusIcon = (status) => {
    switch (status) {
      case "paid":
        return <CheckCircleIcon className="h-4 w-4" />
      case "pending":
        return <ClockIcon className="h-4 w-4" />
      case "overdue":
        return <XCircleIcon className="h-4 w-4" />
      default:
        return <ClockIcon className="h-4 w-4" />
    }
  }

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
      minimumFractionDigits: 2,
    }).format(amount || 0)
  }

  const formatDate = (dateString) => {
    if (!dateString) return "Not set"
    try {
      return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      })
    } catch (err) {
      return "Invalid date"
    }
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        <span className="ml-3 text-gray-600">Loading financial data...</span>
      </div>
    )
  }

  if (error) {
    return (
      <div className="text-center py-12">
        <ExclamationTriangleIcon className="mx-auto h-12 w-12 text-red-500 mb-4" />
        <h3 className="text-lg font-medium text-gray-900 mb-2">Error Loading Financial Data</h3>
        <p className="text-red-500 mb-4 max-w-md mx-auto">{error}</p>
        <button
          onClick={() => {
            setError(null)
            fetchFinanceData()
          }}
          className="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors"
        >
          Try Again
        </button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Financial Management</h1>
          <p className="text-gray-600">Track payments, revenue, and financial reports</p>
        </div>
        <div className="flex space-x-3">
          <select
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="all">All Time</option>
            <option value="month">This Month</option>
            <option value="week">This Week</option>
          </select>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow p-6 text-white">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-green-100">Total Revenue</p>
              <p className="text-3xl font-bold">{formatCurrency(stats.totalRevenue)}</p>
              <p className="text-green-200 text-sm">
                {stats.revenueGrowth >= 0 ? "+" : ""}
                {stats.revenueGrowth}% from last month
              </p>
            </div>
            <CurrencyDollarIcon className="h-12 w-12 text-green-200" />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600">Paid Payments</p>
              <p className="text-3xl font-bold text-green-600">{stats.paidPayments}</p>
              <p className="text-green-500 text-sm">Completed</p>
            </div>
            <CheckCircleIcon className="h-12 w-12 text-green-500" />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600">Pending Payments</p>
              <p className="text-3xl font-bold text-yellow-600">{stats.pendingPayments}</p>
              <p className="text-yellow-500 text-sm">Awaiting payment</p>
            </div>
            <ClockIcon className="h-12 w-12 text-yellow-500" />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600">Overdue Payments</p>
              <p className="text-3xl font-bold text-red-600">{stats.overduePayments}</p>
              <p className="text-red-500 text-sm">Requires attention</p>
            </div>
            <XCircleIcon className="h-12 w-12 text-red-500" />
          </div>
        </div>
      </div>

      {/* Payments Table */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-200">
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-gray-900">Payment Records</h3>
            <div className="flex items-center space-x-2">
              <BanknotesIcon className="h-5 w-5 text-gray-400" />
              <span className="text-sm text-gray-600">{payments.length} total payments</span>
            </div>
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Student
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Amount
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Payment Date
                </th>
               
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Fee Type
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {payments.map((payment) => (
                <tr key={`payment-${payment.id}`} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center">
                      <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <UserIcon className="h-5 w-5 text-white" />
                      </div>
                      <div className="ml-4">
                        <div className="text-sm font-medium text-gray-900">{getStudentName(payment.student_id)}</div>
                        <div className="text-sm text-gray-500">ID: {payment.student_id || "N/A"}</div>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-lg font-bold text-gray-900">{formatCurrency(payment.amount)}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span
                      className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(payment.status)}`}
                    >
                      {getStatusIcon(payment.status)}
                      <span className="ml-1 capitalize">{payment.status}</span>
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {payment.payment_date ? formatDate(payment.payment_date) : "Not paid"}
                  </td>
                 
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{payment.fee_name}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {payments.length === 0 && (
          <div className="text-center py-12">
            <BanknotesIcon className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
            <p className="mt-1 text-sm text-gray-500">Get started by adding payment records.</p>
          </div>
        )}
      </div>
    </div>
  )
}

export default FinancePage
