import { useAuth } from '../../contexts/AuthContext';
import { useLocation } from 'react-router-dom';
import Sidebar from './Sidebar';

const Layout = ({ children }) => {
  const { user, logout } = useAuth();
  const location = useLocation();

  const handleLogout = async () => {
    await logout();
  };

  return (
    <div className="min-h-screen bg-gray-50 flex">
      <Sidebar currentPage={location.pathname} />
      
      <div className="flex-1 flex flex-col">
        {/* Top Navigation */}
        <nav className="bg-white shadow border-b border-gray-200">
          <div className="px-6 py-4">
            <div className="flex justify-between items-center">
              <div>
                <h1 className="text-lg font-semibold text-gray-900">School Management System</h1>
              </div>
              <div className="flex items-center space-x-4">
                <span className="text-gray-700">
                  Welcome, {user?.first_name} {user?.last_name}
                </span>
                <button
                  onClick={handleLogout}
                  className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
                >
                  Logout
                </button>
              </div>
            </div>
          </div>
        </nav>

        {/* Main Content */}
        <main className="flex-1 p-6">
          {children}
        </main>
      </div>
    </div>
  );
};

export default Layout;