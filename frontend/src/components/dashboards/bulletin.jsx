"use client"

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Progress } from "@/components/ui/progress"
import { 
  Chart as ChartJS, 
  CategoryScale, 
  LinearScale, 
  BarElement, 
  Title, 
  Tooltip, 
  Legend,
  ArcElement 
} from "chart.js"
import { Bar, Doughnut } from "react-chartjs-2"
import { Download, TrendingUp, Award, BookOpen } from 'lucide-react'

ChartJS.register(
  CategoryScale, 
  LinearScale, 
  BarElement, 
  ArcElement,
  Title, 
  Tooltip, 
  Legend
)

export function Bulletin({ data }) {
  const { grades = [], evaluations = [] } = data
  
  // Calculate grade statistics
  const gradesBySubject = grades.reduce((acc, grade) => {
    const subject = grade.subject_name
    if (!acc[subject]) {
      acc[subject] = []
    }
    acc[subject].push(Number(grade.note))
    return acc
  }, {})
  
  const subjectAverages = Object.entries(gradesBySubject).map(([subject, gradeList]) => ({
    subject,
    average: gradeList.reduce((sum, grade) => sum + grade, 0) / gradeList.length,
    count: gradeList.length,
    grades: gradeList
  }))
  
  const overallAverage = grades.length > 0 
    ? grades.reduce((sum, grade) => sum + Number(grade.note), 0) / grades.length 
    : 0
  
  // Grade distribution
  const gradeDistribution = {
    excellent: grades.filter(g => Number(g.note) >= 16).length,
    good: grades.filter(g => Number(g.note) >= 12 && Number(g.note) < 16).length,
    average: grades.filter(g => Number(g.note) >= 8 && Number(g.note) < 12).length,
    poor: grades.filter(g => Number(g.note) < 8).length
  }
  
  // Chart data for subject averages
  const subjectAveragesChart = {
    labels: subjectAverages.map(s => s.subject),
    datasets: [
      {
        label: "Average Grade",
        data: subjectAverages.map(s => s.average),
        backgroundColor: subjectAverages.map(s => 
          s.average >= 16 ? "rgba(34, 197, 94, 0.8)" :
          s.average >= 12 ? "rgba(59, 130, 246, 0.8)" :
          s.average >= 8 ? "rgba(245, 158, 11, 0.8)" :
          "rgba(239, 68, 68, 0.8)"
        ),
        borderColor: subjectAverages.map(s => 
          s.average >= 16 ? "rgba(34, 197, 94, 1)" :
          s.average >= 12 ? "rgba(59, 130, 246, 1)" :
          s.average >= 8 ? "rgba(245, 158, 11, 1)" :
          "rgba(239, 68, 68, 1)"
        ),
        borderWidth: 1
      }
    ]
  }
  
  // Grade distribution chart
  const distributionChart = {
    labels: ["Excellent (16-20)", "Good (12-15)", "Average (8-11)", "Poor (0-7)"],
    datasets: [
      {
        data: [
          gradeDistribution.excellent,
          gradeDistribution.good,
          gradeDistribution.average,
          gradeDistribution.poor
        ],
        backgroundColor: [
          "rgba(34, 197, 94, 0.8)",
          "rgba(59, 130, 246, 0.8)",
          "rgba(245, 158, 11, 0.8)",
          "rgba(239, 68, 68, 0.8)"
        ]
      }
    ]
  }
  
  const getGradeColor = (grade) => {
    if (grade >= 16) return "text-green-600 bg-green-50 border-green-200"
    if (grade >= 12) return "text-blue-600 bg-blue-50 border-blue-200"
    if (grade >= 8) return "text-yellow-600 bg-yellow-50 border-yellow-200"
    return "text-red-600 bg-red-50 border-red-200"
  }
  
  const getPerformanceLevel = (average) => {
    if (average >= 16) return { level: "Excellent", color: "text-green-600", icon: Award }
    if (average >= 12) return { level: "Good", color: "text-blue-600", icon: TrendingUp }
    if (average >= 8) return { level: "Average", color: "text-yellow-600", icon: BookOpen }
    return { level: "Needs Improvement", color: "text-red-600", icon: BookOpen }
  }
  
  const performance = getPerformanceLevel(overallAverage)
  const PerformanceIcon = performance.icon
  
  return (
    <div className="space-y-6">
      {/* Bulletin Header */}
      <Card className="bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
        <CardContent className="p-6">
          <div className="flex items-center justify-between mb-4">
            <div>
              <h2 className="text-2xl font-bold text-blue-900">Academic Bulletin</h2>
              <p className="text-blue-600">Your complete academic performance report</p>
            </div>
            <Button variant="outline" className="flex items-center space-x-2">
              <Download className="h-4 w-4" />
              <span>Download PDF</span>
            </Button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <Card>
              <CardContent className="p-4 text-center">
                <div className="flex items-center justify-center mb-2">
                  <PerformanceIcon className={`h-6 w-6 ${performance.color}`} />
                </div>
                <p className="text-sm text-gray-600">Overall Average</p>
                <p className={`text-2xl font-bold ${performance.color}`}>
                  {overallAverage.toFixed(2)}/20
                </p>
                <p className={`text-xs ${performance.color}`}>{performance.level}</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="p-4 text-center">
                <p className="text-sm text-gray-600">Total Evaluations</p>
                <p className="text-2xl font-bold text-gray-900">{grades.length}</p>
                <p className="text-xs text-gray-500">This semester</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="p-4 text-center">
                <p className="text-sm text-gray-600">Subjects</p>
                <p className="text-2xl font-bold text-gray-900">{subjectAverages.length}</p>
                <p className="text-xs text-gray-500">Currently enrolled</p>
              </CardContent>
            </Card>
            
            <Card>
              <CardContent className="p-4 text-center">
                <p className="text-sm text-gray-600">Class Rank</p>
                <p className="text-2xl font-bold text-gray-900">5th</p>
                <p className="text-xs text-gray-500">Out of 30 students</p>
              </CardContent>
            </Card>
          </div>
        </CardContent>
      </Card>
      
      {/* Subject Performance */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Subject Averages</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-64 mb-4">
              <Bar 
                data={subjectAveragesChart} 
                options={{ 
                  responsive: true, 
                  maintainAspectRatio: false,
                  scales: {
                    y: {
                      beginAtZero: true,
                      max: 20
                    }
                  }
                }} 
              />
            </div>
            <div className="space-y-3">
              {subjectAverages.map((subject, i) => (
                <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div>
                    <p className="text-sm font-medium text-gray-900">{subject.subject}</p>
                    <p className="text-xs text-gray-500">{subject.count} evaluations</p>
                  </div>
                  <div className="text-right">
                    <p className={`text-sm font-bold ${getGradeColor(subject.average).split(' ')[0]}`}>
                      {subject.average.toFixed(2)}/20
                    </p>
                    <Progress value={(subject.average / 20) * 100} className="w-16 h-2" />
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader>
            <CardTitle>Grade Distribution</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="h-64 mb-4">
              <Doughnut 
                data={distributionChart} 
                options={{ 
                  responsive: true, 
                  maintainAspectRatio: false 
                }} 
              />
            </div>
            <div className="grid grid-cols-2 gap-2">
              <div className="text-center p-2 bg-green-50 rounded">
                <p className="text-lg font-bold text-green-600">{gradeDistribution.excellent}</p>
                <p className="text-xs text-green-600">Excellent</p>
              </div>
              <div className="text-center p-2 bg-blue-50 rounded">
                <p className="text-lg font-bold text-blue-600">{gradeDistribution.good}</p>
                <p className="text-xs text-blue-600">Good</p>
              </div>
              <div className="text-center p-2 bg-yellow-50 rounded">
                <p className="text-lg font-bold text-yellow-600">{gradeDistribution.average}</p>
                <p className="text-xs text-yellow-600">Average</p>
              </div>
              <div className="text-center p-2 bg-red-50 rounded">
                <p className="text-lg font-bold text-red-600">{gradeDistribution.poor}</p>
                <p className="text-xs text-red-600">Poor</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      
      {/* Detailed Grades */}
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <CardTitle>All Evaluations</CardTitle>
          <Badge variant="secondary">{grades.length} Total</Badge>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {grades && grades.length > 0 ? (
              grades.map((grade, i) => (
                <div key={i} className={`flex items-center justify-between p-4 rounded-lg border ${getGradeColor(Number(grade.note))}`}>
                  <div className="flex items-center space-x-4">
                    <div className={`w-3 h-3 rounded-full ${
                      Number(grade.note) >= 16 ? "bg-green-500" : 
                      Number(grade.note) >= 12 ? "bg-blue-500" :
                      Number(grade.note) >= 8 ? "bg-yellow-500" : "bg-red-500"
                    }`} />
                    <div>
                      <p className="text-sm font-medium text-gray-900">{grade.subject_name}</p>
                      <p className="text-xs text-gray-500">
                        {grade.evaluation_type} • {grade.teacher_name}
                      </p>
                      {grade.description && (
                        <p className="text-xs text-gray-400 mt-1">{grade.description}</p>
                      )}
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-lg font-bold text-gray-900">{grade.note}/20</p>
                    <p className="text-xs text-gray-500">
                      {new Date(grade.date).toLocaleDateString()}
                    </p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-500 text-center py-8">No evaluations available</p>
            )}
          </div>
        </CardContent>
      </Card>
      
      {/* Recent Evaluations (Assignments) */}
      <Card>
        <CardHeader>
          <CardTitle>Recent Evaluations & Assignments</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-3">
            {evaluations && evaluations.length > 0 ? (
              evaluations.map((evaluation, i) => (
                <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                  <div>
                    <p className="text-sm font-medium text-gray-900">{evaluation.title}</p>
                    <p className="text-xs text-gray-500">
                      {evaluation.type} • {evaluation.course_name}
                    </p>
                    <p className="text-xs text-gray-400 mt-1">{evaluation.description}</p>
                  </div>
                  <div className="text-right">
                    <Badge variant={evaluation.status === "graded" ? "default" : "secondary"}>
                      {evaluation.status === "graded" ? `${evaluation.grade}/20` : evaluation.status}
                    </Badge>
                    <p className="text-xs text-gray-500 mt-1">
                      Due: {new Date(evaluation.due_date).toLocaleDateString()}
                    </p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-gray-500 text-center py-4">No recent evaluations</p>
            )}
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
