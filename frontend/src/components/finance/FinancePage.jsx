import { useState, useEffect } from 'react';
import { 
  CurrencyDollarIcon, 
  BanknotesIcon,
  ChartBarIcon,
  CalendarIcon,
  UserIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon
} from '@heroicons/react/24/outline';
import api from '../../services/api';

const FinancePage = () => {
  const [payments, setPayments] = useState([]);
  const [students, setStudents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    totalRevenue: 0,
    paidPayments: 0,
    pendingPayments: 0,
    overduePayments: 0,
    revenueGrowth: 0,
    paymentGrowth: 0,
    avgPayment: 0
  });
  const [selectedPeriod, setSelectedPeriod] = useState('all');

  useEffect(() => {
    fetchFinanceData();
  }, []);

  const fetchFinanceData = async () => {
    try {
        setLoading(true);
        console.log('Fetching finance data...');
        
        // Fetch payments and students in parallel
        const [paymentsRes, studentsRes] = await Promise.all([
            api.get('/showPayments'),
            api.get('/showStudent')
        ]);

        console.log('Payments response:', paymentsRes);
        console.log('Students response:', studentsRes);

        // Extract data with proper fallbacks
        const paymentsData = paymentsRes.data?.data || [];
        const studentsData = studentsRes.data?.data || [];

        // Format payments data
        const formattedPayments = paymentsData.map(payment => ({
            id: payment.id,
            student_id: payment.student_id,
            amount: parseFloat(payment.amount || 0),
            status: payment.status || 'pending',
            payment_date: payment.payment_date,
            due_date: payment.due_date,
            fee_name: payment.fee_name,
            student_name: payment.student_name
        }));

        setPayments(formattedPayments);
        setStudents(studentsData);

        // Calculate statistics
        const totalRevenue = formattedPayments
            .filter(p => p.status === 'paid')
            .reduce((sum, p) => sum + p.amount, 0);

        const paidCount = formattedPayments.filter(p => p.status === 'paid').length;
        const pendingCount = formattedPayments.filter(p => p.status === 'pending').length;
        const overdueCount = formattedPayments.filter(p => p.status === 'overdue').length;

        setStats({
            totalRevenue,
            paidPayments: paidCount,
            pendingPayments: pendingCount,
            overduePayments: overdueCount,
            revenueGrowth: 0, // Calculate this based on your requirements
            paymentGrowth: 0, // Calculate this based on your requirements
            avgPayment: paidCount > 0 ? totalRevenue / paidCount : 0
        });

    } catch (error) {
        console.error('Error fetching finance data:', error);
        // Show error state or notification to user
    } finally {
        setLoading(false);
    }
  };

  const getStudentName = (studentId) => {
    const student = students.find(s => s.id === studentId);
    return student ? `${student.first_name || student.prenom} ${student.last_name || student.nom}` : 'Unknown Student';
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'paid': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'overdue': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusIcon = (status) => {
    switch (status) {
      case 'paid': return <CheckCircleIcon className="h-4 w-4" />;
      case 'pending': return <ClockIcon className="h-4 w-4" />;
      case 'overdue': return <XCircleIcon className="h-4 w-4" />;
      default: return <ClockIcon className="h-4 w-4" />;
    }
  };

  const formatGrowth = (growth) => {
    const sign = growth >= 0 ? '+' : '';
    return `${sign}${growth}%`;
  };

  const getGrowthColor = (growth) => {
    return growth >= 0 ? 'text-green-500' : 'text-red-500';
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Financial Management</h1>
          <p className="text-gray-600">Track payments, revenue, and financial reports</p>
        </div>
        <div className="flex space-x-3">
          <select 
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
          >
            <option value="all">All Time</option>
            <option value="month">This Month</option>
            <option value="week">This Week</option>
          </select>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow p-6 text-white">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-green-100">Total Revenue</p>
              <p className="text-3xl font-bold">${stats.totalRevenue.toLocaleString()}</p>
              <p className="text-green-200 text-sm">+15% from last month</p>
            </div>
            <CurrencyDollarIcon className="h-12 w-12 text-green-200" />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600">Paid Payments</p>
              <p className="text-3xl font-bold text-green-600">{stats.paidPayments}</p>
              <p className="text-green-500 text-sm">Completed</p>
            </div>
            <CheckCircleIcon className="h-12 w-12 text-green-500" />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600">Pending Payments</p>
              <p className="text-3xl font-bold text-yellow-600">{stats.pendingPayments}</p>
              <p className="text-yellow-500 text-sm">Awaiting payment</p>
            </div>
            <ClockIcon className="h-12 w-12 text-yellow-500" />
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600">Overdue Payments</p>
              <p className="text-3xl font-bold text-red-600">{stats.overduePayments}</p>
              <p className="text-red-500 text-sm">Requires attention</p>
            </div>
            <XCircleIcon className="h-12 w-12 text-red-500" />
          </div>
        </div>
      </div>

      {/* Payments Table */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-200">
          <div className="flex items-center justify-between">
            <h3 className="text-lg font-semibold text-gray-900">Payment Records</h3>
            <div className="flex items-center space-x-2">
              <BanknotesIcon className="h-5 w-5 text-gray-400" />
              <span className="text-sm text-gray-600">{payments.length} total payments</span>
            </div>
          </div>
        </div>
        
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Student
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Amount
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Payment Date
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Due Date
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {payments.map((payment) => (
                <tr key={payment.id} className="hover:bg-gray-50 transition-colors">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center">
                      <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <UserIcon className="h-5 w-5 text-white" />
                      </div>
                      <div className="ml-4">
                        <div className="text-sm font-medium text-gray-900">
                          {getStudentName(payment.student_id)}
                        </div>
                        <div className="text-sm text-gray-500">ID: {payment.student_id}</div>
                      </div>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-lg font-bold text-gray-900">
                      ${parseFloat(payment.amount || 0).toLocaleString()}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(payment.status)}`}>
                      {getStatusIcon(payment.status)}
                      <span className="ml-1 capitalize">{payment.status}</span>
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {payment.payment_date ? new Date(payment.payment_date).toLocaleDateString() : 'Not paid'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {payment.due_date ? new Date(payment.due_date).toLocaleDateString() : 'No due date'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {payments.length === 0 && (
          <div className="text-center py-12">
            <BanknotesIcon className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
            <p className="mt-1 text-sm text-gray-500">Get started by adding payment records.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default FinancePage;