import { useAuth } from '../../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';
import { useState } from 'react';
import {
  HomeIcon,
  UsersIcon,
  AcademicCapIcon,
  BookOpenIcon,
  CalendarIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  ChatBubbleLeftRightIcon,
  CogIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
} from '@heroicons/react/24/outline';

const menuItems = {
  admin: [
    { icon: HomeIcon, label: "Dashboard", path: "/dashboard" },
    { icon: UsersIcon, label: "Students", path: "/students" },
    { icon: AcademicCapIcon, label: "Teachers", path: "/teachers" },
    { icon: BookOpenIcon, label: "Courses", path: "/courses" },
    { icon: CalendarIcon, label: "Schedule", path: "/schedule" },
    { icon: DocumentTextIcon, label: "Reports", path: "/reports" },
    { icon: CurrencyDollarIcon, label: "Finance", path: "/finance" },
    { icon: ChatBubbleLeftRightIcon, label: "Communication", path: "/communication" },
    { icon: CogIcon, label: "Settings", path: "/settings" },
  ],
  teacher: [
    { icon: HomeIcon, label: "Dashboard", path: "/dashboard" },
    { icon: UsersIcon, label: "My Students", path: "/students" },
    { icon: BookOpenIcon, label: "My Courses", path: "/courses" },
    { icon: CalendarIcon, label: "Schedule", path: "/schedule" },
    { icon: DocumentTextIcon, label: "Grades", path: "/grades" },
    { icon: ChatBubbleLeftRightIcon, label: "Messages", path: "/messages" },
  ],
  student: [
    { icon: HomeIcon, label: "Dashboard", path: "/dashboard" },
    { icon: BookOpenIcon, label: "My Courses", path: "/courses" },
    { icon: CalendarIcon, label: "Schedule", path: "/schedule" },
    { icon: DocumentTextIcon, label: "Assignments", path: "/assignments" },
    { icon: CurrencyDollarIcon, label: "Payments", path: "/payments" },
  ],
  parent: [
    { icon: HomeIcon, label: "Dashboard", path: "/dashboard" },
    { icon: UsersIcon, label: "My Children", path: "/children" },
    { icon: DocumentTextIcon, label: "Reports", path: "/reports" },
    { icon: CurrencyDollarIcon, label: "Payments", path: "/payments" },
    { icon: ChatBubbleLeftRightIcon, label: "Messages", path: "/messages" },
  ],
};

export default function Sidebar({ currentPage }) {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [isCollapsed, setIsCollapsed] = useState(false);

  const userMenuItems = menuItems[user?.role] || menuItems.admin;

  const handleNavigation = (path) => {
    navigate(path);
  };

  return (
    <div className={`bg-white shadow-lg border-r border-gray-200 transition-all duration-300 ${
      isCollapsed ? 'w-16' : 'w-64'
    }`}>
      {/* Header */}
      <div className="p-4 border-b border-gray-200">
        <div className="flex items-center justify-between">
          {!isCollapsed && (
            <div>
              <h2 className="text-lg font-semibold text-gray-900">Menu</h2>
              <p className="text-sm text-gray-600 capitalize">{user?.role}</p>
            </div>
          )}
          <button
            onClick={() => setIsCollapsed(!isCollapsed)}
            className="p-1 rounded-md hover:bg-gray-100 transition-colors"
          >
            {isCollapsed ? (
              <ChevronRightIcon className="h-5 w-5" />
            ) : (
              <ChevronLeftIcon className="h-5 w-5" />
            )}
          </button>
        </div>
      </div>

      {/* Navigation */}
      <nav className="p-4">
        <ul className="space-y-2">
          {userMenuItems.map((item) => {
            const Icon = item.icon;
            const isActive = currentPage === item.path;
            
            return (
              <li key={item.path}>
                <button
                  onClick={() => handleNavigation(item.path)}
                  className={`w-full flex items-center space-x-3 px-3 py-2 rounded-md text-left transition-colors ${
                    isActive
                      ? 'bg-black text-white'
                      : 'text-gray-700 hover:bg-gray-100'
                  }`}
                  title={isCollapsed ? item.label : ''}
                >
                  <Icon className="h-5 w-5 flex-shrink-0" />
                  {!isCollapsed && (
                    <span className="font-medium">{item.label}</span>
                  )}
                </button>
              </li>
            );
          })}
        </ul>
      </nav>
    </div>
  );
}
