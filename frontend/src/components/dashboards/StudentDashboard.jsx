import { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import api from '../../services/api';
import { 
  BookOpenIcon, 
  ClockIcon, 
  CheckCircleIcon, 
  ExclamationTriangleIcon 
} from '@heroicons/react/24/outline';

const StudentDashboard = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState({
    enrolledCourses: 0,
    upcomingClasses: 0,
    completedAssignments: 0,
    pendingAssignments: 0
  });
  const [todaySchedule, setTodaySchedule] = useState([]);
  const [grades, setGrades] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (user?.id) {
      fetchStudentData();
    }
  }, [user]);

  const fetchStudentData = async () => {
    try {
      setLoading(true);
      
      // Fetch student's data with proper error handling
      const [schedulesRes, gradesRes, coursesRes, enrollmentsRes] = await Promise.all([
        api.get('/showSchedules').catch(err => ({ data: [] })),
        api.get('/showGrades').catch(err => ({ data: [] })),
        api.get('/showCourses').catch(err => ({ data: [] })),
        api.get('/showEnrollments').catch(err => ({ data: [] }))
      ]);

      // Extract data arrays properly
      const schedules = schedulesRes.data?.data || schedulesRes.data || [];
      const allGrades = gradesRes.data?.data || gradesRes.data || [];
      const allCourses = coursesRes.data?.data || coursesRes.data || [];
      const allEnrollments = enrollmentsRes.data?.data || enrollmentsRes.data || [];

      // Filter enrollments for current student
      const studentEnrollments = allEnrollments.filter(enrollment => 
        enrollment.student_id === user.id && enrollment.status === 'active'
      );

      // Get course IDs from student's enrollments
      const studentCourseIds = studentEnrollments.map(enrollment => enrollment.course_id);
      
      // Filter courses for enrolled courses
      const enrolledCourses = allCourses.filter(course => 
        studentCourseIds.includes(course.id)
      );

      // Filter grades for current student
      const studentGrades = allGrades.filter(grade => 
        grade.student_id === user.id
      );

      // Get today's date in YYYY-MM-DD format
      const today = new Date().toISOString().split('T')[0];
      const todayClasses = schedules.filter(schedule => 
        schedule.date === today && studentCourseIds.includes(schedule.course_id)
      );

      // Count completed vs pending assignments
      const completedAssignments = studentGrades.filter(grade => grade.score !== null).length;
      const pendingAssignments = studentGrades.filter(grade => grade.score === null).length;

      setStats({
        enrolledCourses: enrolledCourses.length,
        upcomingClasses: todayClasses.length,
        completedAssignments,
        pendingAssignments
      });

      setTodaySchedule(todayClasses);
      setGrades(studentGrades.slice(0, 5)); // Recent grades

    } catch (err) {
      setError('Failed to fetch student data');
      console.error('Student dashboard error:', err);
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
          onClick={fetchStudentData}
          className="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Retry
        </button>
      </div>
    );
  }

  const statCards = [
    {
      title: 'Enrolled Courses',
      value: stats.enrolledCourses,
      icon: BookOpenIcon,
      color: 'bg-blue-500'
    },
    {
      title: 'Today\'s Classes',
      value: stats.upcomingClasses,
      icon: ClockIcon,
      color: 'bg-green-500'
    },
    {
      title: 'Completed Assignments',
      value: stats.completedAssignments,
      icon: CheckCircleIcon,
      color: 'bg-purple-500'
    },
    {
      title: 'Pending Assignments',
      value: stats.pendingAssignments,
      icon: ExclamationTriangleIcon,
      color: 'bg-orange-500'
    }
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Student Dashboard</h1>
        <p className="text-gray-600">Welcome back, {user?.nom}! Track your academic progress.</p>
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

      {/* Schedule & Grades */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Today's Classes</h3>
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
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Recent Grades</h3>
          <div className="space-y-3">
            {grades.length > 0 ? (
              grades.map((grade) => (
                <div key={grade.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                  <div className={`w-3 h-3 rounded-full ${
                    grade.note >= 16 ? 'bg-green-500' : 
                    grade.note >= 12 ? 'bg-yellow-500' : 
                    grade.note ? 'bg-red-500' : 'bg-gray-400'
                  }`}></div>
                  <div className="flex-1">
                    <p className="text-sm font-medium text-gray-900">
                      {grade.course_name || `Course ${grade.course_id}`}
                    </p>
                    <p className="text-xs text-gray-500">
                      Grade: {grade.note || 'Pending'} / 20
                    </p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-500 text-center py-4">No grades available</p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default StudentDashboard;


