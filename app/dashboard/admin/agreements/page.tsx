"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import {
  Search,
  FileText,
  Download,
  Eye,
  Filter,
  CheckCircle,
  Clock,
  Pen
} from "lucide-react"

const agreements = [
  {
    id: "CONV-2026-001",
    student: "Youssef Hamdi",
    company: "CloudHost DZ",
    position: "DevOps Intern",
    duration: "3 months",
    startDate: "April 1, 2026",
    endDate: "June 30, 2026",
    status: "signed",
    createdAt: "March 28, 2026"
  },
  {
    id: "CONV-2026-002",
    student: "Sara Benmoussa",
    company: "Mobile Solutions",
    position: "Mobile App Developer",
    duration: "4 months",
    startDate: "May 1, 2026",
    endDate: "August 31, 2026",
    status: "pending",
    createdAt: "March 27, 2026"
  },
  {
    id: "CONV-2026-003",
    student: "Karim Mansouri",
    company: "AI Labs Algeria",
    position: "Data Science Intern",
    duration: "6 months",
    startDate: "April 15, 2026",
    endDate: "October 14, 2026",
    status: "approved",
    createdAt: "March 26, 2026"
  },
  {
    id: "CONV-2026-004",
    student: "Amina Boudiaf",
    company: "TechCorp Algeria",
    position: "UI/UX Designer Intern",
    duration: "4 months",
    startDate: "May 1, 2026",
    endDate: "August 31, 2026",
    status: "signed",
    createdAt: "March 25, 2026"
  },
  {
    id: "CONV-2026-005",
    student: "Omar Belkacem",
    company: "DataTech",
    position: "Backend Developer Intern",
    duration: "6 months",
    startDate: "April 1, 2026",
    endDate: "September 30, 2026",
    status: "signed",
    createdAt: "March 20, 2026"
  },
  {
    id: "CONV-2026-006",
    student: "Lina Cherif",
    company: "StartUp DZ",
    position: "Full Stack Developer",
    duration: "3 months",
    startDate: "May 15, 2026",
    endDate: "August 14, 2026",
    status: "approved",
    createdAt: "March 18, 2026"
  },
]

const statusOptions = ["All Status", "signed", "approved", "pending"]

export default function AgreementsPage() {
  const [searchQuery, setSearchQuery] = useState("")
  const [selectedStatus, setSelectedStatus] = useState("All Status")

  const filteredAgreements = agreements.filter((agreement) => {
    const matchesSearch = 
      agreement.student.toLowerCase().includes(searchQuery.toLowerCase()) ||
      agreement.company.toLowerCase().includes(searchQuery.toLowerCase()) ||
      agreement.id.toLowerCase().includes(searchQuery.toLowerCase())
    const matchesStatus = selectedStatus === "All Status" || agreement.status === selectedStatus
    return matchesSearch && matchesStatus
  })

  const statusConfig: Record<string, { label: string; icon: typeof CheckCircle; color: string }> = {
    signed: { label: "Signed", icon: CheckCircle, color: "bg-accent text-accent-foreground" },
    approved: { label: "Approved", icon: Pen, color: "bg-primary text-primary-foreground" },
    pending: { label: "Pending", icon: Clock, color: "bg-yellow-500 text-white" }
  }

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Internship Agreements</h1>
          <p className="mt-1 text-muted-foreground">Manage and download Convention de Stage documents</p>
        </div>

        {/* Summary Stats */}
        <div className="mb-6 grid gap-4 sm:grid-cols-4">
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-muted p-2">
                  <FileText className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">{agreements.length}</p>
                  <p className="text-sm text-muted-foreground">Total Agreements</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-accent/10 p-2">
                  <CheckCircle className="h-5 w-5 text-accent" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">
                    {agreements.filter(a => a.status === "signed").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Signed</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-primary/10 p-2">
                  <Pen className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">
                    {agreements.filter(a => a.status === "approved").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Awaiting Signature</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-yellow-500/10 p-2">
                  <Clock className="h-5 w-5 text-yellow-600" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">
                    {agreements.filter(a => a.status === "pending").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Pending</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Filters */}
        <Card className="mb-6">
          <CardContent className="p-4">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder="Search by student, company, or agreement ID..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10"
                />
              </div>
              <Select value={selectedStatus} onValueChange={setSelectedStatus}>
                <SelectTrigger className="w-[160px]">
                  <Filter className="mr-2 h-4 w-4" />
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {statusOptions.map((s) => (
                    <SelectItem key={s} value={s}>
                      {s === "All Status" ? s : statusConfig[s]?.label || s}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </CardContent>
        </Card>

        {/* Agreements Table */}
        <Card>
          <CardHeader>
            <CardTitle>Agreements List</CardTitle>
            <CardDescription>
              {filteredAgreements.length} agreement{filteredAgreements.length !== 1 && "s"} found
            </CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Agreement ID</TableHead>
                  <TableHead>Student</TableHead>
                  <TableHead>Company</TableHead>
                  <TableHead>Position</TableHead>
                  <TableHead>Period</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredAgreements.map((agreement) => {
                  const status = statusConfig[agreement.status]
                  const StatusIcon = status?.icon || Clock
                  
                  return (
                    <TableRow key={agreement.id}>
                      <TableCell>
                        <div className="flex items-center gap-2">
                          <FileText className="h-4 w-4 text-primary" />
                          <span className="font-mono text-sm">{agreement.id}</span>
                        </div>
                      </TableCell>
                      <TableCell className="font-medium">{agreement.student}</TableCell>
                      <TableCell>{agreement.company}</TableCell>
                      <TableCell>{agreement.position}</TableCell>
                      <TableCell>
                        <div className="text-sm">
                          <p>{agreement.startDate}</p>
                          <p className="text-muted-foreground">to {agreement.endDate}</p>
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge className={status?.color || ""}>
                          <StatusIcon className="mr-1 h-3 w-3" />
                          {status?.label || agreement.status}
                        </Badge>
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-1">
                          <Button size="sm" variant="ghost">
                            <Eye className="h-4 w-4" />
                          </Button>
                          <Button 
                            size="sm" 
                            variant="ghost"
                            disabled={agreement.status === "pending"}
                          >
                            <Download className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  )
                })}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
