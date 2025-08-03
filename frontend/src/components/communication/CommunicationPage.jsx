import { useState, useEffect } from 'react';
import { 
  EnvelopeIcon, 
  DevicePhoneMobileIcon,
  ChatBubbleLeftRightIcon,
  BellIcon,
  UserGroupIcon,
  DocumentTextIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  PaperAirplaneIcon
} from '@heroicons/react/24/outline';
import api from '../../services/api';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const CommunicationPage = () => {
  const [activeTab, setActiveTab] = useState('notifications');
  const [notifications, setNotifications] = useState([]);
  const [students, setStudents] = useState([]);
  const [courses, setCourses] = useState([]);
  const [evaluations, setEvaluations] = useState([]);
  const [loading, setLoading] = useState(false);
  const [sendingNotification, setSendingNotification] = useState(false);
  const [error, setError] = useState('');

  // Form states
  const [emailForm, setEmailForm] = useState({
    email: '',
    title: '',
    message: ''
  });

  const [smsForm, setSmsForm] = useState({
    phone: '',
    message: ''
  });

  const [internalMessageForm, setInternalMessageForm] = useState({
    sender_id: 1,
    receiver_id: '',
    subject: '',
    content: ''
  });

  const [broadcastForm, setBroadcastForm] = useState({
    recipients: [],
    title: '',
    message: '',
    channels: ['email']
  });

  const [bulletinForm, setBulletinForm] = useState({
    student_id: '',
    course_id: '',
    evaluation_id: ''
  });

  useEffect(() => {
    fetchData();
  }, []); // Remove activeTab dependency to prevent multiple calls

  const fetchData = async () => {
    if (loading) return; // Prevent multiple simultaneous calls
    
    try {
      setLoading(true);
      setError('');
      
      // Fetch all required data
      const [notificationsRes, studentsRes, coursesRes, evaluationsRes] = await Promise.all([
        api.get('/notifications').catch(err => {
          console.log('Notifications API failed:', err);
          return { data: [] };
        }),
        api.get('/showStudent').catch(err => {
          console.log('Students API failed:', err);
          return { data: [] };
        }),
        api.get('/showCourses').catch(err => {
          console.log('Courses API failed:', err);
          return { data: [] };
        }),
        api.get('/showEvaluations').catch(err => {
          console.log('Evaluations API failed:', err);
          return { data: [] };
        })
      ]);

      // Handle different response structures
      const notificationsData = notificationsRes.data?.data || notificationsRes.data || [];
      const studentsData = studentsRes.data?.data || studentsRes.data || [];
      const coursesData = coursesRes.data?.data || coursesRes.data || [];
      const evaluationsData = evaluationsRes.data?.data || evaluationsRes.data || [];

      setNotifications(Array.isArray(notificationsData) ? notificationsData : []);
      setStudents(Array.isArray(studentsData) ? studentsData : []);
      setCourses(Array.isArray(coursesData) ? coursesData : []);
      setEvaluations(Array.isArray(evaluationsData) ? evaluationsData : []);

      console.log('Loaded data:', {
        notifications: notificationsData.length,
        students: studentsData.length,
        courses: coursesData.length,
        evaluations: evaluationsData.length
      });

    } catch (error) {
      console.error('Error fetching data:', error);
      setError('Failed to load data. Please refresh the page.');
    } finally {
      setLoading(false);
    }
  };

  const sendEmailNotification = async (e) => {
    e.preventDefault();
    setSendingNotification(true);
    try {
        const response = await api.post('/communication/email', emailForm);
        if (response.data.success) {
            toast.success('Email sent successfully!');
            setEmailForm({ email: '', title: '', message: '' });
            await fetchData();
        } else {
            toast.error(response.data.message || 'Failed to send email');
        }
    } catch (error) {
        console.error('Email error:', error);
        toast.error(error.response?.data?.error || 'Error sending email');
    } finally {
        setSendingNotification(false);
    }
};

const sendSMSNotification = async (e) => {
    e.preventDefault();
    setSendingNotification(true);
    try {
        const response = await api.post('/communication/sms', smsForm);
        if (response.data.success) {
            toast.success('SMS sent successfully!');
            setSmsForm({ phone: '', message: '' });
            await fetchData();
        } else {
            toast.error(response.data.message || 'Failed to send SMS');
        }
    } catch (error) {
        console.error('SMS error:', error);
        toast.error(error.response?.data?.error || 'Error sending SMS');
    } finally {
        setSendingNotification(false);
    }
};

const sendInternalMessage = async (e) => {
    e.preventDefault();
    setSendingNotification(true);
    try {
        const response = await api.post('/communication/message', internalMessageForm);
        if (response.data.success) {
            toast.success('Message sent successfully!');
            setInternalMessageForm({
                sender_id: 1,
                receiver_id: '',
                subject: '',
                content: ''
            });
            await fetchData();
        } else {
            toast.error(response.data.message || 'Failed to send message');
        }
    } catch (error) {
        console.error('Message error:', error);
        toast.error(error.response?.data?.error || 'Error sending message');
    } finally {
        setSendingNotification(false);
    }
};

  const sendBroadcastNotification = async (e) => {
    e.preventDefault();
    setSendingNotification(true);
    try {
      const recipients = broadcastForm.recipients.map(studentId => {
        const student = students.find(s => s.id === parseInt(studentId));
        return {
          id: studentId,
          email: student?.email || `student${studentId}@school.com`,
          phone: student?.phone || `+1234567890`,
          user_id: studentId
        };
      });

      const response = await api.post('/communication/broadcast', {
        recipients,
        title: broadcastForm.title,
        message: broadcastForm.message,
        channels: broadcastForm.channels
      });

      if (response.data.success) {
        alert('Broadcast sent successfully!');
        setBroadcastForm({ recipients: [], title: '', message: '', channels: ['email'] });
        fetchData();
      } else {
        alert('Failed to send broadcast: ' + (response.data.message || 'Unknown error'));
      }
    } catch (error) {
      console.error('Broadcast error:', error);
      alert('Error sending broadcast: ' + (error.response?.data?.message || error.message));
    } finally {
      setSendingNotification(false);
    }
  };

  const generateBulletin = async (e) => {
    e.preventDefault();
    setSendingNotification(true);
    try {
      const response = await api.post('/bulletin/generate', bulletinForm);
      if (response.data.message || response.data.success) {
        alert('Bulletin generated successfully! Notifications sent to parents and student.');
        setBulletinForm({ student_id: '', course_id: '', evaluation_id: '' });
        fetchData();
      } else {
        alert('Failed to generate bulletin: ' + (response.data.error || 'Unknown error'));
      }
    } catch (error) {
      console.error('Bulletin error:', error);
      alert('Error generating bulletin: ' + (error.response?.data?.message || error.message));
    } finally {
      setSendingNotification(false);
    }
  };

  const getStatusIcon = (status) => {
    switch (status) {
      case 'sent': return <CheckCircleIcon className="h-4 w-4 text-green-500" />;
      case 'failed': return <XCircleIcon className="h-4 w-4 text-red-500" />;
      default: return <ClockIcon className="h-4 w-4 text-yellow-500" />;
    }
  };

  const tabs = [
    { id: 'notifications', name: 'Notifications', icon: BellIcon },
    { id: 'email', name: 'Email', icon: EnvelopeIcon },
    { id: 'sms', name: 'SMS', icon: DevicePhoneMobileIcon },
    { id: 'internal', name: 'Internal Messages', icon: ChatBubbleLeftRightIcon },
    { id: 'broadcast', name: 'Broadcast', icon: UserGroupIcon },
    { id: 'bulletins', name: 'Generate Bulletins', icon: DocumentTextIcon }
  ];

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        <span className="ml-3 text-gray-600">Loading communication data...</span>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Communication Center</h1>
          <p className="text-gray-600">Send notifications, messages, and generate bulletins</p>
        </div>
        <button
          onClick={fetchData}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Refresh Data
        </button>
      </div>

      {/* Error Message */}
      {error && (
        <div className="bg-red-50 border border-red-200 rounded-lg p-4">
          <div className="flex">
            <XCircleIcon className="h-5 w-5 text-red-400" />
            <div className="ml-3">
              <h3 className="text-sm font-medium text-red-800">Error</h3>
              <p className="text-sm text-red-700 mt-1">{error}</p>
            </div>
          </div>
        </div>
      )}

      {/* Data Summary */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-center">
          <BellIcon className="h-5 w-5 text-blue-400" />
          <div className="ml-3">
            <h3 className="text-sm font-medium text-blue-800">Data Summary</h3>
            <p className="text-sm text-blue-700 mt-1">
              Students: {students.length} | Courses: {courses.length} | Evaluations: {evaluations.length} | Notifications: {notifications.length}
            </p>
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`${
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              } whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center space-x-2`}
            >
              <tab.icon className="h-5 w-5" />
              <span>{tab.name}</span>
            </button>
          ))}
        </nav>
      </div>

      {/* Tab Content */}
      <div className="bg-white rounded-lg shadow p-6">
        {/* Notifications Tab */}
        {activeTab === 'notifications' && (
          <div>
            <h3 className="text-lg font-semibold mb-4">Recent Notifications</h3>
            <div className="space-y-4">
              {notifications.length > 0 ? (
                notifications.map((notification, index) => (
                  <div key={`notification-${notification.id}-${index}-${Date.now()}`} className="border rounded-lg p-4 hover:bg-gray-50">
                    <div className="flex items-start justify-between">
                      <div className="flex-1">
                        <div className="flex items-center space-x-2">
                          {getStatusIcon(notification.status)}
                          <h4 className="font-medium text-gray-900">{notification.title || 'No Title'}</h4>
                          <span className={`px-2 py-1 text-xs rounded-full ${
                            notification.type === 'email' ? 'bg-blue-100 text-blue-800' :
                            notification.type === 'sms' ? 'bg-green-100 text-green-800' :
                            'bg-gray-100 text-gray-800'
                          }`}>
                            {notification.type || 'unknown'}
                          </span>
                        </div>
                        <p className="text-gray-600 mt-1">{notification.message || 'No message'}</p>
                        <p className="text-sm text-gray-400 mt-2">
                          {notification.created_at ? new Date(notification.created_at).toLocaleString() : 'No date'}
                        </p>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="text-center py-8">
                  <BellIcon className="mx-auto h-12 w-12 text-gray-400" />
                  <h3 className="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                  <p className="mt-1 text-sm text-gray-500">Start by sending your first notification.</p>
                </div>
              )}
            </div>
          </div>
        )}

        {/* Email Tab */}
        {activeTab === 'email' && (
          <div>
            <h3 className="text-lg font-semibold mb-4">Send Email Notification</h3>
            <form onSubmit={sendEmailNotification} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Email Address</label>
                <input
                  type="email"
                  value={emailForm.email}
                  onChange={(e) => setEmailForm({...emailForm, email: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="student@example.com"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Subject</label>
                <input
                  type="text"
                  value={emailForm.title}
                  onChange={(e) => setEmailForm({...emailForm, title: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Important School Notice"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Message</label>
                <textarea
                  value={emailForm.message}
                  onChange={(e) => setEmailForm({...emailForm, message: e.target.value})}
                  rows={4}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Enter your message here..."
                  required
                />
              </div>
              <button
                type="submit"
                disabled={sendingNotification}
                className="flex items-center space-x-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                <PaperAirplaneIcon className="h-4 w-4" />
                <span>{sendingNotification ? 'Sending...' : 'Send Email'}</span>
              </button>
            </form>
          </div>
        )}

        {/* SMS Tab */}
        {activeTab === 'sms' && (
          <div>
            <h3 className="text-lg font-semibold mb-4">Send SMS Notification</h3>
            <form onSubmit={sendSMSNotification} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Phone Number</label>
                <input
                  type="tel"
                  value={smsForm.phone}
                  onChange={(e) => setSmsForm({...smsForm, phone: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="+1234567890"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Message</label>
                <textarea
                  value={smsForm.message}
                  onChange={(e) => setSmsForm({...smsForm, message: e.target.value})}
                  rows={3}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Your SMS message..."
                  maxLength={160}
                  required
                />
                <p className="text-sm text-gray-500 mt-1">{smsForm.message.length}/160 characters</p>
              </div>
              <button
                type="submit"
                disabled={sendingNotification}
                className="flex items-center space-x-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 disabled:opacity-50"
              >
                <DevicePhoneMobileIcon className="h-4 w-4" />
                <span>{sendingNotification ? 'Sending...' : 'Send SMS'}</span>
              </button>
            </form>
          </div>
        )}

        {/* Internal Messages Tab */}
        {activeTab === 'internal' && (
          <div>
            <h3 className="text-lg font-semibold mb-4">Send Internal Message</h3>
            <form onSubmit={sendInternalMessage} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Recipient</label>
                <select
                  value={internalMessageForm.receiver_id}
                  onChange={(e) => setInternalMessageForm({...internalMessageForm, receiver_id: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Select a student</option>
                  {students.map((student, index) => (
                    <option key={`student-${student.id}-${index}`} value={student.id}>
                      {student.first_name || student.prenom || 'Unknown'} {student.last_name || student.nom || 'Student'}
                    </option>
                  ))}
                </select>
                {students.length === 0 && (
                  <p className="text-sm text-gray-500 mt-1">No students available. Please add students first.</p>
                )}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Subject</label>
                <input
                  type="text"
                  value={internalMessageForm.subject}
                  onChange={(e) => setInternalMessageForm({...internalMessageForm, subject: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Message subject"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Content</label>
                <textarea
                  value={internalMessageForm.content}
                  onChange={(e) => setInternalMessageForm({...internalMessageForm, content: e.target.value})}
                  rows={4}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Message content..."
                  required
                />
              </div>
              <button
                type="submit"
                disabled={sendingNotification || students.length === 0}
                className="flex items-center space-x-2 bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 disabled:opacity-50"
              >
                <ChatBubbleLeftRightIcon className="h-4 w-4" />
                <span>{sendingNotification ? 'Sending...' : 'Send Message'}</span>
              </button>
            </form>
          </div>
        )}

        {/* Broadcast Tab */}
        {activeTab === 'broadcast' && (
          <div>
            <h3 className="text-lg font-semibold mb-4">Broadcast Notification</h3>
            <form onSubmit={sendBroadcastNotification} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Recipients</label>
                <select
                  multiple
                  value={broadcastForm.recipients}
                  onChange={(e) => setBroadcastForm({
                    ...broadcastForm, 
                    recipients: Array.from(e.target.selectedOptions, option => option.value)
                  })}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  size={5}
                  required
                >
                  {students.map((student, index) => (
                    <option key={`student-${student.id}-${index}`} value={student.id}>
                      {student.first_name || student.prenom || 'Unknown'} {student.last_name || student.nom || 'Student'}
                    </option>
                  ))}
                </select>
                <p className="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple students</p>
                {students.length === 0 && (
                  <p className="text-sm text-red-500 mt-1">No students available. Please add students first.</p>
                )}
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Channels</label>
                <div className="mt-2 space-y-2">
                  {['email', 'sms', 'internal'].map((channel) => (
                    <label key={channel} className="flex items-center">
                      <input
                        type="checkbox"
                        checked={broadcastForm.channels.includes(channel)}
                        onChange={(e) => {
                          if (e.target.checked) {
                            setBroadcastForm({
                              ...broadcastForm,
                              channels: [...broadcastForm.channels, channel]
                            });
                          } else {
                            setBroadcastForm({
                              ...broadcastForm,
                              channels: broadcastForm.channels.filter(c => c !== channel)
                            });
                          }
                        }}
                        className="mr-2"
                      />
                      <span className="capitalize">{channel}</span>
                    </label>
                  ))}
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Title</label>
                <input
                  type="text"
                  value={broadcastForm.title}
                  onChange={(e) => setBroadcastForm({...broadcastForm, title: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Broadcast title"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Message</label>
                <textarea
                  value={broadcastForm.message}
                  onChange={(e) => setBroadcastForm({...broadcastForm, message: e.target.value})}
                  rows={4}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Broadcast message..."
                  required
                />
              </div>
              <button
                type="submit"
                disabled={sendingNotification || students.length === 0}
                className="flex items-center space-x-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                <UserGroupIcon className="h-4 w-4" />
                <span>{sendingNotification ? 'Broadcasting...' : 'Send Broadcast'}</span>
              </button>
            </form>
          </div>
        )}

        {/* Bulletins Tab */}
        {activeTab === 'bulletins' && (
          <div>
            <h3 className="text-lg font-semibold mb-4">Generate Student Bulletin</h3>
            <p className="text-gray-600 mb-4">
              Generate a bulletin using the Template Method pattern. This will automatically notify parents and students via the Observer pattern.
            </p>
            <form onSubmit={generateBulletin} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700">Student</label>
                <select
                  value={bulletinForm.student_id}
                  onChange={(e) => setBulletinForm({...bulletinForm, student_id: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Select a student</option>
                  {students.map((student, index) => (
                    <option key={`student-${student.id}-${index}`} value={student.id}>
                      {student.first_name || student.prenom || 'Unknown'} {student.last_name || student.nom || 'Student'}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Course</label>
                <select
                  value={bulletinForm.course_id}
                  onChange={(e) => setBulletinForm({...bulletinForm, course_id: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Select a course</option>
                  {courses.map((course, index) => (
                    <option key={`course-${course.id}-${index}`} value={course.id}>
                      {course.name || course.title || `Course ${course.id}`}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700">Evaluation</label>
                <select
                  value={bulletinForm.evaluation_id}
                  onChange={(e) => setBulletinForm({...bulletinForm, evaluation_id: e.target.value})}
                  className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required
                >
                  <option value="">Select an evaluation</option>
                  {evaluations.map((evaluation) => (
                    <option key={evaluation.id} value={evaluation.id}>
                      {evaluation.title || evaluation.name || `Evaluation ${evaluation.id}`}
                    </option>
                  ))}
                </select>
              </div>
              <button
                type="submit"
                disabled={sendingNotification || students.length === 0}
                className="flex items-center space-x-2 bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 disabled:opacity-50"
              >
                <DocumentTextIcon className="h-4 w-4" />
                <span>{sendingNotification ? 'Generating...' : 'Generate Bulletin'}</span>
              </button>
            </form>
          </div>
        )}
      </div>

      <ToastContainer position="top-right" autoClose={3000} />
    </div>
  );
};

export default CommunicationPage;



