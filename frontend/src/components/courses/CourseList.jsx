"use client"

import { useState, useEffect } from "react"
import api from "../../services/api"
import { PlusIcon, PencilIcon, TrashIcon, MagnifyingGlassIcon, EyeIcon } from "@heroicons/react/24/outline"

const CourseList = () => {
  const [courses, setCourses] = useState([])
  const [teachers, setTeachers] = useState([])
  const [subjects, setSubjects] = useState([])
  const [rooms, setRooms] = useState([]) // Added missing rooms state
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
    room_id: "", // Added room_id to formData
    duration: "",
    level: "",
    start_date: "",
    end_date: "",
  })

  useEffect(() => {
    fetchCourses()
    fetchTeachers()
    fetchSubjects()
    fetchRooms()
  }, [])

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
        room_id: course.room_id,
        room_number: course.room_number || "No Room",
        duration: course.duration,
        level: course.level,
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

  const fetchRooms = async () => {
    try {
      const response = await api.get("/showRooms")
      const roomsData = response.data?.data || response.data || []
      setRooms(Array.isArray(roomsData) ? roomsData : [])
    } catch (err) {
      console.warn("Rooms endpoint not available:", err.message)
      setRooms([]) // Set empty array instead of throwing error
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
      await api.post(`/updateCourse/${selectedCourse.id}`, formData)
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
        room_id: course.room_id || "",
        duration: course.duration || "",
        level: course.level || "",
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
      room_id: "",
      duration: "",
      level: "",
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

      {/* Courses Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredCourses.map((course) => (
          <div
            key={course.id}
            className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6"
          >
            <div className="flex justify-between items-start mb-4">
              <h3 className="text-xl font-bold text-gray-900 truncate">{course.title}</h3>
              <div className="flex space-x-2">
                <button
                  onClick={() => openModal("view", course)}
                  className="text-blue-600 hover:text-blue-800 transition-colors"
                >
                  <EyeIcon className="h-5 w-5" />
                </button>
                <button
                  onClick={() => openModal("edit", course)}
                  className="text-indigo-600 hover:text-indigo-800 transition-colors"
                >
                  <PencilIcon className="h-5 w-5" />
                </button>
                <button
                  onClick={() => handleDelete(course.id)}
                  className="text-red-600 hover:text-red-800 transition-colors"
                >
                  <TrashIcon className="h-5 w-5" />
                </button>
              </div>
            </div>

            <div className="space-y-3">
              <div className="flex items-center space-x-2">
                <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-blue-600 text-sm font-medium">
                    {course.teacher_name ? course.teacher_name.substring(0, 2).toUpperCase() : "NA"}
                  </span>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-900">Teacher</p>
                  <p className="text-sm text-gray-600">{course.teacher_name}</p>
                </div>
              </div>

              <div className="flex items-center space-x-2">
                <div className="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <span className="text-green-600 text-sm font-medium">S</span>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-900">Subject</p>
                  <p className="text-sm text-gray-600">{course.subject_name}</p>
                </div>
              </div>

              <div className="flex items-center space-x-2">
                <div className="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                  <span className="text-purple-600 text-sm font-medium">R</span>
                </div>
                <div>
                  <p className="text-sm font-medium text-gray-900">Room</p>
                  <p className="text-sm text-gray-600">{course.room_number}</p>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4 pt-3 border-t border-gray-100">
                <div>
                  <p className="text-xs font-medium text-gray-500 uppercase tracking-wide">Duration</p>
                  <p className="text-sm font-semibold text-gray-900">{course.duration} hours</p>
                </div>
                <div>
                  <p className="text-xs font-medium text-gray-500 uppercase tracking-wide">Level</p>
                  <p className="text-sm font-semibold text-gray-900">{course.level || "Not specified"}</p>
                </div>
              </div>

              <div className="pt-3 border-t border-gray-100">
                <div className="flex justify-between items-center mb-2">
                  <p className="text-xs font-medium text-gray-500 uppercase tracking-wide">Schedule</p>
                </div>
                <div className="space-y-1">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Start:</span>
                    <span className="font-medium text-gray-900">
                      {course.start_date ? new Date(course.start_date).toLocaleDateString() : "TBD"}
                    </span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">End:</span>
                    <span className="font-medium text-gray-900">
                      {course.end_date ? new Date(course.end_date).toLocaleDateString() : "TBD"}
                    </span>
                  </div>
                </div>
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
                          {teacher.nom} {teacher.prenom} - {teacher.specialite}
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

                  {rooms.length > 0 && (
                    <div>
                      <label className="block text-sm font-medium text-gray-700">Room</label>
                      <select
                        value={formData.room_id}
                        onChange={(e) => setFormData({ ...formData, room_id: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">Select a room</option>
                        {rooms.map((room) => (
                          <option key={room.id} value={room.id}>
                            {room.number || room.name}
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
                      <label className="block text-sm font-medium text-gray-700">Level</label>
                      <select
                        value={formData.level}
                        onChange={(e) => setFormData({ ...formData, level: e.target.value })}
                        disabled={modalMode === "view"}
                        className="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                      >
                        <option value="">Select level</option>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
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
