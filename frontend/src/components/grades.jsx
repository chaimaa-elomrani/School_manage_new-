"use client"

import { useState, useEffect } from "react"
import api from "../../services/api"
import { PlusIcon, PencilIcon, TrashIcon, MagnifyingGlassIcon, EyeIcon } from "@heroicons/react/24/outline"
import { UserIcon, BookOpenIcon, HomeIcon, ClockIcon, CalendarDaysIcon } from "@heroicons/react/24/outline"

const CourseList = () => {
  const [courses, setCourses] = useState([])
  const [teachers, setTeachers] = useState([])
  const [subjects, setSubjects] = useState([])
  const [classess, setclassess] = useState([]) // Added missing classess state
  const [loading, setLoading] = useState(true)
  const [searchTerm, setSearchTerm] = useState("")
  const [showModal, setShowModal] = useState(false)
  const [modalMode, setModalMode] = useState("create")
  const [selectedCourse, setSelectedCourse] = useState(null)
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    teacher_id: "",
    subject_id: "",
    class_id: "", // Changed classes_id to class_id to match backend
    duration: "",
    start_date: "",
    end_date: "",
  })

  const getTeacherFullName = (teacherId) => {
    const teacher = teachers.find((t) => t.id === teacherId)
    if (!teacher) return "Unassigned"

    // Try French field names first (nom, prenom), then English (first_name, last_name)
    const firstName = teacher.prenom || teacher.first_name || ""
    const lastName = teacher.nom || teacher.last_name || ""

    return firstName && lastName ? `${firstName} ${lastName}` : "Unassigned"
  }

  useEffect(() => {
    fetchCourses()
    fetchTeachers()
    fetchSubjects()
    fetchclassess()
  }, [])

  // Add a separate useEffect to refetch courses when teachers are loaded
  useEffect(() => {
    if (teachers.length > 0) {
      fetchCourses()
    }
  }, [teachers])

  const fetchCourses = async () => {
    try {
      setLoading(true)
      const response = await api.get("/showCourses")
      console.log("Raw courses response:", response.data)
      let coursesData = []
      if (response.data?.success && Array.isArray(response.data.data)) {
        coursesData = response.data.data
      } else if (response.data?.data && Array.isArray(response.data.data)) {
        coursesData = response.data.data
      } else if (Array.isArray(response.data)) {
        coursesData = response.data
      } else {
        console.error("Invalid course data format:", response.data)
        setCourses([])
        return
      }

      const formattedCourses = coursesData.map((course) => ({
        id: course.id,
        title: course.title || course.name || "",
        description: course.description || "",
        teacher_id: course.teacher_id,
        teacher_name: course.teacher_name || "Unassigned",
        subject_id: course.subject_id,
        subject_name: course.subject_name || "No Subject",
        class_id: course.class_id, // Changed classes_id to class_id to match backend
        duration: course.duration,
        start_date: course.start_date,
        end_date: course.end_date,
      }))
      console.log("Formatted courses:", formattedCourses)
      setCourses(formattedCourses)
    } catch (error) {
      console.error("Fetch courses error:", error)
      setCourses([])
    } finally {
      setLoading(false)
    }
  }

  const fetchTeachers = async () => {
    try {
      const response = await api.get("/showTeacher")
      const teachersData = response.data?.data || response.data || []
      setTeachers(Array.isArray(teachersData) ? teachersData : [])
    } catch (err) {
      console.error("Fetch teachers error:", err)
      setTeachers([])
    }
  }

  const fetchSubjects = async () => {
    try {
      const response = await api.get("/showSubjects")
      console.log("Subjects response:", response.data)
      let subjectsData = []
      if (response.data?.subjects && Array.isArray(response.data.subjects)) {
        subjectsData = response.data.subjects
      } else if (response.data?.data && Array.isArray(response.data.data)) {
        subjectsData = response.data.data
      } else if (Array.isArray(response.data)) {
        subjectsData = response.data
      } else {
        console.error("Invalid subjects data format:", response.data)
        setSubjects([])
        return
      }
      setSubjects(subjectsData)
    } catch (error) {
      console.error("Fetch subjects error:", error)
      setSubjects([])
    }
  }

  const fetchclassess = async () => {
    try {
      const response = await api.get("/showClasses")
      const classessData = response.data?.data || response.data || []
      setclassess(Array.isArray(classessData) ? classessData : [])
    } catch (err) {
      console.warn("classess endpoint not available:", err.message)
      setclassess([]) // Set empty array instead of throwing error
    }
  }

  const handleCreate = async (e) => {
    e.preventDefault()
    try {
      await api.post("/createCourse", formData)
      setShowModal(false)
      resetForm()
      fetchCourses()
      alert("Course created successfully!")
    } catch (err) {
      alert("Failed to create course: " + (err.response?.data?.error || err.message))
    }
  }

  const handleUpdate = async (e) => {
    e.preventDefault()
    try {
      const updateData = {
        ...formData,
        id: selectedCourse.id, // Added course id to formData for update
      }
      await api.post(`/updateCourse/${selectedCourse.id}`, updateData)
      setShowModal(false)
      resetForm()
      fetchCourses()
      alert("Course updated successfully!")
    } catch (err) {
      alert("Failed to update course: " + (err.response?.data?.error || err.message))
    }
  }

  const handleDelete = async (courseId) => {
    if (window.confirm("Are you sure you want to delete this course?")) {
      try {
        await api.delete(`/deleteCourse/${courseId}`)
        fetchCourses()
        alert("Course deleted successfully!")
      } catch (err) {
        alert("Failed to delete course: " + (err.response?.data?.error || err.message))
      }
    }
  }

  const openModal = (mode, course = null) => {
    setModalMode(mode)
    setSelectedCourse(course)
    if (course) {
      setFormData({
        title: course.title || "",
        description: course.description || "",
        teacher_id: course.teacher_id || "",
        subject_id: course.subject_id || "",
        class_id: course.class_id || "", // Changed classes_id to class_id to match backend
        duration: course.duration || "",
        start_date: course.start_date || "",
        end_date: course.end_date || "",
      })
    } else {
      resetForm()
    }
    setShowModal(true)
  }

  const resetForm = () => {
    setFormData({
      title: "",
      description: "",
      teacher_id: "",
      subject_id: "",
      class_id: "", // Changed classes_id to class_id to match backend
      duration: "",
      start_date: "",
      end_date: "",
    })
  }

  const filteredCourses = courses.filter(
    (course) =>
      (course.name || course.title)?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      course.teacher_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
      course.subject_name?.toLowerCase().includes(searchTerm.toLowerCase()),
  )

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Courses</h1>
          <p className="text-gray-600">Manage course curriculum and assignments</p>
        </div>
        <button
          onClick={() => openModal("create")}
          className="bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 hover:bg-blue-700"
        >
          <PlusIcon className="h-5 w-5" />
          <span>Add Course</span>
        </button>
      </div>

      {/* Search Bar */}
      <div className="relative">
        <MagnifyingGlassIcon className="h-5 w-5 absolute left-3 top-3 text-gray-400" />
        <input
          type="text"
          placeholder="Search courses..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      {/* Enhanced Courses Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredCourses.map((course) => (
          <div
            key={course.id}
            className="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden"
          >
            {/* Course Header */}
            <div className="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 text-white">
              <div className="flex justify-between items-start mb-2">
                <h3 className="text-lg font-bold truncate pr-2">{course.title}</h3>
                <div className="flex space-x-1">
                  <button
                    onClick={() => openModal("view", course)}
                    className="text-white/80 hover:text-white transition-colors p-1 rounded"
                  >
                    <EyeIcon className="h-4 w-4" />
                  </button>
                  <button
                    onClick={() => openModal("edit", course)}
                    className="text-white/80 hover:text-white transition-colors p-1 rounded"
                  >
                    <PencilIcon className="h-4 w-4" />
                  </button>
                  <button
                    onClick={() => handleDelete(course.id)}
                    className="text-white/80 hover:text-red-200 transition-colors p-1 rounded"
                  >
                    <TrashIcon className="h-4 w-4" />
                  </button>
                </div>
              </div>
              {course.description && <p className="text-sm text-white/90 line-clamp-2">{course.description}</p>}
            </div>

            {/* Course Content */}
            <div className="p-4 space-y-4">
              {/* Teacher Information */}
              <div className="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <UserIcon className="h-5 w-5 text-blue-600" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-xs font-medium text-blue-600 uppercase tracking-wide">Instructor</p>
                  <p className="text-sm font-semibold text-gray-900 truncate">{course.teacher_name}</p>
                </div>
              </div>

              {/* Subject & classes Information */}
              <div className="grid grid-cols-2 gap-3">
                <div className="flex items-center space-x-2 p-2 bg-green-50 rounded-lg">
                  <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <BookOpenIcon className="h-4 w-4 text-green-600" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-xs font-medium text-green-600 uppercase tracking-wide">Subject</p>
                    <p className="text-sm font-semibold text-gray-900 truncate">{course.subject_name}</p>
                  </div>
                </div>

                <div className="flex items-center space-x-2 p-2 bg-purple-50 rounded-lg">
                  <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <HomeIcon className="h-4 w-4 text-purple-600" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-xs font-medium text-purple-600 uppercase tracking-wide">Class</p>
                    <p className="text-sm font-semibold text-gray-900 truncate">{course.class_number}</p>
                  </div>
                </div>
              </div>

              {/* Duration */}
              <div className="grid grid-cols-2 gap-3">
                <div className="flex items-center space-x-2 p-2 bg-orange-50 rounded-lg">
                  <div className="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                    <ClockIcon className="h-4 w-4 text-orange-600" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-xs font-medium text-orange-600 uppercase tracking-wide">Duration</p>
                    <p className="text-sm font-semibold text-gray-900">{course.duration}h</p>
                  </div>
                </div>
              </div>

              {/* Schedule Information */}
              <div className="p-3 bg-gray-50 rounded-lg">
                <div className="flex items-center space-x-2 mb-2">
                  <CalendarDaysIcon className="h-4 w-4 text-gray-600" />
                  <p className="text-xs font-medium text-gray-600 uppercase tracking-wide">Schedule</p>
                </div>
                <div className="space-y-1">
                  <div className="flex justify-between items-center text-sm">
                    <span className="text-gray-600">Start Date:</span>
                    <span className="font-medium text-gray-900">
                      {course.start_date
                        ? new Date(course.start_date).toLocaleDateString("en-US", {
                            month: "short",
                            day: "numeric",
                            year: "numeric",
                          })
                        : "TBD"}
                    </span>
                  </div>
                  <div className="flex justify-between items-center text-sm">
                    <span className="text-gray-600">End Date:</span>
                    <span className="font-medium text-gray-900">
                      {course.end_date
                        ? new Date(course.end_date).toLocaleDateString("en-US", {
                            month: "short",
                            day: "numeric",
                            year: "numeric",
                          })
                        : "TBD"}
                    </span>
                  </div>
                </div>
              </div>

              {/* Course Status Badge */}
              <div className="flex justify-center">
                <span
                  className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                    course.start_date && new Date(course.start_date) > new Date()
                      ? "bg-yellow-100 text-yellow-800"
                      : course.end_date && new Date(course.end_date) < new Date()
                        ? "bg-gray-100 text-gray-800"
                        : "bg-green-100 text-green-800"
                  }`}
                >
                  {course.start_date && new Date(course.start_date) > new Date()
                    ? "Upcoming"
                    : course.end_date && new Date(course.end_date) < new Date()
                      ? "Completed"
                      : "Active"}
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>

      {filteredCourses.length === 0 && (
        <div className="text-center py-12">
          <p className="text-gray-500">No courses found</p>
        </div>
      )}

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div className="mt-3">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                {modalMode === "create" ? "Add New Course" : modalMode === "edit" ? "Edit Course" : "Course Details"}
              </h3>
              <form onSubmit={modalMode === "create" ? handleCreate : handleUpdate}>
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-700">Course Title</label>
                    <input
                      type="text"
                      value={formData.title}
                      onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                      disabled={modalMode === "view"}
                      className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      required
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                      value={formData.description}
                      onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                      disabled={modalMode === "view"}
                      className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      rows="3"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">Teacher</label>
                    <select
                      value={formData.teacher_id}
                      onChange={(e) => setFormData({ ...formData, teacher_id: e.target.value })}
                      disabled={modalMode === "view"}
                      className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      required
                    >
                      <option value="">Select a teacher</option>
                      {teachers.map((teacher) => (
                        <option key={teacher.id} value={teacher.id}>
                          {teacher.first_name} {teacher.last_name} - {teacher.specialite}
                        </option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">Subject</label>
                    <select
                      value={formData.subject_id}
                      onChange={(e) => setFormData({ ...formData, subject_id: e.target.value })}
                      disabled={modalMode === "view"}
                      className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      required
                    >
                      <option value="">Select a subject</option>
                      {subjects.map((subject) => (
                        <option key={subject.id} value={subject.id}>
                          {subject.name || subject.title}
                        </option>
                      ))}
                    </select>
                  </div>
                  {classess.length > 0 && (
                    <div>
                      <label className="block text-sm font-medium text-gray-700">Class</label>
                      <select
                        value={formData.class_id}
                        onChange={(e) => setFormData({ ...formData, class_id: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">Select a class</option>
                        {classess.map((classes) => (
                          <option key={classes.id} value={classes.id}>
                            {classes.number || classes.name}
                          </option>
                        ))}
                      </select>
                    </div>
                  )}
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700">Duration (hours)</label>
                      <input
                        type="number"
                        value={formData.duration}
                        onChange={(e) => setFormData({ ...formData, duration: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        min="1"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700">Class</label>
                      <select
                        value={formData.class_id}
                        onChange={(e) => setFormData({ ...formData, class_id: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      >
                        <option value="">Select a class</option>
                        {classess.map((classes) => (
                          <option key={classes.id} value={classes.id}>
                            {classes.number || classes.name}
                          </option>
                        ))}
                      </select>
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700">Start Date</label>
                      <input
                        type="date"
                        value={formData.start_date}
                        onChange={(e) => setFormData({ ...formData, start_date: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700">End Date</label>
                      <input
                        type="date"
                        value={formData.end_date}
                        onChange={(e) => setFormData({ ...formData, end_date: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      />
                    </div>
                  </div>
                </div>
                <div className="flex justify-end space-x-3 mt-6">
                  <button
                    type="button"
                    onClick={() => setShowModal(false)}
                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
                  >
                    {modalMode === "view" ? "Close" : "Cancel"}
                  </button>
                  {modalMode !== "view" && (
                    <button
                      type="submit"
                      className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                    >
                      {modalMode === "create" ? "Create" : "Update"}
                    </button>
                  )}
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}

export default CourseList
