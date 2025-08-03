import { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import api from '../../services/api';
import { 
  UsersIcon, 
  BookOpenIcon, 
  ClockIcon, 
  CheckCircleIcon 
} from '@heroicons/react/24/outline';

const TeacherDashboard = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState({
    myStudents: 0,
    myCourses: 0,
    todayClasses: 0,
    pendingGrades: 0
  });
  const [todaySchedule, setTodaySchedule] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (user?.id) {
      fetchTeacherData();
    }
  }, [user]);

  const fetchTeacherData = async () => {
    try {
      setLoading(true);
      
      // Fetch teacher's courses and schedules
      const [coursesRes, schedulesRes, gradesRes] = await Promise.all([
        api.get('/showCourses').catch(err => ({ data: [] })),
        api.get('/showSchedules').catch(err => ({ data: [] })),
        api.get('/showGrades').catch(err => ({ data: [] }))
      ]);

      // Extract data arrays properly
      const allCourses = coursesRes.data?.data || coursesRes.data || [];
      const allSchedules = schedulesRes.data?.data || schedulesRes.data || [];
      const allGrades = gradesRes.data?.data || gradesRes.data || [];

      // Filter courses for current teacher
      const teacherCourses = allCourses.filter(course => 
        course.teacher_id === user.id
      );
      
      // Filter schedules for teacher's courses
      const teacherSchedules = allSchedules.filter(schedule => 
        teacherCourses.some(course => course.id === schedule.course_id)
      );

      // Get today's date
      const today = new Date().toISOString().split('T')[0];
      const todayClasses = teacherSchedules.filter(schedule => 
        schedule.date === today
      );

      // Count pending grades (grades without a score)
      const pendingGrades = allGrades.filter(grade => 
        !grade.score && teacherCourses.some(course => course.id === grade.course_id)
      ).length;

      // Estimate student count (you might need to adjust this based on your data)
      const totalStudents = teacherCourses.reduce((total, course) => {
        const courseGrades = allGrades.filter(grade => grade.course_id === course.id);
        const uniqueStudents = [...new Set(courseGrades.map(grade => grade.student_id))];
        return total + uniqueStudents.length;
      }, 0);

      setStats({
        myStudents: totalStudents,
        myCourses: teacherCourses.length,
        todayClasses: todayClasses.length,
        pendingGrades: pendingGrades
      });

      setTodaySchedule(todayClasses);

    } catch (err) {
      setError('Failed to fetch teacher data');
      console.error('Teacher dashboard error:', err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-4">
        <p className="text-red-600">{error}</p>
        <button 
          onClick={fetchTeacherData}
          className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Retry
        </button>
      </div>
    );
  }

  const statCards = [
    {
      title: 'My Students',
      value: stats.myStudents,
      icon: UsersIcon,
      color: 'bg-blue-500'
    },
    {
      title: 'My Courses',
      value: stats.myCourses,
      icon: BookOpenIcon,
      color: 'bg-green-500'
    },
    {
      title: 'Today\'s Classes',
      value: stats.todayClasses,
      icon: ClockIcon,
      color: 'bg-purple-500'
    },
    {
      title: 'Pending Grades',
      value: stats.pendingGrades,
      icon: CheckCircleIcon,
      color: 'bg-orange-500'
    }
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Teacher Dashboard</h1>
        <p className="text-gray-600">Welcome back, {user?.nom}! Manage your classes and track student progress.</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {statCards.map((stat, index) => {
          const Icon = stat.icon;
          return (
            <div key={index} className="bg-white rounded-lg shadow p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600">{stat.title}</p>
                  <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                </div>
                <div className={`${stat.color} p-3 rounded-full`}>
                  <Icon className="h-6 w-6 text-white" />
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Today's Schedule & Recent Activity */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Today's Schedule</h3>
          <div className="space-y-3">
            {todaySchedule.length > 0 ? (
              todaySchedule.map((schedule) => (
                <div key={schedule.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                  <div className="text-sm font-medium text-blue-600">
                    {schedule.start_time} - {schedule.end_time}
                  </div>
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">{schedule.course_name}</p>
                    <p className="text-xs text-gray-500">Room {schedule.room_id}</p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-500 text-center py-4">No classes scheduled for today</p>
            )}
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
          <div className="space-y-3">
            <button className="w-full text-left p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
              <p className="font-medium text-blue-900">Grade Assignments</p>
              <p className="text-sm text-blue-600">Review and grade pending submissions</p>
            </button>
            <button className="w-full text-left p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
              <p className="font-medium text-green-900">View Schedule</p>
              <p className="text-sm text-green-600">Check your complete teaching schedule</p>
            </button>
            <button className="w-full text-left p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
              <p className="font-medium text-purple-900">Student Progress</p>
              <p className="text-sm text-purple-600">Track student performance and attendance</p>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default TeacherDashboard;
