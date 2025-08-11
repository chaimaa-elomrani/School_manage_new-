"use client"

import { useState, useEffect } from "react"
import { useAuth } from "@/contexts/auth-context"
import { fetchStudentData } from "@/lib/api"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Skeleton } from "@/components/ui/skeleton"
import { Overview } from "@/components/dashboard/overview"
import { Academic } from "@/components/dashboard/academic"
import { Bulletin } from "@/components/dashboard/bulletin"
import { Communication } from "@/components/dashboard/communication"
import { DashboardHeader } from "@/components/dashboard/dashboard-header"
import { AlertCircle } from 'lucide-react'
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"

export function StudentDashboard() {
  const { user } = useAuth()
  const [data, setData] = useState({
    profile: null,
    schedule: [],
    teachers: [],
    grades: [],
    evaluations: [],
    announcements: [],
  })
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [activeTab, setActiveTab] = useState("overview")

  useEffect(() => {
    if (user?.id) {
      loadStudentData()
    }
  }, [user])

  const loadStudentData = async () => {
    try {
      setLoading(true)
      setError(null)
      
      // Use your actual API endpoint
      const studentData = await fetchStudentData(user.id)
      console.log("Student data loaded:", studentData)
      
      setData(studentData)
    } catch (err) {
      console.error("Error loading student data:", err)
      setError(err.message || "Failed to load student data")
    } finally {
      setLoading(false)
    }
  }

  // Handle tab navigation without router
  const handleTabChange = (tabValue) => {
    setActiveTab(tabValue)
    // Update URL without causing navigation
    if (typeof window !== 'undefined') {
      window.history.pushState({}, '', `#${tabValue}`)
    }
  }

  if (!user) {
    return (
      <div className="flex items-center justify-center h-screen">
        <Card className="w-[350px]">
          <CardHeader>
            <CardTitle>Authentication Required</CardTitle>
            <CardDescription>Please log in to access your dashboard</CardDescription>
          </CardHeader>
          <CardContent>
            <Button className="w-full" onClick={() => window.location.href = "/login"}>
              Go to Login
            </Button>
          </CardContent>
        </Card>
      </div>
    )
  }

  if (loading) {
    return <DashboardSkeleton />
  }

  if (error) {
    return (
      <div className="container mx-auto p-6">
        <Alert variant="destructive">
          <AlertCircle className="h-4 w-4" />
          <AlertTitle>Error</AlertTitle>
          <AlertDescription>{error}</AlertDescription>
        </Alert>
        <Button onClick={loadStudentData} className="mt-4">
          Retry
        </Button>
      </div>
    )
  }

  return (
    <div className="container mx-auto p-6 space-y-6">
      <DashboardHeader user={user} />
      
      <Tabs value={activeTab} onValueChange={handleTabChange} className="space-y-6">
        <TabsList className="grid grid-cols-4 w-full max-w-2xl">
          <TabsTrigger value="overview">Overview</TabsTrigger>
          <TabsTrigger value="academic">Academic</TabsTrigger>
          <TabsTrigger value="bulletin">Bulletin & Grades</TabsTrigger>
          <TabsTrigger value="communication">Communication</TabsTrigger>
        </TabsList>
        
        <TabsContent value="overview" className="space-y-6">
          <Overview data={data} />
        </TabsContent>
        
        <TabsContent value="academic" className="space-y-6">
          <Academic data={data} />
        </TabsContent>
        
        <TabsContent value="bulletin" className="space-y-6">
          <Bulletin data={data} />
        </TabsContent>
        
        <TabsContent value="communication" className="space-y-6">
          <Communication data={data} />
        </TabsContent>
      </Tabs>
    </div>
  )
}

function DashboardSkeleton() {
  return (
    <div className="container mx-auto p-6 space-y-6">
      <div className="space-y-2">
        <Skeleton className="h-10 w-[250px]" />
        <Skeleton className="h-4 w-[350px]" />
      </div>
      
      <Skeleton className="h-10 w-[400px]" />
      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {Array(4).fill(0).map((_, i) => (
          <Skeleton key={i} className="h-[180px] w-full" />
        ))}
      </div>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Skeleton className="h-[300px] w-full" />
        <Skeleton className="h-[300px] w-full" />
      </div>
    </div>
  )
}
