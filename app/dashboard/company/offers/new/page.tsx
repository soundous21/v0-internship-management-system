"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Briefcase,
  ArrowLeft,
  Plus,
  X,
  Save,
  Eye
} from "lucide-react"
import Link from "next/link"

const availableSkills = [
  "React", "Vue.js", "Angular", "TypeScript", "JavaScript", "Python",
  "Java", "Node.js", "Express", "Django", "FastAPI", "Laravel",
  "MongoDB", "PostgreSQL", "MySQL", "Docker", "Git", "Linux",
  "AWS", "React Native", "Flutter", "TensorFlow", "Machine Learning",
  "Figma", "UI Design", "Prototyping"
]

const wilayas = ["Algiers", "Oran", "Constantine", "Annaba", "Setif", "Blida", "Tizi Ouzou"]
const durations = ["1 month", "2 months", "3 months", "4 months", "6 months"]
const types = ["Full-time", "Part-time", "Remote", "Hybrid"]

export default function NewOfferPage() {
  const router = useRouter()
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    requirements: "",
    duration: "",
    type: "",
    wilaya: "",
    deadline: ""
  })
  const [requiredSkills, setRequiredSkills] = useState<string[]>([])
  const [newSkill, setNewSkill] = useState("")
  const [showSkillDropdown, setShowSkillDropdown] = useState(false)

  const addSkill = (skill: string) => {
    if (skill && !requiredSkills.includes(skill)) {
      setRequiredSkills([...requiredSkills, skill])
    }
    setNewSkill("")
    setShowSkillDropdown(false)
  }

  const removeSkill = (skill: string) => {
    setRequiredSkills(requiredSkills.filter(s => s !== skill))
  }

  const filteredSkills = availableSkills.filter(
    skill => skill.toLowerCase().includes(newSkill.toLowerCase()) && !requiredSkills.includes(skill)
  )

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Handle form submission
    router.push("/dashboard/company/offers")
  }

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <Button variant="ghost" size="sm" className="mb-4" asChild>
            <Link href="/dashboard/company/offers">
              <ArrowLeft className="mr-2 h-4 w-4" />
              Back to Offers
            </Link>
          </Button>
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Create New Offer</h1>
          <p className="mt-1 text-muted-foreground">Post a new internship position for students</p>
        </div>

        <form onSubmit={handleSubmit}>
          <div className="grid gap-6 lg:grid-cols-3">
            {/* Main Form */}
            <div className="lg:col-span-2 space-y-6">
              {/* Basic Information */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Briefcase className="h-5 w-5" />
                    Position Details
                  </CardTitle>
                  <CardDescription>Basic information about the internship</CardDescription>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="space-y-2">
                    <Label htmlFor="title">Position Title *</Label>
                    <Input
                      id="title"
                      placeholder="e.g., Frontend Developer Intern"
                      value={formData.title}
                      onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                      required
                    />
                  </div>
                  
                  <div className="space-y-2">
                    <Label htmlFor="description">Description *</Label>
                    <Textarea
                      id="description"
                      placeholder="Describe the internship position, responsibilities, and what the intern will learn..."
                      value={formData.description}
                      onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                      rows={5}
                      required
                    />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="requirements">Requirements</Label>
                    <Textarea
                      id="requirements"
                      placeholder="List the requirements and qualifications needed..."
                      value={formData.requirements}
                      onChange={(e) => setFormData({ ...formData, requirements: e.target.value })}
                      rows={3}
                    />
                  </div>

                  <div className="grid gap-4 sm:grid-cols-2">
                    <div className="space-y-2">
                      <Label>Duration *</Label>
                      <Select
                        value={formData.duration}
                        onValueChange={(value) => setFormData({ ...formData, duration: value })}
                      >
                        <SelectTrigger>
                          <SelectValue placeholder="Select duration" />
                        </SelectTrigger>
                        <SelectContent>
                          {durations.map((d) => (
                            <SelectItem key={d} value={d}>{d}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="space-y-2">
                      <Label>Type *</Label>
                      <Select
                        value={formData.type}
                        onValueChange={(value) => setFormData({ ...formData, type: value })}
                      >
                        <SelectTrigger>
                          <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                          {types.map((t) => (
                            <SelectItem key={t} value={t}>{t}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                  </div>

                  <div className="grid gap-4 sm:grid-cols-2">
                    <div className="space-y-2">
                      <Label>Location (Wilaya) *</Label>
                      <Select
                        value={formData.wilaya}
                        onValueChange={(value) => setFormData({ ...formData, wilaya: value })}
                      >
                        <SelectTrigger>
                          <SelectValue placeholder="Select location" />
                        </SelectTrigger>
                        <SelectContent>
                          {wilayas.map((w) => (
                            <SelectItem key={w} value={w}>{w}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="deadline">Application Deadline *</Label>
                      <Input
                        id="deadline"
                        type="date"
                        value={formData.deadline}
                        onChange={(e) => setFormData({ ...formData, deadline: e.target.value })}
                        required
                      />
                    </div>
                  </div>
                </CardContent>
              </Card>

              {/* Required Skills */}
              <Card>
                <CardHeader>
                  <CardTitle>Required Skills</CardTitle>
                  <CardDescription>Add technical skills required for this position</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {/* Current Skills */}
                    {requiredSkills.length > 0 && (
                      <div className="flex flex-wrap gap-2">
                        {requiredSkills.map((skill) => (
                          <Badge
                            key={skill}
                            variant="secondary"
                            className="flex items-center gap-1 pr-1"
                          >
                            {skill}
                            <button
                              type="button"
                              onClick={() => removeSkill(skill)}
                              className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                            >
                              <X className="h-3 w-3" />
                            </button>
                          </Badge>
                        ))}
                      </div>
                    )}

                    {/* Add Skill */}
                    <div className="relative">
                      <div className="flex gap-2">
                        <Input
                          placeholder="Add a required skill..."
                          value={newSkill}
                          onChange={(e) => {
                            setNewSkill(e.target.value)
                            setShowSkillDropdown(true)
                          }}
                          onFocus={() => setShowSkillDropdown(true)}
                          onKeyDown={(e) => {
                            if (e.key === "Enter") {
                              e.preventDefault()
                              if (newSkill) addSkill(newSkill)
                            }
                          }}
                        />
                        <Button
                          type="button"
                          size="icon"
                          variant="outline"
                          onClick={() => addSkill(newSkill)}
                          disabled={!newSkill}
                        >
                          <Plus className="h-4 w-4" />
                        </Button>
                      </div>
                      
                      {showSkillDropdown && filteredSkills.length > 0 && newSkill && (
                        <div className="absolute top-full left-0 right-12 z-10 mt-1 max-h-48 overflow-auto rounded-md border border-border bg-popover p-1 shadow-md">
                          {filteredSkills.slice(0, 8).map((skill) => (
                            <button
                              key={skill}
                              type="button"
                              className="w-full rounded-sm px-2 py-1.5 text-left text-sm hover:bg-accent hover:text-accent-foreground"
                              onClick={() => addSkill(skill)}
                            >
                              {skill}
                            </button>
                          ))}
                        </div>
                      )}
                    </div>

                    <p className="text-xs text-muted-foreground">
                      Popular: React, Python, Node.js, Java, Docker
                    </p>
                  </div>
                </CardContent>
              </Card>
            </div>

            {/* Preview & Actions */}
            <div className="space-y-6">
              <Card className="sticky top-6">
                <CardHeader>
                  <CardTitle className="text-base">Preview</CardTitle>
                  <CardDescription>How students will see your offer</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="rounded-lg border border-border p-4">
                    <h3 className="font-semibold text-foreground">
                      {formData.title || "Position Title"}
                    </h3>
                    <p className="mt-1 text-sm text-muted-foreground">
                      TechCorp Algeria
                    </p>
                    <div className="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                      {formData.wilaya && <span>{formData.wilaya}</span>}
                      {formData.duration && <span>• {formData.duration}</span>}
                      {formData.type && <span>• {formData.type}</span>}
                    </div>
                    {requiredSkills.length > 0 && (
                      <div className="mt-3 flex flex-wrap gap-1">
                        {requiredSkills.slice(0, 4).map((skill) => (
                          <Badge key={skill} variant="outline" className="text-xs">
                            {skill}
                          </Badge>
                        ))}
                        {requiredSkills.length > 4 && (
                          <Badge variant="outline" className="text-xs">
                            +{requiredSkills.length - 4}
                          </Badge>
                        )}
                      </div>
                    )}
                  </div>

                  <div className="mt-6 space-y-2">
                    <Button type="submit" className="w-full">
                      <Save className="mr-2 h-4 w-4" />
                      Publish Offer
                    </Button>
                    <Button type="button" variant="outline" className="w-full">
                      <Eye className="mr-2 h-4 w-4" />
                      Save as Draft
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </form>
      </div>
    </div>
  )
}
