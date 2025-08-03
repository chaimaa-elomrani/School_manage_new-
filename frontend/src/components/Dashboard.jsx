import { useAuth } from '../contexts/AuthContext';
import AdminDashboard from './dashboards/AdminDashboard';
import TeacherDashboard from './dashboards/TeacherDashboard';
import StudentDashboard from './dashboards/StudentDashboard';

const Dashboard = () => {
  const { user, loading } = useAuth();

  console.log('Dashboard user:', user); // Debug log

  if (loading) {
    return <div>Loading...</div>;
  }

  const renderDashboard = () => {
    console.log('User role:', user?.role); // Debug log
    
    switch (user?.role) {
      case 'admin':
        return <AdminDashboard />;
      case 'teacher':
        return <TeacherDashboard />;
      case 'student':
        return <StudentDashboard />;
      case 'parent':
        return <div className="text-center py-12">Parent Dashboard Coming Soon...</div>;
      default:
        return <div className="text-center py-12">No dashboard available for your role</div>;
    }
  };

  return (
    <div>
      {renderDashboard()}
    </div>
  );
};

export default Dashboard;





