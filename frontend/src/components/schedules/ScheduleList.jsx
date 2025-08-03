import { useState, useEffect } from 'react';
import api from '../../services/api';
import { 
  CalendarIcon,
  ClockIcon,
  AcademicCapIcon,
  UserIcon,
  BuildingOfficeIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  ViewColumnsIcon,
  ListBulletIcon
} from '@heroicons/react/24/outline';

const ScheduleList = () => {
  const [schedules, setSchedules] = useState([]);
  const [loading, setLoading] = useState(true);
  const [viewMode, setViewMode] = useState('week'); // 'week', 'day', 'list'
  const [currentDate, setCurrentDate] = useState(new Date());
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);

  useEffect(() => {
    fetchSchedules();
  }, []);

  useEffect(() => {
    console.log('Current schedules state:', schedules);
    console.log('Current date:', currentDate);
    console.log('Selected date:', selectedDate);
}, [schedules, currentDate, selectedDate]);

  const fetchSchedules = async () => {
    try {
        setLoading(true);
        const response = await api.get('/showSchedules');
        console.log('Schedule response:', response.data);

        if (response.data?.data) {
            const formattedSchedules = response.data.data.map(schedule => ({
                id: schedule.id,
                date: new Date(schedule.date).toISOString().split('T')[0],
                start_time: schedule.start_time,
                end_time: schedule.end_time,
                course_name: schedule.course_name || 'Unnamed Course',
                teacher_name: schedule.teacher_name || 'Unassigned',
                teacher_id: schedule.teacher_id,
                room_number: schedule.room_number || schedule.room_id,
                course_id: schedule.course_id
            }));
            setSchedules(formattedSchedules);
        }
    } catch (error) {
        console.error('Failed to fetch schedules:', error);
    } finally {
        setLoading(false);
    }
  };

  const getWeekDates = (date) => {
    const week = [];
    const startDate = new Date(date);
    startDate.setHours(0, 0, 0, 0);
    
    // Adjust to Monday if necessary
    const day = startDate.getDay();
    const diff = startDate.getDate() - day + (day === 0 ? -6 : 1);
    startDate.setDate(diff);

    for (let i = 0; i < 7; i++) {
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);
        week.push(currentDate.toISOString().split('T')[0]);
    }
    
    console.log('Week dates generated:', week);
    return week;
  };

  const isValidSchedule = (schedule) => {
    return schedule 
        && schedule.date 
        && schedule.start_time 
        && schedule.end_time 
        && schedule.course_name;
};

  const getSchedulesForDate = (date) => {
    if (!Array.isArray(schedules)) return [];
    
    // Convert date to YYYY-MM-DD format for comparison
    const formattedDate = new Date(date).toISOString().split('T')[0];
    
    return schedules.filter(schedule => {
        // Add debug logging
        console.log('Comparing dates:', {
            scheduleDate: schedule.date,
            formattedDate: formattedDate,
            match: schedule.date === formattedDate
        });
        
        return schedule.date === formattedDate;
    });
};

  const getSchedulesForWeek = () => {
    const weekDates = getWeekDates(currentDate);
    return weekDates.map(date => ({
      date,
      schedules: getSchedulesForDate(date)
    }));
  };

  const formatTime = (time) => {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  };

  const getDayName = (date) => {
    return new Date(date).toLocaleDateString('en-US', { weekday: 'long' });
  };

  const getDateDisplay = (date) => {
    return new Date(date).toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric' 
    });
  };

  const navigateWeek = (direction) => {
    const newDate = new Date(currentDate);
    newDate.setDate(currentDate.getDate() + (direction * 7));
    setCurrentDate(newDate);
  };

  const navigateDay = (direction) => {
    const newDate = new Date(selectedDate);
    newDate.setDate(newDate.getDate() + direction);
    setSelectedDate(newDate.toISOString().split('T')[0]);
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    );
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
          <button
            onClick={() => setViewMode('week')}
            className={`px-3 py-2 rounded-md text-sm font-medium transition-colors ${
              viewMode === 'week' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            <ViewColumnsIcon className="h-4 w-4 inline mr-1" />
            Week
          </button>
          <button
            onClick={() => setViewMode('day')}
            className={`px-3 py-2 rounded-md text-sm font-medium transition-colors ${
              viewMode === 'day' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            <CalendarIcon className="h-4 w-4 inline mr-1" />
            Day
          </button>
          <button
            onClick={() => setViewMode('list')}
            className={`px-3 py-2 rounded-md text-sm font-medium transition-colors ${
              viewMode === 'list' 
                ? 'bg-white text-blue-600 shadow-sm' 
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            <ListBulletIcon className="h-4 w-4 inline mr-1" />
            List
          </button>
        </div>
      </div>

      {/* Week View */}
      {viewMode === 'week' && (
        <div className="bg-white rounded-lg shadow">
          {/* Week Navigation */}
          <div className="flex items-center justify-between p-4 border-b border-gray-200">
            <button
              onClick={() => navigateWeek(-1)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ChevronLeftIcon className="h-5 w-5" />
            </button>
            <h2 className="text-lg font-semibold text-gray-900">
              Week of {getDateDisplay(getWeekDates(currentDate)[0])} - {getDateDisplay(getWeekDates(currentDate)[6])}
            </h2>
            <button
              onClick={() => navigateWeek(1)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ChevronRightIcon className="h-5 w-5" />
            </button>
          </div>

          {/* Week Grid */}
          <div className="grid grid-cols-7 gap-px bg-gray-200">
            {getSchedulesForWeek().map(({ date, schedules: daySchedules }) => (
              <div key={date} className="bg-white min-h-[300px] p-3">
                <div className="text-center mb-3">
                  <div className="text-sm font-medium text-gray-900">{getDayName(date)}</div>
                  <div className="text-xs text-gray-500">{getDateDisplay(date)}</div>
                </div>
                <div className="space-y-2">
                  {daySchedules.map((schedule) => (
                    <div
                      key={schedule.id}
                      className="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200 cursor-pointer border-l-4 border-blue-300"
                    >
                      <div className="text-sm font-bold mb-2 truncate">
                        {schedule.course_name}
                      </div>
                      <div className="text-xs flex items-center mb-1 opacity-90">
                        <ClockIcon className="h-3 w-3 mr-1" />
                        {formatTime(schedule.start_time)} - {formatTime(schedule.end_time)}
                      </div>
                      <div className="text-xs flex items-center opacity-90">
                        <BuildingOfficeIcon className="h-3 w-3 mr-1" />
                        Room {schedule.room_number}
                      </div>
                      <div className="text-xs flex items-center mt-1 opacity-90">
                        <UserIcon className="h-3 w-3 mr-1" />
                        {schedule.teacher_name}
                      </div>
                    </div>
                  ))}
                  {daySchedules.length === 0 && (
                    <div className="text-center py-8">
                      <div className="w-8 h-8 mx-auto mb-2 rounded-full bg-gray-100 flex items-center justify-center">
                        <CalendarIcon className="h-4 w-4 text-gray-400" />
                      </div>
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
      {viewMode === 'day' && (
        <div className="bg-white rounded-lg shadow">
          {/* Day Navigation */}
          <div className="flex items-center justify-between p-4 border-b border-gray-200">
            <button
              onClick={() => navigateDay(-1)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ChevronLeftIcon className="h-5 w-5" />
            </button>
            <h2 className="text-lg font-semibold text-gray-900">
              {new Date(selectedDate).toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
              })}
            </h2>
            <button
              onClick={() => navigateDay(1)}
              className="p-2 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <ChevronRightIcon className="h-5 w-5" />
            </button>
          </div>

          {/* Day Schedule */}
          <div className="p-6">
            <div className="space-y-4">
              {getSchedulesForDate(selectedDate).length > 0 ? (
                getSchedulesForDate(selectedDate)
                  .sort((a, b) => a.start_time.localeCompare(b.start_time))
                  .map((schedule) => (
                    <div
                      key={schedule.id}
                      className="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border-l-4 border-blue-500 rounded-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                    >
                      <div className="flex items-center justify-between mb-4">
                        <div className="flex items-center space-x-3">
                          <div className="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <AcademicCapIcon className="h-6 w-6 text-white" />
                          </div>
                          <div>
                            <h3 className="text-xl font-bold text-gray-900">
                              {schedule.course_name}
                            </h3>
                            <p className="text-sm text-gray-600">
                              {schedule.teacher_name}
                            </p>
                          </div>
                        </div>
                        <div className="text-right">
                          <div className="text-2xl font-bold text-blue-600">
                            {formatTime(schedule.start_time)}
                          </div>
                          <div className="text-sm text-gray-500">
                            to {formatTime(schedule.end_time)}
                          </div>
                        </div>
                      </div>
                      
                      <div className="grid grid-cols-2 gap-6">
                        <div className="flex items-center space-x-3 bg-white bg-opacity-60 rounded-lg p-3">
                          <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <BuildingOfficeIcon className="h-4 w-4 text-green-600" />
                          </div>
                          <div>
                            <p className="text-sm font-medium text-gray-900">Room</p>
                            <p className="text-lg font-bold text-green-600">
                              {schedule.room_number || schedule.room_id}
                            </p>
                          </div>
                        </div>
                        
                        {schedule.teacher_id && (
                          <div className="flex items-center space-x-3 bg-white bg-opacity-60 rounded-lg p-3">
                            <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                              <UserIcon className="h-4 w-4 text-purple-600" />
                            </div>
                            <div>
                              <p className="text-sm font-medium text-gray-900">Teacher</p>
                              <p className="text-lg font-bold text-purple-600">
                                ID: {schedule.teacher_id}
                              </p>
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
      {viewMode === 'list' && (
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
                {Array.isArray(schedules) && schedules
                  .sort((a, b) => new Date(a.date) - new Date(b.date) || a.start_time.localeCompare(b.start_time))
                  .map((schedule) => (
                    <tr key={schedule.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center">
                          <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <AcademicCapIcon className="h-5 w-5 text-blue-600" />
                          </div>
                          <div className="ml-4">
                            <div className="text-sm font-medium text-gray-900">
                              {schedule.course_name}
                            </div>
                            <div className="text-sm text-gray-500 flex items-center">
                              <ClockIcon className="h-4 w-4 mr-1" />
                              {formatTime(schedule.start_time)} - {formatTime(schedule.end_time)}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-900">
                          {new Date(schedule.date).toLocaleDateString('en-US', {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric'
                          })}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center text-sm text-gray-900">
                          <BuildingOfficeIcon className="h-4 w-4 mr-2 text-gray-400" />
                          Room {schedule.room_number || schedule.room_id}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="flex items-center text-sm text-gray-900">
                          <UserIcon className="h-4 w-4 mr-2 text-gray-400" />
                          Teacher ID: {schedule.teacher_id}
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
  );
};

export default ScheduleList;






