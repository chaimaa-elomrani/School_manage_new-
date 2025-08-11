"use client"
import { useState, useEffect } from "react"
import api from "../../services/api"
import ScheduleAgendaTable from "./ScheduleAgendaTable"
import {
  CalendarIcon,
  ViewColumnsIcon,
  ListBulletIcon,
  TableCellsIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ClockIcon,
  UserIcon,
  BuildingOfficeIcon,
  AcademicCapIcon,
  ExclamationTriangleIcon,
} from "@heroicons/react/24/outline"

const ScheduleList = () => {
  const [schedules, setSchedules] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [viewMode, setViewMode] = useState("agenda")
  const [currentDate, setCurrentDate] = useState(new Date())
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split("T")[0])

  useEffect(() => {
    fetchSchedules()
  }, [])

  const fetchSchedules = async () => {
    try {
      setLoading(true)
      setError(null)

      // Try multiple possible endpoints
      let response
      const possibleEndpoints = ["/showSchedules", "/schedules", "/api/schedules"]

      for (const endpoint of possibleEndpoints) {
        try {
          response = await api.get(endpoint)
          break
        } catch (err) {
          if (err.response?.status !== 404) {
            throw err
          }
          continue
        }
      }

      if (!response) {
        throw new Error("No valid schedule endpoint found")
      }

      console.log("Raw schedule response:", response.data)

      // Handle different response structures
      let scheduleData = []

      if (Array.isArray(response.data)) {
        scheduleData = response.data
      } else if (response.data?.data && Array.isArray(response.data.data)) {
        scheduleData = response.data.data
      } else if (response.data?.schedules && Array.isArray(response.data.schedules)) {
        scheduleData = response.data.schedules
      } else if (typeof response.data === "object" && response.data !== null) {
        // If it's an object, try to extract array values
        const values = Object.values(response.data)
        const arrayValue = values.find((val) => Array.isArray(val))
        if (arrayValue) {
          scheduleData = arrayValue
        } else {
          // Convert single object to array
          scheduleData = [response.data]
        }
      }

      // Validate and format schedules
      const formattedSchedules = scheduleData
        .filter((schedule) => schedule && typeof schedule === "object")
        .map((schedule, index) => {
          // Ensure we have a unique ID
          const id = schedule.id || schedule.schedule_id || `schedule-${index}`

          return {
            id: id,
            date: schedule.date
              ? new Date(schedule.date).toISOString().split("T")[0]
              : new Date().toISOString().split("T")[0],
            start_time: schedule.start_time || schedule.startTime || "09:00:00",
            end_time: schedule.end_time || schedule.endTime || "10:00:00",
            course_name: schedule.course_name || schedule.courseName || schedule.course || "Unnamed Course",
            teacher_name: schedule.teacher_name || schedule.teacherName || schedule.teacher || "Unassigned",
            teacher_id: schedule.teacher_id || schedule.teacherId || null,
            room_number: schedule.room_number || schedule.roomNumber || schedule.room_id || schedule.roomId || "TBD",
            course_id: schedule.course_id || schedule.courseId || null,
          }
        })

      console.log("Formatted schedules:", formattedSchedules)
      setSchedules(formattedSchedules)
    } catch (error) {
      console.error("Failed to fetch schedules:", error)
      setError(error.message || "Failed to load schedules")
      setSchedules([])
    } finally {
      setLoading(false)
    }
  }

  const getWeekDates = (date) => {
    const week = []
    const startDate = new Date(date)
    startDate.setHours(0, 0, 0, 0)

    const day = startDate.getDay()
    const diff = startDate.getDate() - day + (day === 0 ? -6 : 1)
    startDate.setDate(diff)

    for (let i = 0; i < 7; i++) {
      const currentDate = new Date(startDate)
      currentDate.setDate(startDate.getDate() + i)
      week.push(currentDate.toISOString().split("T")[0])
    }

    return week
  }

  const getSchedulesForDate = (date) => {
    if (!date || !Array.isArray(schedules)) return []

    const formattedDate = new Date(date).toISOString().split("T")[0]
    return schedules.filter((schedule) => {
      try {
        return schedule.date === formattedDate
      } catch (err) {
        console.error("Error comparing dates:", err)
        return false
      }
    })
  }

  const getSchedulesForWeek = () => {
    try {
      const weekDates = getWeekDates(currentDate)
      return weekDates.map((date) => ({
        date,
        schedules: getSchedulesForDate(date),
      }))
    } catch (err) {
      console.error("Error getting week schedules:", err)
      return []
    }
  }

  const formatTime = (time) => {
    try {
      return new Date(`2000-01-01T${time}`).toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
      })
    } catch (err) {
      return time
    }
  }

  const getDayName = (date) => {
    try {
      return new Date(date).toLocaleDateString("en-US", { weekday: "long" })
    } catch (err) {
      return "Invalid Date"
    }
  }

  const getDateDisplay = (date) => {
    try {
      return new Date(date).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
      })
    } catch (err) {
      return "Invalid Date"
    }
  }

  const navigateWeek = (direction) => {
    const newDate = new Date(currentDate)
    newDate.setDate(currentDate.getDate() + direction * 7)
    setCurrentDate(newDate)
  }

  const navigateDay = (direction) => {
    const newDate = new Date(selectedDate)
    newDate.setDate(newDate.getDate() + direction)
    setSelectedDate(newDate.toISOString().split("T")[0])
  }

  // Loading state
  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        <span className="ml-3 text-gray-600">Loading schedules...</span>
      </div>
    )
  }

  // Error state
  if (error) {
    return (
      <div className="text-center py-12">
        <ExclamationTriangleIcon className="h-12 w-12 mx-auto text-red-500 mb-4" />
        <h3 className="text-lg font-medium text-gray-900 mb-2">Error Loading Schedules</h3>
        <p className="text-red-500 mb-4">{error}</p>
        <button
          onClick={fetchSchedules}
          className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
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
          <h1 className="text-2xl font-bold text-gray-900">Schedule Management</h1>
          <p className="text-gray-600">View and manage class schedules</p>
        </div>

        {/* View Mode Toggle */}
        <div className="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
          {[
            { mode: "agenda", icon: TableCellsIcon, label: "Agenda" },
            { mode: "week", icon: ViewColumnsIcon, label: "Week" },
            { mode: "day", icon: CalendarIcon, label: "Day" },
            { mode: "list", icon: ListBulletIcon, label: "List" },
          ].map(({ mode, icon: Icon, label }) => (
            <button
              key={mode}
              onClick={() => setViewMode(mode)}
              className={`px-3 py-2 rounded-md text-sm font-medium transition-colors ${
                viewMode === mode ? "bg-white text-blue-600 shadow-sm" : "text-gray-600 hover:text-gray-900"
              }`}
            >
              <Icon className="h-4 w-4 inline mr-1" />
              {label}
            </button>
          ))}
        </div>
      </div>

      {/* Agenda View */}
      {viewMode === "agenda" && (
        <>
          {Array.isArray(schedules) && schedules.length > 0 ? (
            <ScheduleAgendaTable schedules={schedules} />
          ) : (
            <div className="text-center py-12 bg-white rounded-lg shadow">
              <CalendarIcon className="h-12 w-12 mx-auto text-gray-300 mb-4" />
              <p className="text-gray-500 text-lg font-medium">No schedules found</p>
              <p className="text-gray-400 text-sm">Add some schedules to see them here</p>
            </div>
          )}
        </>
      )}

      {/* Week View */}
      {viewMode === "week" && (
        <div className="bg-white rounded-lg shadow">
          <div className="flex items-center justify-between p-4 border-b border-gray-200">
            <button onClick={() => navigateWeek(-1)} className="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <ChevronLeftIcon className="h-5 w-5" />
            </button>
            <h2 className="text-lg font-semibold text-gray-900">
              Week of {getDateDisplay(getWeekDates(currentDate)[0])} - {getDateDisplay(getWeekDates(currentDate)[6])}
            </h2>
            <button onClick={() => navigateWeek(1)} className="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <ChevronRightIcon className="h-5 w-5" />
            </button>
          </div>
          <div className="grid grid-cols-7 gap-px bg-gray-200">
            {getSchedulesForWeek().map(({ date, schedules: daySchedules }) => (
              <div key={date} className="bg-white min-h-[300px] p-3">
                <div className="text-center mb-3">
                  <div className="text-sm font-medium text-gray-900">{getDayName(date)}</div>
                  <div className="text-xs text-gray-500">{getDateDisplay(date)}</div>
                </div>
                <div className="space-y-2">
                  {Array.isArray(daySchedules) && daySchedules.length > 0 ? (
                    daySchedules.map((schedule) => (
                      <div
                        key={`${date}-${schedule.id}`}
                        className="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-2 rounded-lg shadow-sm"
                      >
                        <div className="text-sm font-semibold truncate">{schedule.course_name}</div>
                        <div className="text-xs mt-1 flex items-center">
                          <ClockIcon className="h-3 w-3 mr-1" />
                          {formatTime(schedule.start_time)}
                        </div>
                      </div>
                    ))
                  ) : (
                    <div className="text-center py-4">
                      <p className="text-xs text-gray-400">No classes</p>
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Day View */}
      {viewMode === "day" && (
        <div className="bg-white rounded-lg shadow">
          <div className="flex items-center justify-between p-4 border-b border-gray-200">
            <button onClick={() => navigateDay(-1)} className="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <ChevronLeftIcon className="h-5 w-5" />
            </button>
            <h2 className="text-lg font-semibold text-gray-900">
              {new Date(selectedDate).toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
              })}
            </h2>
            <button onClick={() => navigateDay(1)} className="p-2 hover:bg-gray-100 rounded-lg transition-colors">
              <ChevronRightIcon className="h-5 w-5" />
            </button>
          </div>

          <div className="p-6">
            <div className="space-y-4">
              {getSchedulesForDate(selectedDate).length > 0 ? (
                getSchedulesForDate(selectedDate)
                  .sort((a, b) => a.start_time.localeCompare(b.start_time))
                  .map((schedule) => (
                    <div
                      key={`day-${schedule.id}`}
                      className="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border-l-4 border-blue-500 rounded-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                    >
                      <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center space-x-3">
                          <div className="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <AcademicCapIcon className="h-6 w-6 text-white" />
                          </div>
                          <div>
                            <h3 className="text-xl font-bold text-gray-900">{schedule.course_name}</h3>
                            <p className="text-sm text-gray-600">{schedule.teacher_name}</p>
                          </div>
                        </div>
                        <div className="text-right">
                          <div className="text-2xl font-bold text-blue-600">{formatTime(schedule.start_time)}</div>
                          <div className="text-sm text-gray-500">to {formatTime(schedule.end_time)}</div>
                        </div>
                      </div>

                      <div className="grid grid-cols-2 gap-6">
                        <div className="flex items-center space-x-3 bg-white bg-opacity-60 rounded-lg p-3">
                          <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <BuildingOfficeIcon className="h-4 w-4 text-green-600" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Room</p>
                            <p className="text-lg font-bold text-green-600">{schedule.room_number}</p>
                          </div>
                        </div>

                        {schedule.teacher_id && (
                          <div className="flex items-center space-x-3 bg-white bg-opacity-60 rounded-lg p-3">
                            <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                              <UserIcon className="h-4 w-4 text-purple-600" />
                            </div>
                            <div>
                              <p className="text-sm font-medium text-gray-900">Teacher</p>
                              <p className="text-lg font-bold text-purple-600">ID: {schedule.teacher_id}</p>
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                  ))
              ) : (
                <div className="text-center py-16">
                  <div className="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <CalendarIcon className="h-10 w-10 text-gray-300" />
                  </div>
                  <h3 className="text-lg font-medium text-gray-900 mb-2">No Classes Today</h3>
                  <p className="text-gray-500">Enjoy your free day!</p>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* List View */}
      {viewMode === "list" && (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Course & Time
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Location
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Teacher
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {Array.isArray(schedules) &&
                  schedules
                    .sort((a, b) => new Date(a.date) - new Date(b.date) || a.start_time.localeCompare(b.start_time))
                    .map((schedule) => (
                      <tr key={`list-${schedule.id}`} className="hover:bg-gray-50">
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                              <AcademicCapIcon className="h-5 w-5 text-blue-600" />
                            </div>
                            <div className="ml-4">
                              <div className="text-sm font-medium text-gray-900">{schedule.course_name}</div>
                              <div className="text-sm text-gray-500 flex items-center">
                                <ClockIcon className="h-4 w-4 mr-1" />
                                {formatTime(schedule.start_time)} - {formatTime(schedule.end_time)}
                              </div>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="text-sm text-gray-900">
                            {new Date(schedule.date).toLocaleDateString("en-US", {
                              weekday: "short",
                              month: "short",
                              day: "numeric",
                            })}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center text-sm text-gray-900">
                            <BuildingOfficeIcon className="h-4 w-4 mr-2 text-gray-400" />
                            Room {schedule.room_number}
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center text-sm text-gray-900">
                            <UserIcon className="h-4 w-4 mr-2 text-gray-400" />
                            {schedule.teacher_name}
                            {schedule.teacher_id && (
                              <span className="text-gray-500 ml-1">(ID: {schedule.teacher_id})</span>
                            )}
                          </div>
                        </td>
                      </tr>
                    ))}
              </tbody>
            </table>
          </div>

          {(!Array.isArray(schedules) || schedules.length === 0) && (
            <div className="text-center py-12">
              <CalendarIcon className="h-12 w-12 mx-auto text-gray-300 mb-4" />
              <p className="text-gray-500 text-lg font-medium">No schedules found</p>
              <p className="text-gray-400 text-sm">Schedules will appear here once created</p>
            </div>
          )}
        </div>
      )}
    </div>
  )
}

export default ScheduleList
