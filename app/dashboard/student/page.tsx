"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Progress } from "@/components/ui/progress"
import {
  Briefcase,
  Clock,
  CheckCircle2,
  XCircle,
  ArrowRight,
  MapPin,
  Building2,
  Calendar
} from "lucide-react"
import Link from "next/link"

// Mock data
const stats = [
  { label: "Applications Sent", value: 12, icon: Briefcase, color: "text-primary" },
  { label: "Pending Review", value: 5, icon: Clock, color: "text-yellow-500" },
  { label: "Accepted", value: 2, icon: CheckCircle2, color: "text-accent" },
  { label: "Declined", value: 3, icon: XCircle, color: "text-destructive" },
]

const recommendedOffers = [
  {
    id: 1,
    title: "Frontend Developer Intern",
    company: "TechCorp Algeria",
    location: "Algiers",
    type: "6 months",
    tags: ["React", "TypeScript", "Tailwind"],
    matchScore: 95,
    postedAt: "2 days ago"
  },
  {
    id: 2,
    title: "Full Stack Developer",
    company: "StartUp DZ",
    location: "Oran",
    type: "3 months",
    tags: ["Node.js", "React", "MongoDB"],
    matchScore: 88,
    postedAt: "1 week ago"
  },
  {
    id: 3,
    title: "Mobile App Developer",
    company: "Mobile Solutions",
    location: "Constantine",
    type: "4 months",
    tags: ["React Native", "JavaScript"],
    matchScore: 82,
    postedAt: "3 days ago"
  },
]

const recentApplications = [
  {
    id: 1,
    position: "Backend Developer Intern",
    company: "DataTech",
    status: "pending",
    appliedAt: "March 15, 2026"
  },
  {
    id: 2,
    position: "DevOps Intern",
    company: "CloudHost DZ",
    status: "accepted",
    appliedAt: "March 10, 2026"
  },
  {
    id: 3,
    position: "UI/UX Designer Intern",
    company: "DesignStudio",
    status: "rejected",
    appliedAt: "March 5, 2026"
  },
]

const skills = ["React", "TypeScript", "Node.js", "Python", "MongoDB", "Git"]

export default function StudentDashboard() {
  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Welcome back, Ahmed</h1>
          <p className="mt-1 text-muted-foreground">Track your internship applications and discover new opportunities</p>
        </div>

        {/* Stats Grid */}
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {stats.map((stat) => (
            <Card key={stat.label}>
              <CardContent className="flex items-center gap-4 p-6">
                <div className={`rounded-lg bg-muted p-3 ${stat.color}`}>
                  <stat.icon className="h-6 w-6" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">{stat.value}</p>
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        <div className="mt-8 grid gap-8 lg:grid-cols-3">
          {/* Recommended Offers */}
          <div className="lg:col-span-2">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between">
                <div>
                  <CardTitle>Recommended for You</CardTitle>
                  <CardDescription>Based on your skills and preferences</CardDescription>
                </div>
                <Button variant="ghost" size="sm" asChild>
                  <Link href="/dashboard/student/offers">
                    View All <ArrowRight className="ml-1 h-4 w-4" />
                  </Link>
                </Button>
              </CardHeader>
              <CardContent className="space-y-4">
                {recommendedOffers.map((offer) => (
                  <div
                    key={offer.id}
                    className="flex flex-col gap-4 rounded-lg border border-border p-4 transition-colors hover:border-primary/50 sm:flex-row sm:items-center sm:justify-between"
                  >
                    <div className="flex-1">
                      <div className="flex items-start justify-between">
                        <div>
                          <h3 className="font-semibold text-foreground">{offer.title}</h3>
                          <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
                            <span className="flex items-center gap-1">
                              <Building2 className="h-3.5 w-3.5" />
                              {offer.company}
                            </span>
                            <span className="flex items-center gap-1">
                              <MapPin className="h-3.5 w-3.5" />
                              {offer.location}
                            </span>
                            <span className="flex items-center gap-1">
                              <Calendar className="h-3.5 w-3.5" />
                              {offer.type}
                            </span>
                          </div>
                        </div>
                        <Badge variant="secondary" className="bg-accent/10 text-accent">
                          {offer.matchScore}% Match
                        </Badge>
                      </div>
                      <div className="mt-3 flex flex-wrap gap-2">
                        {offer.tags.map((tag) => (
                          <Badge key={tag} variant="outline" className="text-xs">
                            {tag}
                          </Badge>
                        ))}
                      </div>
                    </div>
                    <Button size="sm" className="shrink-0">
                      Apply Now
                    </Button>
                  </div>
                ))}
              </CardContent>
            </Card>
          </div>

          {/* Right Column */}
          <div className="space-y-6">
            {/* Profile Completion */}
            <Card>
              <CardHeader>
                <CardTitle className="text-base">Profile Completion</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-3">
                  <div className="flex items-center justify-between text-sm">
                    <span className="text-muted-foreground">75% Complete</span>
                    <span className="font-medium text-primary">3 items left</span>
                  </div>
                  <Progress value={75} className="h-2" />
                  <p className="text-xs text-muted-foreground">
                    Add your GitHub link and portfolio to improve visibility
                  </p>
                  <Button variant="outline" size="sm" className="w-full" asChild>
                    <Link href="/dashboard/student/profile">Complete Profile</Link>
                  </Button>
                </div>
              </CardContent>
            </Card>

            {/* Skills */}
            <Card>
              <CardHeader>
                <CardTitle className="text-base">Your Skills</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex flex-wrap gap-2">
                  {skills.map((skill) => (
                    <Badge key={skill} variant="secondary">
                      {skill}
                    </Badge>
                  ))}
                </div>
                <Button variant="link" size="sm" className="mt-3 h-auto p-0 text-primary" asChild>
                  <Link href="/dashboard/student/profile">Edit Skills</Link>
                </Button>
              </CardContent>
            </Card>

            {/* Recent Applications */}
            <Card>
              <CardHeader>
                <CardTitle className="text-base">Recent Applications</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                {recentApplications.map((app) => (
                  <div key={app.id} className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-foreground">{app.position}</p>
                      <p className="text-xs text-muted-foreground">{app.company}</p>
                    </div>
                    <Badge
                      variant={
                        app.status === "accepted"
                          ? "default"
                          : app.status === "pending"
                          ? "secondary"
                          : "destructive"
                      }
                      className={
                        app.status === "accepted"
                          ? "bg-accent text-accent-foreground"
                          : ""
                      }
                    >
                      {app.status}
                    </Badge>
                  </div>
                ))}
                <Button variant="link" size="sm" className="h-auto p-0 text-primary" asChild>
                  <Link href="/dashboard/student/applications">View All Applications</Link>
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  )
}
