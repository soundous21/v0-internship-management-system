"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import {
  User,
  Mail,
  Phone,
  MapPin,
  GraduationCap,
  Github,
  Globe,
  Linkedin,
  Plus,
  X,
  Save
} from "lucide-react"

const availableSkills = [
  "React", "Vue.js", "Angular", "TypeScript", "JavaScript", "Python",
  "Java", "Node.js", "Express", "Django", "FastAPI", "Laravel",
  "MongoDB", "PostgreSQL", "MySQL", "Docker", "Git", "Linux",
  "AWS", "React Native", "Flutter", "TensorFlow", "Machine Learning"
]

export default function StudentProfilePage() {
  const [profile, setProfile] = useState({
    firstName: "Ahmed",
    lastName: "Benali",
    email: "ahmed.benali@univ.edu",
    phone: "+213 555 123 456",
    wilaya: "Algiers",
    university: "USTHB",
    department: "Computer Science",
    level: "L3",
    bio: "Passionate computer science student specializing in web development. Looking for an internship opportunity to apply my skills in a real-world environment.",
    github: "https://github.com/ahmedbenali",
    portfolio: "https://ahmed-portfolio.dev",
    linkedin: "https://linkedin.com/in/ahmedbenali"
  })

  const [skills, setSkills] = useState(["React", "TypeScript", "Node.js", "Python", "MongoDB", "Git"])
  const [newSkill, setNewSkill] = useState("")
  const [showSkillDropdown, setShowSkillDropdown] = useState(false)

  const addSkill = (skill: string) => {
    if (skill && !skills.includes(skill)) {
      setSkills([...skills, skill])
    }
    setNewSkill("")
    setShowSkillDropdown(false)
  }

  const removeSkill = (skill: string) => {
    setSkills(skills.filter(s => s !== skill))
  }

  const filteredSkills = availableSkills.filter(
    skill => skill.toLowerCase().includes(newSkill.toLowerCase()) && !skills.includes(skill)
  )

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8 flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground lg:text-3xl">My Profile</h1>
            <p className="mt-1 text-muted-foreground">Manage your digital CV and profile settings</p>
          </div>
          <Button>
            <Save className="mr-2 h-4 w-4" />
            Save Changes
          </Button>
        </div>

        <div className="grid gap-6 lg:grid-cols-3">
          {/* Main Profile Info */}
          <div className="lg:col-span-2 space-y-6">
            {/* Personal Information */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <User className="h-5 w-5" />
                  Personal Information
                </CardTitle>
                <CardDescription>Your basic contact information</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="firstName">First Name</Label>
                    <Input
                      id="firstName"
                      value={profile.firstName}
                      onChange={(e) => setProfile({ ...profile, firstName: e.target.value })}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="lastName">Last Name</Label>
                    <Input
                      id="lastName"
                      value={profile.lastName}
                      onChange={(e) => setProfile({ ...profile, lastName: e.target.value })}
                    />
                  </div>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="email">
                      <Mail className="mr-1 inline h-4 w-4" />
                      Email
                    </Label>
                    <Input
                      id="email"
                      type="email"
                      value={profile.email}
                      onChange={(e) => setProfile({ ...profile, email: e.target.value })}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="phone">
                      <Phone className="mr-1 inline h-4 w-4" />
                      Phone
                    </Label>
                    <Input
                      id="phone"
                      value={profile.phone}
                      onChange={(e) => setProfile({ ...profile, phone: e.target.value })}
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="wilaya">
                    <MapPin className="mr-1 inline h-4 w-4" />
                    Wilaya
                  </Label>
                  <Input
                    id="wilaya"
                    value={profile.wilaya}
                    onChange={(e) => setProfile({ ...profile, wilaya: e.target.value })}
                  />
                </div>
              </CardContent>
            </Card>

            {/* Academic Information */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <GraduationCap className="h-5 w-5" />
                  Academic Information
                </CardTitle>
                <CardDescription>Your university and department details</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="university">University</Label>
                    <Input
                      id="university"
                      value={profile.university}
                      onChange={(e) => setProfile({ ...profile, university: e.target.value })}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="department">Department</Label>
                    <Input
                      id="department"
                      value={profile.department}
                      onChange={(e) => setProfile({ ...profile, department: e.target.value })}
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label htmlFor="level">Current Level</Label>
                  <Input
                    id="level"
                    value={profile.level}
                    onChange={(e) => setProfile({ ...profile, level: e.target.value })}
                    placeholder="e.g., L3, M1, M2"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="bio">Bio / About Me</Label>
                  <Textarea
                    id="bio"
                    value={profile.bio}
                    onChange={(e) => setProfile({ ...profile, bio: e.target.value })}
                    rows={4}
                    placeholder="Tell companies about yourself..."
                  />
                </div>
              </CardContent>
            </Card>

            {/* Links */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Globe className="h-5 w-5" />
                  Links & Portfolio
                </CardTitle>
                <CardDescription>Add your online presence</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="github">
                    <Github className="mr-1 inline h-4 w-4" />
                    GitHub Profile
                  </Label>
                  <Input
                    id="github"
                    value={profile.github}
                    onChange={(e) => setProfile({ ...profile, github: e.target.value })}
                    placeholder="https://github.com/username"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="portfolio">
                    <Globe className="mr-1 inline h-4 w-4" />
                    Portfolio Website
                  </Label>
                  <Input
                    id="portfolio"
                    value={profile.portfolio}
                    onChange={(e) => setProfile({ ...profile, portfolio: e.target.value })}
                    placeholder="https://yourportfolio.com"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="linkedin">
                    <Linkedin className="mr-1 inline h-4 w-4" />
                    LinkedIn Profile
                  </Label>
                  <Input
                    id="linkedin"
                    value={profile.linkedin}
                    onChange={(e) => setProfile({ ...profile, linkedin: e.target.value })}
                    placeholder="https://linkedin.com/in/username"
                  />
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Sidebar - Skills */}
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle>Technical Skills</CardTitle>
                <CardDescription>Add tags to help companies find you</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {/* Current Skills */}
                  <div className="flex flex-wrap gap-2">
                    {skills.map((skill) => (
                      <Badge
                        key={skill}
                        variant="secondary"
                        className="flex items-center gap-1 pr-1"
                      >
                        {skill}
                        <button
                          onClick={() => removeSkill(skill)}
                          className="ml-1 rounded-full p-0.5 hover:bg-muted-foreground/20"
                        >
                          <X className="h-3 w-3" />
                        </button>
                      </Badge>
                    ))}
                  </div>

                  {/* Add Skill */}
                  <div className="relative">
                    <div className="flex gap-2">
                      <Input
                        placeholder="Add a skill..."
                        value={newSkill}
                        onChange={(e) => {
                          setNewSkill(e.target.value)
                          setShowSkillDropdown(true)
                        }}
                        onFocus={() => setShowSkillDropdown(true)}
                        onKeyDown={(e) => {
                          if (e.key === "Enter" && newSkill) {
                            addSkill(newSkill)
                          }
                        }}
                      />
                      <Button
                        size="icon"
                        variant="outline"
                        onClick={() => addSkill(newSkill)}
                        disabled={!newSkill}
                      >
                        <Plus className="h-4 w-4" />
                      </Button>
                    </div>
                    
                    {/* Suggestions Dropdown */}
                    {showSkillDropdown && filteredSkills.length > 0 && newSkill && (
                      <div className="absolute top-full left-0 right-12 z-10 mt-1 max-h-48 overflow-auto rounded-md border border-border bg-popover p-1 shadow-md">
                        {filteredSkills.slice(0, 8).map((skill) => (
                          <button
                            key={skill}
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

            {/* Profile Preview Card */}
            <Card className="border-primary/50">
              <CardHeader>
                <CardTitle className="text-base">Profile Preview</CardTitle>
                <CardDescription>How companies will see you</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="text-center">
                  <div className="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <span className="text-xl font-bold">
                      {profile.firstName[0]}{profile.lastName[0]}
                    </span>
                  </div>
                  <h3 className="mt-3 font-semibold text-foreground">
                    {profile.firstName} {profile.lastName}
                  </h3>
                  <p className="text-sm text-muted-foreground">
                    {profile.level} - {profile.department}
                  </p>
                  <p className="text-sm text-muted-foreground">{profile.university}</p>
                  
                  <div className="mt-4 flex flex-wrap justify-center gap-1">
                    {skills.slice(0, 4).map((skill) => (
                      <Badge key={skill} variant="outline" className="text-xs">
                        {skill}
                      </Badge>
                    ))}
                    {skills.length > 4 && (
                      <Badge variant="outline" className="text-xs">
                        +{skills.length - 4}
                      </Badge>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  )
}
