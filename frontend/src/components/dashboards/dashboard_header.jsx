import { User } from 'lucide-react'
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"

export function DashboardHeader({ user }) {
  return (
    <div className="flex flex-col space-y-2 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Student Dashboard</h1>
        <p className="text-gray-600">Welcome back, {user?.first_name || 'Student'}!</p>
      </div>
      <div className="flex items-center space-x-4">
        <div className="text-right hidden md:block">
          <p className="text-sm font-medium">{user?.first_name} {user?.last_name}</p>
          <p className="text-xs text-gray-500">{user?.email}</p>
        </div>
        <Avatar>
          <AvatarImage src={user?.avatar || ''} alt={user?.first_name || 'Student'} />
          <AvatarFallback>
            <User className="h-5 w-5" />
          </AvatarFallback>
        </Avatar>
      </div>
    </div>
  )
}
