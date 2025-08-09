import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"

export function Academic({ data }) {
  const { schedule = [], grades = [], teachers = [] } = data
  
  console.log("Academic component - schedule data:", schedule)
  console.log("Academic component - grades data:", grades)
  
  // Group schedule by day
  const scheduleByDay = schedule.reduce((acc, item) => {
    const day = item.day_name || "Monday"
    if (!acc[day]) acc[day] = []
    acc[day].push(item)
    return acc
  }, {})
  
  const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
  
  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Weekly Schedule</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-6">
              {days.map(day => {
                const daySchedule = scheduleByDay[day] || []
                if (daySchedule.length === 0) return null
                
                return (
                  <div key={day} className="space-y-3">
                    <h3 className="font-medium text-gray-900">{day}</h3>
                    {daySchedule.map((item, i) => (
                      <div key={`${day}-${i}`} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div className="text-sm font-medium text-blue-600 w-24">
                          {item.start_time} - {item.end_time}
                        </div>
                        <div className="flex-1">
                          <p className="text-sm font-medium text-gray-900">
                            {item.course_name || item.subject_name}
                          </p>
                          <p className="text-xs text-gray-500">
                            Room {item.room_number} • {item.teacher_name}
                          </p>
                          {item.date && (
                            <p className="text-xs text-gray-400">
                              {new Date(item.date).toLocaleDateString()}
                            </p>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                )
              })}
              
              {Object.keys(scheduleByDay).length === 0 && (
                <div className="text-center py-8">
                  <p className="text-gray-500">No schedule available</p>
                  <p className="text-xs text-gray-400 mt-2">
                    Schedule data: {JSON.stringify(schedule, null, 2)}
                  </p>
                </div>
              )}
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader>
            <CardTitle>My Teachers</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              {teachers && teachers.length > 0 ? (
                teachers.map((teacher, i) => (
                  <div key={i} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <span className="text-blue-600 font-medium">
                        {teacher.first_name?.charAt(0)}{teacher.last_name?.charAt(0)}
                      </span>
                    </div>
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-900">
                        {teacher.teacher_name || `${teacher.first_name} ${teacher.last_name}`}
                      </p>
                      <p className="text-xs text-gray-500">{teacher.subject_name}</p>
                    </div>
                    <Badge variant="outline">{teacher.specialty}</Badge>
                  </div>
                ))
              ) : (
                <p className="text-gray-500 text-center py-4">No teacher information available</p>
              )}
            </div>
          </CardContent>
        </Card>
      </div>
      
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <CardTitle>Recent Grades</CardTitle>
          <Badge variant="secondary" className="text-xs">
            {grades.length} Total
          </Badge>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {grades && grades.length > 0 ? (
              grades.map((grade, i) => (
                <div key={i} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                  <div className={`w-3 h-3 rounded-full ${
                    Number(grade.note) >= 16 ? "bg-green-500" : 
                    Number(grade.note) >= 12 ? "bg-yellow-500" : 
                    "bg-red-500"
                  }`} />
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{grade.subject_name}</p>
                    <p className="text-xs text-gray-500">
                      {grade.evaluation_type} • {grade.teacher_name}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-bold text-gray-900">{grade.note}/20</p>
                    <p className="text-xs text-gray-500">
                      {new Date(grade.date).toLocaleDateString()}
                    </p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-500 text-center py-4">No grades available</p>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
