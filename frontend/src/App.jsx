import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import Login from './components/auth/Login';
import Register from './components/auth/register';
import Dashboard from './components/Dashboard';
import StudentList from './components/students/StudentList';
import AdminDashboard from './components/dashboards/AdminDashboard';
import TeacherDashboard from './components/dashboards/TeacherDashboard';
import StudentDashboard from './components/dashboards/StudentDashboard';
import Layout from './components/layout/Layout';
import TeacherList from './components/teachers/TeacherList';
import CourseList from './components/courses/CourseList';
import ScheduleList from './components/schedules/ScheduleList';
import FinancePage from './components/finance/FinancePage';
import CommunicationPage from './components/communication/CommunicationPage';
import './App.css';

// Protected Route Component
const ProtectedRoute = ({ children }) => {
  const { isAuthenticated, loading } = useAuth();
  
  if (loading) {
    return <div className="flex items-center justify-center min-h-screen">Loading...</div>;
  }
  
  return isAuthenticated ? children : <Navigate to="/login" />;
};

// Public Route Component (redirect if already authenticated)
const PublicRoute = ({ children }) => {
  const { isAuthenticated, loading } = useAuth();
  
  if (loading) {
    return <div className="flex items-center justify-center min-h-screen">Loading...</div>;
  }
  
  return !isAuthenticated ? children : <Navigate to="/dashboard" />;
};

function App() {
  return (
    <AuthProvider>
      <Router>
        <div className="App">
          <Routes>
            <Route path="/" element={<Navigate to="/dashboard" />} />
            <Route 
              path="/login" 
              element={
                <PublicRoute>
                  <Login />
                </PublicRoute>
              } 
            />
            <Route 
              path="/register" 
              element={
                <PublicRoute>
                  <Register />
                </PublicRoute>
              } 
            />
            
            {/* Protected Routes with Layout */}
            <Route 
              path="/dashboard" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <Dashboard />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/students" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <StudentList />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/teachers" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <TeacherList />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/courses" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <CourseList />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/schedule" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <ScheduleList />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/reports" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <div className="text-center py-12">Reports Page Coming Soon...</div>
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/finance" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <FinancePage />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/communication" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <CommunicationPage />
                  </Layout>
                </ProtectedRoute>
              } 
            />
            <Route 
              path="/settings" 
              element={
                <ProtectedRoute>
                  <Layout>
                    <div className="text-center py-12">Settings Page Coming Soon...</div>
                  </Layout>
                </ProtectedRoute>
              } 
            />
          </Routes>
        </div>
      </Router>
    </AuthProvider>
  );
}

export default App;