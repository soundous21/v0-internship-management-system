"use client"

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Users,
  Building2,
  GraduationCap,
  TrendingUp,
  TrendingDown,
  Target
} from "lucide-react"
import { useState } from "react"
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  LineChart,
  Line,
  PieChart,
  Pie,
  Cell,
  Legend
} from "recharts"

const monthlyData = [
  { month: "Sep", students: 45, placements: 12, applications: 78 },
  { month: "Oct", students: 68, placements: 24, applications: 142 },
  { month: "Nov", students: 92, placements: 38, applications: 210 },
  { month: "Dec", students: 115, placements: 52, applications: 268 },
  { month: "Jan", students: 156, placements: 78, applications: 345 },
  { month: "Feb", students: 198, placements: 102, applications: 412 },
  { month: "Mar", students: 256, placements: 134, applications: 520 },
  { month: "Apr", students: 324, placements: 156, applications: 612 },
]

const departmentData = [
  { name: "Computer Science", students: 124, placed: 89, rate: 72 },
  { name: "Software Engineering", students: 86, placed: 68, rate: 79 },
  { name: "Information Systems", students: 64, placed: 42, rate: 66 },
  { name: "Networks & Security", students: 50, placed: 32, rate: 64 },
]

const industryData = [
  { name: "Technology", value: 45, color: "hsl(220, 60%, 55%)" },
  { name: "Finance", value: 18, color: "hsl(160, 60%, 50%)" },
  { name: "Telecom", value: 15, color: "hsl(45, 90%, 55%)" },
  { name: "E-commerce", value: 12, color: "hsl(280, 60%, 55%)" },
  { name: "Other", value: 10, color: "hsl(0, 0%, 60%)" },
]

const topCompanies = [
  { name: "TechCorp Algeria", placements: 24, rating: 4.8 },
  { name: "DataTech", placements: 18, rating: 4.6 },
  { name: "CloudHost DZ", placements: 15, rating: 4.7 },
  { name: "StartUp DZ", placements: 12, rating: 4.5 },
  { name: "AI Labs Algeria", placements: 10, rating: 4.9 },
]

const topSkills = [
  { skill: "React", demand: 89 },
  { skill: "Python", demand: 76 },
  { skill: "Node.js", demand: 68 },
  { skill: "Java", demand: 54 },
  { skill: "Docker", demand: 42 },
  { skill: "TypeScript", demand: 38 },
]

export default function StatisticsPage() {
  const [timeRange, setTimeRange] = useState("year")

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Statistics & Analytics</h1>
            <p className="mt-1 text-muted-foreground">Comprehensive overview of internship program performance</p>
          </div>
          <Select value={timeRange} onValueChange={setTimeRange}>
            <SelectTrigger className="w-[180px]">
              <SelectValue placeholder="Select period" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="month">This Month</SelectItem>
              <SelectItem value="quarter">This Quarter</SelectItem>
              <SelectItem value="year">This Year</SelectItem>
            </SelectContent>
          </Select>
        </div>

        {/* KPI Cards */}
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div className="rounded-lg bg-primary/10 p-3">
                  <Users className="h-6 w-6 text-primary" />
                </div>
                <Badge variant="secondary" className="text-xs text-accent">
                  <TrendingUp className="mr-1 h-3 w-3" />
                  +18%
                </Badge>
              </div>
              <div className="mt-4">
                <p className="text-3xl font-bold text-foreground">324</p>
                <p className="text-sm text-muted-foreground">Registered Students</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div className="rounded-lg bg-accent/10 p-3">
                  <GraduationCap className="h-6 w-6 text-accent" />
                </div>
                <Badge variant="secondary" className="text-xs text-accent">
                  <TrendingUp className="mr-1 h-3 w-3" />
                  +24%
                </Badge>
              </div>
              <div className="mt-4">
                <p className="text-3xl font-bold text-foreground">156</p>
                <p className="text-sm text-muted-foreground">Students Placed</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div className="rounded-lg bg-yellow-500/10 p-3">
                  <Building2 className="h-6 w-6 text-yellow-600" />
                </div>
                <Badge variant="secondary" className="text-xs">
                  +5
                </Badge>
              </div>
              <div className="mt-4">
                <p className="text-3xl font-bold text-foreground">45</p>
                <p className="text-sm text-muted-foreground">Partner Companies</p>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div className="rounded-lg bg-green-500/10 p-3">
                  <Target className="h-6 w-6 text-green-600" />
                </div>
                <Badge variant="secondary" className="text-xs text-accent">
                  <TrendingUp className="mr-1 h-3 w-3" />
                  +8%
                </Badge>
              </div>
              <div className="mt-4">
                <p className="text-3xl font-bold text-foreground">48%</p>
                <p className="text-sm text-muted-foreground">Placement Rate</p>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Charts Row 1 */}
        <div className="mt-8 grid gap-6 lg:grid-cols-2">
          {/* Growth Trends */}
          <Card>
            <CardHeader>
              <CardTitle>Growth Trends</CardTitle>
              <CardDescription>Students and placements over time</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={monthlyData}>
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
                    <Legend />
                    <Line
                      type="monotone"
                      dataKey="students"
                      stroke="hsl(220, 60%, 55%)"
                      strokeWidth={2}
                      dot={{ fill: "hsl(220, 60%, 55%)" }}
                      name="Total Students"
                    />
                    <Line
                      type="monotone"
                      dataKey="placements"
                      stroke="hsl(160, 60%, 50%)"
                      strokeWidth={2}
                      dot={{ fill: "hsl(160, 60%, 50%)" }}
                      name="Placements"
                    />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>

          {/* Industry Distribution */}
          <Card>
            <CardHeader>
              <CardTitle>Placements by Industry</CardTitle>
              <CardDescription>Distribution of internships across sectors</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie
                      data={industryData}
                      cx="50%"
                      cy="50%"
                      outerRadius={100}
                      dataKey="value"
                      label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                      labelLine={false}
                    >
                      {industryData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={entry.color} />
                      ))}
                    </Pie>
                    <Tooltip />
                  </PieChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Charts Row 2 */}
        <div className="mt-8 grid gap-6 lg:grid-cols-3">
          {/* Department Performance */}
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle>Placement Rate by Department</CardTitle>
              <CardDescription>Performance comparison across departments</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="h-[280px]">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={departmentData} layout="vertical">
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                    <XAxis type="number" domain={[0, 100]} unit="%" className="text-xs" />
                    <YAxis dataKey="name" type="category" width={140} className="text-xs" />
                    <Tooltip
                      contentStyle={{
                        backgroundColor: "hsl(var(--card))",
                        border: "1px solid hsl(var(--border))",
                        borderRadius: "8px"
                      }}
                      formatter={(value) => [`${value}%`, "Placement Rate"]}
                    />
                    <Bar dataKey="rate" fill="hsl(220, 60%, 55%)" radius={[0, 4, 4, 0]} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>

          {/* Most Demanded Skills */}
          <Card>
            <CardHeader>
              <CardTitle>Top Skills in Demand</CardTitle>
              <CardDescription>Most requested by companies</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {topSkills.map((item, index) => (
                  <div key={item.skill} className="space-y-1">
                    <div className="flex items-center justify-between text-sm">
                      <span className="font-medium">{item.skill}</span>
                      <span className="text-muted-foreground">{item.demand} offers</span>
                    </div>
                    <div className="h-2 rounded-full bg-muted overflow-hidden">
                      <div
                        className="h-full rounded-full bg-primary transition-all"
                        style={{ width: `${(item.demand / 89) * 100}%` }}
                      />
                    </div>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Top Companies */}
        <Card className="mt-8">
          <CardHeader>
            <CardTitle>Top Partner Companies</CardTitle>
            <CardDescription>Companies with most internship placements</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
              {topCompanies.map((company, index) => (
                <div
                  key={company.name}
                  className="flex flex-col items-center rounded-lg border border-border p-4 text-center"
                >
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary font-bold">
                    #{index + 1}
                  </div>
                  <h4 className="mt-3 font-semibold text-foreground">{company.name}</h4>
                  <p className="text-2xl font-bold text-primary">{company.placements}</p>
                  <p className="text-xs text-muted-foreground">placements</p>
                  <Badge variant="secondary" className="mt-2">
                    {company.rating} rating
                  </Badge>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
