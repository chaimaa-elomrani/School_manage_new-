import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Textarea } from "@/components/ui/textarea"
import { Input } from "@/components/ui/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { useState } from "react"

export function Communication({ data }) {
  const { messages = [], announcements = [], teachers = [] } = data
  const [messageForm, setMessageForm] = useState({
    recipient: "",
    subject: "",
    content: ""
  })
  
  const handleInputChange = (e) => {
    const { name, value } = e.target
    setMessageForm(prev => ({
      ...prev,
      [name]: value
    }))
  }
  
  const handleSelectChange = (value) => {
    setMessageForm(prev => ({
      ...prev,
      recipient: value
    }))
  }
  
  const handleSubmit = (e) => {
    e.preventDefault()
    // Here you would implement the API call to send the message
    console.log("Sending message:", messageForm)
    alert("Message sending functionality will be implemented in the next phase")
    
    // Reset form
    setMessageForm({
      recipient: "",
      subject: "",
      content: ""
    })
  }
  
  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Messages</CardTitle>
          </CardHeader>
          <CardContent>
            {messages && messages.length > 0 ? (
              <div className="space-y-3">
                {messages.map((message, i) => (
                  <div key={i} className="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium text-gray-900">{message.sender_name}</p>
                      <p className="text-xs text-gray-500">
                        {new Date(message.created_at).toLocaleDateString()}
                      </p>
                    </div>
                    <p className="text-sm text-gray-700">{message.subject}</p>
                    <p className="text-xs text-gray-500 mt-1">{message.preview || message.content}</p>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-gray-500 text-center py-4">No messages available</p>
            )}
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader>
            <CardTitle>School Announcements</CardTitle>
          </CardHeader>
          <CardContent>
            {announcements && announcements.length > 0 ? (
              <div className="space-y-3">
                {announcements.map((announcement, i) => (
                  <div key={i} className="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div className="flex items-center justify-between mb-2">
                      <p className="text-sm font-medium text-blue-900">{announcement.title}</p>
                      <p className="text-xs text-blue-600">
                        {new Date(announcement.created_at).toLocaleDateString()}
                      </p>
                    </div>
                    <p className="text-sm text-blue-800">{announcement.content}</p>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-gray-500 text-center py-4">No announcements available</p>
            )}
          </CardContent>
        </Card>
      </div>
      
      <Card>
        <CardHeader>
          <CardTitle>Send Message</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-2">
              <label className="text-sm font-medium">Recipient</label>
              <Select value={messageForm.recipient} onValueChange={handleSelectChange}>
                <SelectTrigger>
                  <SelectValue placeholder="Select recipient" />
                </SelectTrigger>
                <SelectContent>
                  {teachers && teachers.map((teacher, i) => (
                    <SelectItem key={i} value={teacher.id.toString()}>
                      {teacher.first_name} {teacher.last_name} ({teacher.subject_name})
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            
            <div className="space-y-2">
              <label className="text-sm font-medium">Subject</label>
              <Input 
                name="subject"
                value={messageForm.subject}
                onChange={handleInputChange}
                placeholder="Message subject"
              />
            </div>
            
            <div className="space-y-2">
              <label className="text-sm font-medium">Message</label>
              <Textarea 
                name="content"
                value={messageForm.content}
                onChange={handleInputChange}
                placeholder="Type your message here"
                rows={5}
              />
            </div>
            
            <div className="flex justify-end">
              <Button type="submit">Send Message</Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
