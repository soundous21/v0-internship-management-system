"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Users,
  Building2,
  FileText,
  CheckCircle2,
  Clock,
  TrendingUp,
  ArrowRight,
  AlertCircle
} from "lucide-react"
import Link from "next/link"
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from "recharts"

const stats = [
  { label: "Total Students", value: 324, icon: Users, color: "text-primary", change: "+12%" },
  { label: "Partner Companies", value: 45, icon: Building2, color: "text-accent", change: "+3" },
  { label: "Active Agreements", value: 87, icon: FileText, color: "text-yellow-500", change: "+8" },
  { label: "Placements This Year", value: 156, icon: CheckCircle2, color: "text-green-500", change: "+24%" },
]

const placementData = [
  { month: "Jan", placed: 12, pending: 5 },
  { month: "Feb", placed: 18, pending: 8 },
  { month: "Mar", placed: 24, pending: 12 },
  { month: "Apr", placed: 31, pending: 15 },
  { month: "May", placed: 28, pending: 10 },
  { month: "Jun", placed: 43, pending: 18 },
]

const statusData = [
  { name: "Placed", value: 156, color: "hsl(160, 60%, 50%)" },
  { name: "In Progress", value: 87, color: "hsl(45, 90%, 55%)" },
  { name: "Searching", value: 81, color: "hsl(220, 60%, 55%)" },
]

const pendingValidations = [
  {
    id: 1,
    student: "Ahmed Benali",
    company: "TechCorp Algeria",
    position: "Frontend Developer Intern",
    submittedAt: "2 hours ago",
    priority: "high"
  },
  {
    id: 2,
    student: "Fatima Zohra",
    company: "DataTech",
    position: "Backend Developer Intern",
    submittedAt: "5 hours ago",
    priority: "normal"
  },
  {
    id: 3,
    student: "Mohamed Karim",
    company: "StartUp DZ",
    position: "Full Stack Developer",
    submittedAt: "1 day ago",
    priority: "normal"
  },
]

const recentAgreements = [
  {
    id: 1,
    student: "Youssef Hamdi",
    company: "CloudHost DZ",
    status: "signed",
    date: "March 28, 2026"
  },
  {
    id: 2,
    student: "Sara Benmoussa",
    company: "Mobile Solutions",
    status: "pending",
    date: "March 27, 2026"
  },
  {
    id: 3,
    student: "Karim Mansouri",
    company: "AI Labs Algeria",
    status: "approved",
    date: "March 26, 2026"
  },
]

export default function AdminDashboard() {
  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Administration Dashboard</h1>
          <p className="mt-1 text-muted-foreground">Overview of internship management and statistics</p>
        </div>

        {/* Stats Grid */}
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {stats.map((stat) => (
            <Card key={stat.label}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div className={`rounded-lg bg-muted p-3 ${stat.color}`}>
                    <stat.icon className="h-6 w-6" />
                  </div>
                  <Badge variant="secondary" className="text-xs">
                    <TrendingUp className="mr-1 h-3 w-3" />
                    {stat.change}
                  </Badge>
                </div>
                <div className="mt-4">
                  <p className="text-2xl font-bold text-foreground">{stat.value}</p>
                  <p className="text-sm text-muted-foreground">{stat.label}</p>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Charts Row */}
        <div className="mt-8 grid gap-6 lg:grid-cols-3">
          {/* Placement Trends */}
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle>Placement Trends</CardTitle>
              <CardDescription>Monthly placement statistics for 2026</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={placementData}>
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                    <XAxis dataKey="month" className="text-xs" />
                    <YAxis className="text-xs" />
                    <Tooltip 
                      contentStyle={{ 
                        backgroundColor: "hsl(var(--card))", 
                        border: "1px solid hsl(var(--border))",
                        borderRadius: "8px"
                      }}
                    />
                    <Bar dataKey="placed" fill="hsl(160, 60%, 50%)" radius={[4, 4, 0, 0]} name="Placed" />
                    <Bar dataKey="pending" fill="hsl(220, 60%, 55%)" radius={[4, 4, 0, 0]} name="Pending" />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>

          {/* Status Distribution */}
          <Card>
            <CardHeader>
              <CardTitle>Student Status</CardTitle>
              <CardDescription>Current placement status</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-[200px]">
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie
                      data={statusData}
                      cx="50%"
                      cy="50%"
                      innerRadius={60}
                      outerRadius={80}
                      paddingAngle={5}
                      dataKey="value"
                    >
                      {statusData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={entry.color} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </div>
              <div className="mt-4 space-y-2">
                {statusData.map((item) => (
                  <div key={item.name} className="flex items-center justify-between text-sm">
                    <div className="flex items-center gap-2">
                      <div className="h-3 w-3 rounded-full" style={{ backgroundColor: item.color }} />
                      <span className="text-muted-foreground">{item.name}</span>
                    </div>
                    <span className="font-medium">{item.value}</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Bottom Row */}
        <div className="mt-8 grid gap-6 lg:grid-cols-2">
          {/* Pending Validations */}
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle className="flex items-center gap-2">
                  <AlertCircle className="h-5 w-5 text-yellow-500" />
                  Pending Validations
                </CardTitle>
                <CardDescription>Internships awaiting your approval</CardDescription>
              </div>
              <Button variant="ghost" size="sm" asChild>
                <Link href="/dashboard/admin/validations">
                  View All <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
              </Button>
            </CardHeader>
            <CardContent className="space-y-4">
              {pendingValidations.map((item) => (
                <div
                  key={item.id}
                  className="flex items-center justify-between rounded-lg border border-border p-3 transition-colors hover:border-primary/50"
                >
                  <div className="flex items-center gap-3">
                    <div className={`h-2 w-2 rounded-full ${
                      item.priority === "high" ? "bg-destructive animate-pulse" : "bg-yellow-500"
                    }`} />
                    <div>
                      <p className="font-medium text-foreground">{item.student}</p>
                      <p className="text-sm text-muted-foreground">{item.position} at {item.company}</p>
                      <p className="text-xs text-muted-foreground">{item.submittedAt}</p>
                    </div>
                  </div>
                  <Button size="sm">Review</Button>
                </div>
              ))}
            </CardContent>
          </Card>

          {/* Recent Agreements */}
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <div>
                <CardTitle>Recent Agreements</CardTitle>
                <CardDescription>Latest convention de stage documents</CardDescription>
              </div>
              <Button variant="ghost" size="sm" asChild>
                <Link href="/dashboard/admin/agreements">
                  View All <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
              </Button>
            </CardHeader>
            <CardContent className="space-y-4">
              {recentAgreements.map((agreement) => (
                <div
                  key={agreement.id}
                  className="flex items-center justify-between rounded-lg border border-border p-3"
                >
                  <div className="flex items-center gap-3">
                    <FileText className="h-8 w-8 text-primary" />
                    <div>
                      <p className="font-medium text-foreground">{agreement.student}</p>
                      <p className="text-sm text-muted-foreground">{agreement.company}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <Badge
                      variant={
                        agreement.status === "signed" 
                          ? "default" 
                          : agreement.status === "approved"
                          ? "secondary"
                          : "outline"
                      }
                      className={agreement.status === "signed" ? "bg-accent text-accent-foreground" : ""}
                    >
                      {agreement.status}
                    </Badge>
                    <p className="mt-1 text-xs text-muted-foreground">{agreement.date}</p>
                  </div>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
