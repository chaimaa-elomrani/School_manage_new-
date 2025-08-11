// this file is made for the connection with the backend
import axios from 'axios';     

// creating axious instance with base configuration 
// axious is a library that allows us to make http requests
// we are creating an instance of axious with a base url and a timeout
// the base url is the url of our backend
// the timeout is the time after which the request will be cancelled
// we are also adding a header to our request
// the header is the authorization header
// the authorization header is used to send the token to the backend
// the token is used to authenticate the user


// Add timeout and better error handling
const api = axios.create({
    baseURL: 'http://localhost:8000',
    timeout: 30000,  // Increased timeout for email sending
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Add request interceptor
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("authToken")
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    // Add special handling for email requests (existing logic)
    if (config.url?.includes("/sendEmail")) {
      console.log("Preparing email request:", {
        url: config.url,
        data: config.data,
      })
    }
    return config
  },
  (error) => {
    console.error("API Request Error:", error)
    return Promise.reject(error)
  },
);
// Update the response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem("authToken")
      localStorage.removeItem("user")
      window.location.href = "/login"
    }
    return Promise.reject(error)
  }
)

// Function to fetch all student data in parallel
export async function fetchStudentData(studentId) {
  try {
    // For demo purposes, we'll return mock data
    // In production, uncomment the API calls below
    
    
    const [
      profileRes,
      scheduleRes,
      teachersRes,
      gradesRes,
      paymentsRes,
      assignmentsRes,
      announcementsRes
    ] = await Promise.all([
      api.get(`/showStudent/${studentId}`),
      api.get(`//showStudent/{id}/${studentId}`),
      api.get(`/deleteStudent/${studentId}`),
    ])
    
    return {
      profile: profileRes.data?.data || {},
      schedule: scheduleRes.data?.data || [],
      teachers: teachersRes.data?.data || [],
      grades: gradesRes.data?.data || [],
      payments: paymentsRes.data?.data || [],
      assignments: assignmentsRes.data?.data || [],
      announcements: announcementsRes.data?.data || []
    }
    
    
    // Mock data for demonstration
    return {
      profile: {
        id: studentId,
        first_name: "John",
        last_name: "Doe",
        email: "student@example.com",
        phone: "123-456-7890",
        student_number: "S12345",
        attendance_rate: 95,
        classmates_count: 28
      },
      schedule: [
        {
          id: "1",
          course_id: "101",
          course_name: "Mathematics",
          subject_name: "Algebra",
          room_number: "204",
          building: "Science Building",
          teacher_name: "Prof. Smith",
          day_of_week: 1,
          day_name: "Monday",
          start_time: "08:00",
          end_time: "09:30"
        },
        {
          id: "2",
          course_id: "102",
          course_name: "Physics",
          subject_name: "Mechanics",
          room_number: "305",
          building: "Science Building",
          teacher_name: "Dr. Johnson",
          day_of_week: 1,
          day_name: "Monday",
          start_time: "10:00",
          end_time: "11:30"
        },
        {
          id: "3",
          course_id: "103",
          course_name: "Literature",
          subject_name: "Modern Poetry",
          room_number: "101",
          building: "Arts Building",
          teacher_name: "Ms. Williams",
          day_of_week: 2,
          day_name: "Tuesday",
          start_time: "13:00",
          end_time: "14:30"
        },
        {
          id: "4",
          course_id: "104",
          course_name: "History",
          subject_name: "World History",
          room_number: "202",
          building: "Arts Building",
          teacher_name: "Prof. Davis",
          day_of_week: 3,
          day_name: "Wednesday",
          start_time: "09:00",
          end_time: "10:30"
        },
        {
          id: "5",
          course_id: "105",
          course_name: "Computer Science",
          subject_name: "Programming",
          room_number: "405",
          building: "Tech Building",
          teacher_name: "Dr. Brown",
          day_of_week: 4,
          day_name: "Thursday",
          start_time: "14:00",
          end_time: "16:00"
        }
      ],
      teachers: [
        {
          id: "1",
          person_id: "101",
          first_name: "Robert",
          last_name: "Smith",
          email: "smith@school.edu",
          phone: "123-456-7890",
          specialty: "Mathematics",
          subject_name: "Algebra"
        },
        {
          id: "2",
          person_id: "102",
          first_name: "Emily",
          last_name: "Johnson",
          email: "johnson@school.edu",
          phone: "123-456-7891",
          specialty: "Physics",
          subject_name: "Mechanics"
        },
        {
          id: "3",
          person_id: "103",
          first_name: "Sarah",
          last_name: "Williams",
          email: "williams@school.edu",
          phone: "123-456-7892",
          specialty: "Literature",
          subject_name: "Modern Poetry"
        },
        {
          id: "4",
          person_id: "104",
          first_name: "Michael",
          last_name: "Davis",
          email: "davis@school.edu",
          phone: "123-456-7893",
          specialty: "History",
          subject_name: "World History"
        },
        {
          id: "5",
          person_id: "105",
          first_name: "Jennifer",
          last_name: "Brown",
          email: "brown@school.edu",
          phone: "123-456-7894",
          specialty: "Computer Science",
          subject_name: "Programming"
        }
      ],
      grades: [
        {
          id: "1",
          student_id: studentId,
          evaluation_id: "1",
          course_id: "101",
          note: "18",
          subject_name: "Algebra",
          course_name: "Mathematics",
          evaluation_type: "Exam",
          date: "2025-07-15",
          description: "Mid-term examination covering chapters 1-5",
          teacher_name: "Prof. Smith"
        },
        {
          id: "2",
          student_id: studentId,
          evaluation_id: "2",
          course_id: "102",
          note: "15",
          subject_name: "Mechanics",
          course_name: "Physics",
          evaluation_type: "Quiz",
          date: "2025-07-20",
          description: "Newton's laws and applications",
          teacher_name: "Dr. Johnson"
        },
        {
          id: "3",
          student_id: studentId,
          evaluation_id: "3",
          course_id: "103",
          note: "16",
          subject_name: "Modern Poetry",
          course_name: "Literature",
          evaluation_type: "Essay",
          date: "2025-07-25",
          description: "Analysis of contemporary poets",
          teacher_name: "Ms. Williams"
        },
        {
          id: "4",
          student_id: studentId,
          evaluation_id: "4",
          course_id: "104",
          note: "14",
          subject_name: "World History",
          course_name: "History",
          evaluation_type: "Presentation",
          date: "2025-07-30",
          description: "World War II impact presentation",
          teacher_name: "Prof. Davis"
        },
        {
          id: "5",
          student_id: studentId,
          evaluation_id: "5",
          course_id: "105",
          note: "19",
          subject_name: "Programming",
          course_name: "Computer Science",
          evaluation_type: "Project",
          date: "2025-08-05",
          description: "Web application development project",
          teacher_name: "Dr. Brown"
        },
        {
          id: "6",
          student_id: studentId,
          evaluation_id: "6",
          course_id: "101",
          note: "17",
          subject_name: "Algebra",
          course_name: "Mathematics",
          evaluation_type: "Quiz",
          date: "2025-08-01",
          description: "Quadratic equations quiz",
          teacher_name: "Prof. Smith"
        },
        {
          id: "7",
          student_id: studentId,
          evaluation_id: "7",
          course_id: "102",
          note: "13",
          subject_name: "Mechanics",
          course_name: "Physics",
          evaluation_type: "Lab Report",
          date: "2025-08-03",
          description: "Pendulum experiment analysis",
          teacher_name: "Dr. Johnson"
        }
      ],
      evaluations: [
        {
          id: "1",
          course_id: "101",
          course_name: "Mathematics",
          title: "Final Exam - Algebra",
          description: "Comprehensive final examination covering all semester topics",
          type: "Exam",
          due_date: "2025-08-20",
          status: "pending",
          grade: null
        },
        {
          id: "2",
          course_id: "102",
          course_name: "Physics",
          title: "Lab Report - Optics",
          description: "Write a detailed report on the light refraction experiment",
          type: "Lab Report",
          due_date: "2025-08-15",
          status: "submitted",
          grade: null
        },
        {
          id: "3",
          course_id: "103",
          course_name: "Literature",
          title: "Poetry Analysis Essay",
          description: "Analyze the themes in Robert Frost's poetry collection",
          type: "Essay",
          due_date: "2025-08-18",
          status: "graded",
          grade: "16"
        },
        {
          id: "4",
          course_id: "104",
          course_name: "History",
          title: "Research Project - Cold War",
          description: "Research and present on a specific aspect of the Cold War",
          type: "Project",
          due_date: "2025-08-25",
          status: "in_progress",
          grade: null
        },
        {
          id: "5",
          course_id: "105",
          course_name: "Computer Science",
          title: "Programming Assignment - Database",
          description: "Create a student management system using SQL and PHP",
          type: "Assignment",
          due_date: "2025-08-22",
          status: "pending",
          grade: null
        }
      ],
      assignments: [
        {
          id: "1",
          course_id: "101",
          title: "Algebra Problem Set",
          description: "Complete problems 1-20",
          due_date: "2025-08-10",
          submitted: true
        },
        {
          id: "2",
          course_id: "102",
          title: "Physics Lab Report",
          description: "Write a report on the pendulum experiment",
          due_date: "2025-08-15",
          submitted: false
        },
        {
          id: "3",
          course_id: "103",
          title: "Poetry Analysis",
          description: "Analyze the poem 'The Road Not Taken'",
          due_date: "2025-08-20",
          submitted: false
        }
      ],
      announcements: [
        {
          id: "1",
          title: "School Holiday",
          content: "School will be closed on August 15th for Independence Day.",
          created_at: "2025-08-01",
          created_by: "1",
          sender_name: "Principal Wilson"
        },
        {
          id: "2",
          title: "Parent-Teacher Meeting",
          content: "Parent-teacher meetings will be held on August 20th from 4 PM to 7 PM.",
          created_at: "2025-08-03",
          created_by: "1",
          sender_name: "Principal Wilson"
        },
        {
          id: "3",
          title: "Sports Day",
          content: "Annual sports day will be held on August 25th. All students are encouraged to participate.",
          created_at: "2025-08-05",
          created_by: "2",
          sender_name: "Sports Department"
        }
      ]
    }
  } catch (error) {
    console.error("Error fetching student data:", error)
    throw new Error("Failed to fetch student data")
  }
};

export default api;

