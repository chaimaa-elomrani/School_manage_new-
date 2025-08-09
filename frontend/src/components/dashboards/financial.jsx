import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { BanknoteIcon } from 'lucide-react'

export function Financial({ data }) {
  const { payments = [] } = data
  
  // Calculate financial stats
  const totalPaid = payments
    .filter(p => p.status === "paid")
    .reduce((sum, p) => sum + Number(p.amount || 0), 0)
    
  const totalPending = payments
    .filter(p => p.status === "pending")
    .reduce((sum, p) => sum + Number(p.amount || 0), 0)
    
  const nextPayment = payments.find(p => p.status === "pending")
  
  return (
    <div className="space-y-6">
      <Card className="bg-gradient-to-r from-green-50 to-emerald-50 border-green-200">
        <CardContent className="p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h3 className="text-xl font-bold text-green-900">Payment Overview</h3>
              <p className="text-green-600">Manage your school fees</p>
            </div>
            <div className="bg-white p-3 rounded-full">
              <BanknoteIcon className="h-8 w-8 text-green-600" />
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card>
              <CardContent className="p-4">
                <p className="text-sm text-gray-600">Total Paid</p>
                <p className="text-2xl font-bold text-green-600">${totalPaid.toFixed(2)}</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="p-4">
                <p className="text-sm text-gray-600">Outstanding</p>
                <p className="text-2xl font-bold text-red-600">${totalPending.toFixed(2)}</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="p-4">
                <p className="text-sm text-gray-600">Next Due</p>
                <p className="text-2xl font-bold text-orange-600">
                  ${nextPayment ? Number(nextPayment.amount).toFixed(2) : '0.00'}
                </p>
              </CardContent>
            </Card>
          </div>
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <CardTitle>Payment History</CardTitle>
          <Button>Make Payment</Button>
        </CardHeader>
        <CardContent>
          {payments && payments.length > 0 ? (
            <div className="space-y-3">
              {payments.map((payment, i) => (
                <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="text-sm font-medium text-gray-900">{payment.description}</p>
                    <p className="text-xs text-gray-500">
                      {payment.child_name || ''} • {new Date(payment.date || payment.created_at).toLocaleDateString()}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-bold text-gray-900">${Number(payment.amount).toFixed(2)}</p>
                    <Badge variant={payment.status === "paid" ? "success" : "destructive"}>
                      {payment.status}
                    </Badge>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-gray-500 text-center py-4">No payment history available</p>
          )}
        </CardContent>
      </Card>
      
      <Card>
        <CardHeader>
          <CardTitle>Payment Schedule</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {payments && payments.filter(p => p.status === "pending").length > 0 ? (
              payments
                .filter(p => p.status === "pending")
                .map((payment, i) => (
                  <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                      <p className="text-sm font-medium text-gray-900">{payment.description}</p>
                      <p className="text-xs text-gray-500">Due: {new Date(payment.due_date).toLocaleDateString()}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-sm font-bold text-gray-900">${Number(payment.amount).toFixed(2)}</p>
                      <Button size="sm" variant="outline" className="mt-1">Pay Now</Button>
                    </div>
                  </div>
                ))
            ) : (
              <p className="text-gray-500 text-center py-4">No upcoming payments</p>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
