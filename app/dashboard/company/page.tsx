"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import {
  Briefcase,
  Users,
  FileCheck,
  Clock,
  ArrowRight,
  Eye,
  CheckCircle,
  XCircle
} from "lucide-react"
import Link from "next/link"

const stats = [
  { label: "Active Offers", value: 4, icon: Briefcase, color: "text-primary" },
  { label: "Total Applications", value: 47, icon: Users, color: "text-accent" },
  { label: "Interviews Scheduled", value: 8, icon: Clock, color: "text-yellow-500" },
  { label: "Positions Filled", value: 3, icon: FileCheck, color: "text-green-500" },
]

const recentCandidates = [
  {
    id: 1,
    name: "Ahmed Benali",
    position: "Frontend Developer Intern",
    university: "USTHB",
    skills: ["React", "TypeScript", "Tailwind"],
    appliedAt: "2 hours ago",
    status: "new"
  },
  {
    id: 2,
    name: "Fatima Zohra",
    position: "Backend Developer Intern",
    university: "ESI Algiers",
    skills: ["Python", "Django", "PostgreSQL"],
    appliedAt: "5 hours ago",
    status: "reviewed"
  },
  {
    id: 3,
    name: "Mohamed Karim",
    position: "Full Stack Developer",
    university: "USTHB",
    skills: ["Node.js", "React", "MongoDB"],
    appliedAt: "1 day ago",
    status: "interview"
  },
  {
    id: 4,
    name: "Sara Benmoussa",
    position: "Frontend Developer Intern",
    university: "U. Constantine",
    skills: ["Vue.js", "JavaScript", "CSS"],
    appliedAt: "2 days ago",
    status: "new"
  },
]

const activeOffers = [
  {
    id: 1,
    title: "Frontend Developer Intern",
    applications: 18,
    views: 124,
    status: "active",
    deadline: "April 30, 2026"
  },
  {
    id: 2,
    title: "Backend Developer Intern",
    applications: 12,
    views: 89,
    status: "active",
    deadline: "May 15, 2026"
  },
  {
    id: 3,
    title: "Full Stack Developer",
    applications: 9,
    views: 67,
    status: "active",
    deadline: "April 20, 2026"
  },
  {
    id: 4,
    title: "DevOps Intern",
    applications: 8,
    views: 45,
    status: "closing",
    deadline: "April 10, 2026"
  },
]

export default function CompanyDashboard() {
  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Company Dashboard</h1>
            <p className="mt-1 text-muted-foreground">Manage your internship offers and candidates</p>
          </div>
          <Button asChild>
            <Link href="/dashboard/company/offers/new">
              <Briefcase className="mr-2 h-4 w-4" />
              Post New Offer
            </Link>
          </Button>
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

        <div className="mt-8 grid gap-8 lg:grid-cols-2">
          {/* Recent Candidates */}
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Recent Candidates</CardTitle>
                <CardDescription>Latest applications to your offers</CardDescription>
              </div>
              <Button variant="ghost" size="sm" asChild>
                <Link href="/dashboard/company/candidates">
                  View All <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
              </Button>
            </CardHeader>
            <CardContent className="space-y-4">
              {recentCandidates.map((candidate) => (
                <div
                  key={candidate.id}
                  className="flex items-center gap-4 rounded-lg border border-border p-3 transition-colors hover:border-primary/50"
                >
                  <Avatar>
                    <AvatarFallback className="bg-primary/10 text-primary">
                      {candidate.name.split(" ").map(n => n[0]).join("")}
                    </AvatarFallback>
                  </Avatar>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <p className="font-medium text-foreground truncate">{candidate.name}</p>
                      {candidate.status === "new" && (
                        <Badge variant="default" className="bg-accent text-accent-foreground text-xs">
                          New
                        </Badge>
                      )}
                    </div>
                    <p className="text-sm text-muted-foreground truncate">{candidate.position}</p>
                    <p className="text-xs text-muted-foreground">{candidate.university}</p>
                  </div>
                  <div className="flex gap-1">
                    <Button size="icon" variant="ghost" className="h-8 w-8 text-accent hover:text-accent hover:bg-accent/10">
                      <CheckCircle className="h-4 w-4" />
                    </Button>
                    <Button size="icon" variant="ghost" className="h-8 w-8 text-destructive hover:text-destructive hover:bg-destructive/10">
                      <XCircle className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              ))}
            </CardContent>
          </Card>

          {/* Active Offers */}
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Active Offers</CardTitle>
                <CardDescription>Your current internship positions</CardDescription>
              </div>
              <Button variant="ghost" size="sm" asChild>
                <Link href="/dashboard/company/offers">
                  Manage <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
              </Button>
            </CardHeader>
            <CardContent className="space-y-4">
              {activeOffers.map((offer) => (
                <div
                  key={offer.id}
                  className="flex items-center justify-between rounded-lg border border-border p-4 transition-colors hover:border-primary/50"
                >
                  <div>
                    <div className="flex items-center gap-2">
                      <h3 className="font-medium text-foreground">{offer.title}</h3>
                      {offer.status === "closing" && (
                        <Badge variant="outline" className="text-yellow-600 border-yellow-600">
                          Closing Soon
                        </Badge>
                      )}
                    </div>
                    <div className="mt-1 flex items-center gap-4 text-sm text-muted-foreground">
                      <span className="flex items-center gap-1">
                        <Users className="h-3.5 w-3.5" />
                        {offer.applications} applications
                      </span>
                      <span className="flex items-center gap-1">
                        <Eye className="h-3.5 w-3.5" />
                        {offer.views} views
                      </span>
                    </div>
                    <p className="mt-1 text-xs text-muted-foreground">
                      Deadline: {offer.deadline}
                    </p>
                  </div>
                  <Button size="sm" variant="outline">
                    View
                  </Button>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
