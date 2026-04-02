"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Building2,
  MapPin,
  Calendar,
  Clock,
  ExternalLink,
  FileText,
  CheckCircle2,
  XCircle,
  AlertCircle
} from "lucide-react"

const applications = [
  {
    id: 1,
    position: "Frontend Developer Intern",
    company: "TechCorp Algeria",
    location: "Algiers",
    appliedAt: "March 28, 2026",
    status: "pending",
    statusMessage: "Application under review",
    duration: "6 months",
    hasAgreement: false
  },
  {
    id: 2,
    position: "Backend Developer Intern",
    company: "DataTech",
    location: "Algiers",
    appliedAt: "March 15, 2026",
    status: "accepted",
    statusMessage: "Congratulations! Your application was accepted",
    duration: "6 months",
    hasAgreement: true
  },
  {
    id: 3,
    position: "DevOps Intern",
    company: "CloudHost DZ",
    location: "Algiers",
    appliedAt: "March 10, 2026",
    status: "interview",
    statusMessage: "Interview scheduled for April 5, 2026",
    duration: "3 months",
    hasAgreement: false
  },
  {
    id: 4,
    position: "UI/UX Designer Intern",
    company: "DesignStudio",
    location: "Oran",
    appliedAt: "March 5, 2026",
    status: "rejected",
    statusMessage: "Position has been filled",
    duration: "4 months",
    hasAgreement: false
  },
  {
    id: 5,
    position: "Mobile App Developer",
    company: "Mobile Solutions",
    location: "Constantine",
    appliedAt: "February 28, 2026",
    status: "pending",
    statusMessage: "Application submitted",
    duration: "4 months",
    hasAgreement: false
  },
]

const statusConfig = {
  pending: {
    label: "Pending",
    variant: "secondary" as const,
    icon: Clock,
    color: "text-yellow-600"
  },
  accepted: {
    label: "Accepted",
    variant: "default" as const,
    icon: CheckCircle2,
    color: "text-accent"
  },
  rejected: {
    label: "Rejected",
    variant: "destructive" as const,
    icon: XCircle,
    color: "text-destructive"
  },
  interview: {
    label: "Interview",
    variant: "outline" as const,
    icon: AlertCircle,
    color: "text-primary"
  }
}

export default function ApplicationsPage() {
  const acceptedCount = applications.filter(a => a.status === "accepted").length
  const pendingCount = applications.filter(a => a.status === "pending").length
  const interviewCount = applications.filter(a => a.status === "interview").length

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">My Applications</h1>
          <p className="mt-1 text-muted-foreground">Track the status of your internship applications</p>
        </div>

        {/* Summary Stats */}
        <div className="mb-8 grid gap-4 sm:grid-cols-4">
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-foreground">{applications.length}</div>
              <p className="text-sm text-muted-foreground">Total Applications</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-accent">{acceptedCount}</div>
              <p className="text-sm text-muted-foreground">Accepted</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-primary">{interviewCount}</div>
              <p className="text-sm text-muted-foreground">Interviews</p>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="text-2xl font-bold text-yellow-600">{pendingCount}</div>
              <p className="text-sm text-muted-foreground">Pending</p>
            </CardContent>
          </Card>
        </div>

        {/* Applications List */}
        <Card>
          <CardHeader>
            <CardTitle>Application History</CardTitle>
            <CardDescription>All your submitted applications</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {applications.map((app) => {
                const status = statusConfig[app.status as keyof typeof statusConfig]
                const StatusIcon = status.icon

                return (
                  <div
                    key={app.id}
                    className="flex flex-col gap-4 rounded-lg border border-border p-4 transition-colors hover:border-primary/30 sm:flex-row sm:items-center"
                  >
                    <div className="flex-1">
                      <div className="flex items-start gap-3">
                        <div className={`mt-1 rounded-full p-1 ${
                          app.status === "accepted" ? "bg-accent/10" : 
                          app.status === "rejected" ? "bg-destructive/10" : 
                          app.status === "interview" ? "bg-primary/10" : "bg-yellow-500/10"
                        }`}>
                          <StatusIcon className={`h-4 w-4 ${status.color}`} />
                        </div>
                        <div className="flex-1">
                          <div className="flex items-center gap-2">
                            <h3 className="font-semibold text-foreground">{app.position}</h3>
                            <Badge variant={status.variant}>
                              {status.label}
                            </Badge>
                          </div>
                          <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground">
                            <span className="flex items-center gap-1">
                              <Building2 className="h-3.5 w-3.5" />
                              {app.company}
                            </span>
                            <span className="flex items-center gap-1">
                              <MapPin className="h-3.5 w-3.5" />
                              {app.location}
                            </span>
                            <span className="flex items-center gap-1">
                              <Clock className="h-3.5 w-3.5" />
                              {app.duration}
                            </span>
                          </div>
                          <p className="mt-2 text-sm text-muted-foreground">
                            {app.statusMessage}
                          </p>
                          <p className="mt-1 text-xs text-muted-foreground">
                            <Calendar className="mr-1 inline h-3 w-3" />
                            Applied on {app.appliedAt}
                          </p>
                        </div>
                      </div>
                    </div>

                    <div className="flex gap-2 sm:flex-col">
                      {app.status === "accepted" && app.hasAgreement && (
                        <Button size="sm" variant="outline" className="gap-1">
                          <FileText className="h-4 w-4" />
                          View Agreement
                        </Button>
                      )}
                      <Button size="sm" variant="ghost" className="gap-1">
                        <ExternalLink className="h-4 w-4" />
                        View Offer
                      </Button>
                    </div>
                  </div>
                )
              })}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
