"use client"

import { useState, useEffect } from "react"
import { ClockIcon, UserIcon, BuildingOfficeIcon, ChevronLeftIcon, ChevronRightIcon } from "@heroicons/react/24/outline"

const ScheduleAgendaTable = ({ schedules }) => {
  // Add validation at the start of the component
  if (!Array.isArray(schedules)) {
    console.error('Invalid schedules prop:', schedules);
    return (
      <div className="text-center py-12 bg-white rounded-lg shadow">
        <p className="text-red-500">Error: Invalid schedule data</p>
      </div>
    );
  }

  // Log the schedules for debugging
  useEffect(() => {
    console.log('Schedules in AgendaTable:', schedules);
  }, [schedules]);

  const [currentWeek, setCurrentWeek] = useState(new Date())

  // Generate time slots from 8 AM to 6 PM
  const generateTimeSlots = () => {
    const slots = [];
    for (let hour = 8; hour <= 18; hour++) {
      // Format hours to match database format (HH:mm:ss)
      const formattedHour = hour.toString().padStart(2, '0');
      slots.push(`${formattedHour}:00`);
      if (hour < 18) {
        slots.push(`${formattedHour}:30`);
      }
    }
    return slots;
  }

  const timeSlots = generateTimeSlots()

  // Get week dates starting from Monday
  const getWeekDates = (date) => {
    const week = []
    const startDate = new Date(date)
    startDate.setHours(0, 0, 0, 0)

    // Adjust to Monday
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

  const weekDates = getWeekDates(currentWeek)

  // Get schedules for a specific date and time slot
  const getScheduleForSlot = (date, timeSlot) => {
    if (!Array.isArray(schedules)) return null;
    
    return schedules.find((schedule) => {
      try {
        // Convert schedule date to match the expected format
        const scheduleDate = new Date(schedule.date).toISOString().split('T')[0];
        if (scheduleDate !== date) return false;

        // Parse the time slots
        const slotTime = timeSlot.split(':');
        const slotHour = parseInt(slotTime[0]);
        const slotMinute = parseInt(slotTime[1]);

        // Parse schedule times
        const startParts = schedule.start_time.split(':');
        const endParts = schedule.end_time.split(':');
        const startHour = parseInt(startParts[0]);
        const startMinute = parseInt(startParts[1]);
        const endHour = parseInt(endParts[0]);
        const endMinute = parseInt(endParts[1]);

        // Convert all times to minutes for easier comparison
        const slotTimeInMinutes = slotHour * 60 + slotMinute;
        const startTimeInMinutes = startHour * 60 + startMinute;
        const endTimeInMinutes = endHour * 60 + endMinute;

        // Check if the time slot falls within the schedule time
        return slotTimeInMinutes >= startTimeInMinutes && 
               slotTimeInMinutes < endTimeInMinutes;
      } catch (err) {
        console.error('Error processing schedule:', err);
        return false;
      }
    });
  }

  // Check if a schedule spans multiple time slots
  const getScheduleSpan = (schedule, timeSlot) => {
    if (!schedule) return 0

    const startTime = schedule.start_time.substring(0, 5)
    const endTime = schedule.end_time.substring(0, 5)

    // Calculate how many 30-minute slots this schedule spans
    const startIndex = timeSlots.indexOf(startTime)
    const endIndex = timeSlots.findIndex((slot) => slot >= endTime)

    if (startIndex === -1) return 0
    if (timeSlot === startTime) {
      return endIndex - startIndex
    }
    return 0 // Don't render if it's not the starting slot
  }

  const formatTime = (time) => {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString("en-US", {
      hour: "numeric",
      minute: "2-digit",
      hour12: true,
    })
  }

  const getDayName = (date) => {
    return new Date(date).toLocaleDateString("en-US", { weekday: "short" })
  }

  const getDateDisplay = (date) => {
    return new Date(date).toLocaleDateString("en-US", {
      month: "short",
      day: "numeric",
    })
  }

  const navigateWeek = (direction) => {
    const newDate = new Date(currentWeek)
    newDate.setDate(currentWeek.getDate() + direction * 7)
    setCurrentWeek(newDate)
  }

  const getScheduleColor = (index) => {
    const colors = [
      "from-blue-500 to-blue-600",
      "from-green-500 to-green-600",
      "from-purple-500 to-purple-600",
      "from-red-500 to-red-600",
      "from-yellow-500 to-yellow-600",
      "from-indigo-500 to-indigo-600",
      "from-pink-500 to-pink-600",
      "from-teal-500 to-teal-600",
    ]
    return colors[index % colors.length]
  }

  return (
    <div className="bg-white rounded-lg shadow-lg overflow-hidden">
      {/* Week Navigation */}
      <div className="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
        <button onClick={() => navigateWeek(-1)} className="p-2 hover:bg-gray-200 rounded-lg transition-colors">
          <ChevronLeftIcon className="h-5 w-5" />
        </button>
        <h2 className="text-lg font-semibold text-gray-900">
          Week of {getDateDisplay(weekDates[0])} - {getDateDisplay(weekDates[6])}
        </h2>
        <button onClick={() => navigateWeek(1)} className="p-2 hover:bg-gray-200 rounded-lg transition-colors">
          <ChevronRightIcon className="h-5 w-5" />
        </button>
      </div>

      {/* Agenda Table */}
      <div className="overflow-x-auto">
        <table className="min-w-full">
          {/* Header */}
          <thead className="bg-gray-100">
            <tr>
              <th className="sticky left-0 z-10 bg-gray-100 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">
                Time
              </th>
              {weekDates.map((date) => (
                <th
                  key={date}
                  className="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px]"
                >
                  <div className="flex flex-col items-center">
                    <span className="font-semibold text-gray-700">{getDayName(date)}</span>
                    <span className="text-gray-500">{getDateDisplay(date)}</span>
                  </div>
                </th>
              ))}
            </tr>
          </thead>

          {/* Body */}
          <tbody className="bg-white divide-y divide-gray-100">
            {timeSlots.map((timeSlot, timeIndex) => (
              <tr key={timeSlot} className="hover:bg-gray-50">
                {/* Time Column */}
                <td className="sticky left-0 z-10 bg-white px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-200">
                  <div className="flex items-center">
                    <ClockIcon className="h-4 w-4 mr-2 text-gray-400" />
                    {formatTime(timeSlot)}
                  </div>
                </td>

                {/* Date Columns */}
                {weekDates.map((date, dateIndex) => {
                  const schedule = getScheduleForSlot(date, timeSlot)
                  const span = getScheduleSpan(schedule, timeSlot)

                  // Skip rendering if this cell is part of a spanned schedule
                  if (schedule && span === 0) {
                    return null
                  }

                  return (
                    <td
                      key={`${date}-${timeSlot}`}
                      className="px-2 py-1 text-sm relative"
                      style={{ height: "60px" }}
                      rowSpan={span > 0 ? span : 1}
                    >
                      {schedule ? (
                        <div
                          className={`
                            bg-gradient-to-r ${getScheduleColor(dateIndex)} 
                            text-white p-3 rounded-lg shadow-md 
                            hover:shadow-lg transform hover:scale-105 
                            transition-all duration-200 cursor-pointer
                            h-full flex flex-col justify-center
                            border-l-4 border-white border-opacity-30
                          `}
                          style={{
                            minHeight: `${span * 60}px`,
                          }}
                        >
                          <div className="font-bold text-sm mb-1 truncate">{schedule.course_name}</div>

                          <div className="text-xs opacity-90 flex items-center mb-1">
                            <ClockIcon className="h-3 w-3 mr-1" />
                            {formatTime(schedule.start_time)} - {formatTime(schedule.end_time)}
                          </div>

                          <div className="text-xs opacity-90 flex items-center mb-1">
                            <BuildingOfficeIcon className="h-3 w-3 mr-1" />
                            Room {schedule.room_number}
                          </div>

                          <div className="text-xs opacity-90 flex items-center truncate">
                            <UserIcon className="h-3 w-3 mr-1" />
                            {schedule.teacher_name}
                          </div>
                        </div>
                      ) : (
                        <div className="h-full flex items-center justify-center">
                          <div className="w-2 h-2 bg-gray-100 rounded-full"></div>
                        </div>
                      )}
                    </td>
                  )
                })}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Legend */}
      <div className="p-4 bg-gray-50 border-t border-gray-200">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4 text-sm text-gray-600">
            <div className="flex items-center">
              <div className="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded mr-2"></div>
              <span>Classes are highlighted in time slots</span>
            </div>
            <div className="flex items-center">
              <div className="w-3 h-3 bg-gray-100 rounded mr-2"></div>
              <span>Free time</span>
            </div>
          </div>
          <div className="text-sm text-gray-500">Total schedules: {schedules.length}</div>
        </div>
      </div>
    </div>
  )
}

export default ScheduleAgendaTable
