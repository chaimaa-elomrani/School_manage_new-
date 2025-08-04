import { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import api from '../../services/api';
import { 
  BookOpenIcon, 
  ClockIcon, 
  UserGroupIcon, 
  AcademicCapIcon,
  DocumentTextIcon 
} from '@heroicons/react/24/outline';

const StudentDashboard = () => {
  const { user } = useAuth();
  const [stats, setStats] = useState({
    myTeachers: 0,
    myClassmates: 0,
    coursesCount: 0,
    evaluationsCount: 0
  });
  const [schedules, setSchedules] = useState([]);
  const [teachers, setTeachers] = useState([]);
  const [classmates, setClassmates] = useState([]);
  const [evaluations, setEvaluations] = useState([]);
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
      
      // Get student's class first
      const studentRes = await api.get(`/getStudentData/${user.id}`);
      const classId = studentRes.data?.class_id;

      if (!classId) {
        throw new Error('Student class not found');
      }

      // Fetch all related data
      const [
        scheduleRes,
        teachersRes,
        classmatesRes,
        evaluationsRes,
        gradesRes
      ] = await Promise.all([
        api.get(`/getStudentSchedule/${user.id}`),
        api.get(`/getClassTeachers/${classId}`),
        api.get(`/getClassmates/${classId}`),
        api.get(`/getStudentEvaluations/${user.id}`),
        api.get(`/getStudentGrades/${user.id}`)
      ]);

      // Process the data
      const scheduleData = scheduleRes.data?.data || [];
      const teacherData = teachersRes.data?.data || [];
      const classmateData = classmatesRes.data?.data || [];
      const evaluationData = evaluationsRes.data?.data || [];
      const gradeData = gradesRes.data?.data || [];

      // Set states
      setSchedules(scheduleData);
      setTeachers(teacherData);
      setClassmates(classmateData.filter(c => c.id !== user.id));
      setEvaluations(evaluationData);
      setGrades(gradeData);

      // Update stats
      setStats({
        myTeachers: teacherData.length,
        myClassmates: classmateData.length - 1,
        coursesCount: new Set(scheduleData.map(s => s.course_id)).size,
        evaluationsCount: evaluationData.length
      });

    } catch (err) {
      console.error('Error fetching student data:', err);
      setError(err.message || 'Failed to fetch student data');
    } finally {
      setLoading(false);
    }
  };

  // Render methods
  const renderTeachersList = () => (
    <div className="bg-white rounded-lg shadow p-6">
      <h3 className="text-lg font-semibold text-gray-900 mb-4">My Teachers</h3>
      <div className="space-y-3">
        {teachers.map(teacher => (
          <div key={teacher.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
            <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <span className="text-blue-600 font-medium">
                {teacher.nom?.charAt(0)}{teacher.prenom?.charAt(0)}
              </span>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-900">{`${teacher.nom} ${teacher.prenom}`}</p>
              <p className="text-xs text-gray-500">{teacher.specialite}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );

  const renderSchedule = () => (
    <div className="bg-white rounded-lg shadow p-6">
      <h3 className="text-lg font-semibold text-gray-900 mb-4">My Schedule</h3>
      <div className="space-y-3">
        {schedules.map(schedule => (
          <div key={schedule.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
            <div className="text-sm font-medium text-blue-600">
              {schedule.start_time} - {schedule.end_time}
            </div>
            <div className="flex-1">
              <p className="text-sm font-medium text-gray-900">{schedule.course_name}</p>
              <p className="text-xs text-gray-500">Room {schedule.room_number}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );

  const renderGrades = () => (
    <div className="bg-white rounded-lg shadow p-6">
      <h3 className="text-lg font-semibold text-gray-900 mb-4">Recent Grades</h3>
      <div className="space-y-3">
        {grades.map(grade => (
          <div key={grade.id} className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
            <div className={`w-2 h-full rounded-full ${
              grade.note >= 16 ? 'bg-green-500' : 
              grade.note >= 12 ? 'bg-yellow-500' : 
              'bg-red-500'
            }`} />
            <div className="flex-1">
              <p className="text-sm font-medium text-gray-900">{grade.course_name}</p>
              <p className="text-xs text-gray-500">Grade: {grade.note}/20</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500" />
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

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Student Dashboard</h1>
        <p className="text-gray-600">Welcome back, {user?.nom}!</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {[
          {
            title: 'My Teachers',
            value: stats.myTeachers,
            icon: AcademicCapIcon,
            color: 'bg-blue-500'
          },
          {
            title: 'Classmates',
            value: stats.myClassmates,
            icon: UserGroupIcon,
            color: 'bg-green-500'
          },
          {
            title: 'My Courses',
            value: stats.coursesCount,
            icon: BookOpenIcon,
            color: 'bg-purple-500'
          },
          {
            title: 'Evaluations',
            value: stats.evaluationsCount,
            icon: DocumentTextIcon,
            color: 'bg-yellow-500'
          }
        ].map((stat, index) => {
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

      {/* Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {renderTeachersList()}
        {renderSchedule()}
      </div>

      {/* Grades Section */}
      <div className="mt-6">
        {renderGrades()}
      </div>
    </div>
  );
};

export default StudentDashboard;


