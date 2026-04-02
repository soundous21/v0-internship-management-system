"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import {
  CheckCircle,
  XCircle,
  Eye,
  FileText,
  Building2,
  GraduationCap,
  Calendar,
  Clock
} from "lucide-react"

const pendingValidations = [
  {
    id: 1,
    student: {
      name: "Ahmed Benali",
      email: "ahmed.benali@univ.edu",
      university: "USTHB",
      department: "Computer Science",
      level: "L3"
    },
    company: {
      name: "TechCorp Algeria",
      location: "Algiers",
      supervisor: "Mr. Karim Mansouri"
    },
    position: "Frontend Developer Intern",
    duration: "6 months",
    startDate: "May 1, 2026",
    submittedAt: "March 28, 2026",
    priority: "high",
    status: "pending"
  },
  {
    id: 2,
    student: {
      name: "Fatima Zohra",
      email: "fatima.zohra@esi.dz",
      university: "ESI Algiers",
      department: "Software Engineering",
      level: "M1"
    },
    company: {
      name: "DataTech",
      location: "Algiers",
      supervisor: "Ms. Sarah Benali"
    },
    position: "Backend Developer Intern",
    duration: "6 months",
    startDate: "May 15, 2026",
    submittedAt: "March 27, 2026",
    priority: "normal",
    status: "pending"
  },
  {
    id: 3,
    student: {
      name: "Mohamed Karim",
      email: "m.karim@usthb.dz",
      university: "USTHB",
      department: "Computer Science",
      level: "L3"
    },
    company: {
      name: "StartUp DZ",
      location: "Oran",
      supervisor: "Mr. Youssef Hamdi"
    },
    position: "Full Stack Developer",
    duration: "3 months",
    startDate: "April 20, 2026",
    submittedAt: "March 26, 2026",
    priority: "normal",
    status: "pending"
  },
  {
    id: 4,
    student: {
      name: "Sara Benmoussa",
      email: "sara.b@univ-constantine.dz",
      university: "U. Constantine",
      department: "Information Systems",
      level: "L3"
    },
    company: {
      name: "Mobile Solutions",
      location: "Constantine",
      supervisor: "Mr. Ali Cherif"
    },
    position: "Mobile App Developer",
    duration: "4 months",
    startDate: "May 1, 2026",
    submittedAt: "March 25, 2026",
    priority: "normal",
    status: "pending"
  },
]

export default function ValidationsPage() {
  const [validations, setValidations] = useState(pendingValidations)
  const [selectedItem, setSelectedItem] = useState<typeof pendingValidations[0] | null>(null)
  const [showApproveDialog, setShowApproveDialog] = useState(false)
  const [showRejectDialog, setShowRejectDialog] = useState(false)

  const handleApprove = () => {
    if (selectedItem) {
      setValidations(prev => prev.map(v => 
        v.id === selectedItem.id ? { ...v, status: "approved" } : v
      ))
    }
    setShowApproveDialog(false)
    setSelectedItem(null)
  }

  const handleReject = () => {
    if (selectedItem) {
      setValidations(prev => prev.map(v => 
        v.id === selectedItem.id ? { ...v, status: "rejected" } : v
      ))
    }
    setShowRejectDialog(false)
    setSelectedItem(null)
  }

  const pendingCount = validations.filter(v => v.status === "pending").length

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Pending Validations</h1>
          <p className="mt-1 text-muted-foreground">Review and validate internship placements</p>
        </div>

        {/* Summary */}
        <div className="mb-6 grid gap-4 sm:grid-cols-3">
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-yellow-500/10 p-2">
                  <Clock className="h-5 w-5 text-yellow-600" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">{pendingCount}</p>
                  <p className="text-sm text-muted-foreground">Pending</p>
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
                    {validations.filter(v => v.status === "approved").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Approved Today</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-destructive/10 p-2">
                  <XCircle className="h-5 w-5 text-destructive" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">
                    {validations.filter(v => v.status === "rejected").length}
                  </p>
                  <p className="text-sm text-muted-foreground">Rejected</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Validations Table */}
        <Card>
          <CardHeader>
            <CardTitle>Internship Requests</CardTitle>
            <CardDescription>Review each request before generating the agreement</CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Student</TableHead>
                  <TableHead>Company</TableHead>
                  <TableHead>Position</TableHead>
                  <TableHead>Duration</TableHead>
                  <TableHead>Submitted</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {validations.map((item) => (
                  <TableRow key={item.id}>
                    <TableCell>
                      <div className="flex items-center gap-2">
                        {item.priority === "high" && (
                          <div className="h-2 w-2 rounded-full bg-destructive animate-pulse" />
                        )}
                        <div>
                          <p className="font-medium">{item.student.name}</p>
                          <p className="text-sm text-muted-foreground">{item.student.university}</p>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <p className="font-medium">{item.company.name}</p>
                      <p className="text-sm text-muted-foreground">{item.company.location}</p>
                    </TableCell>
                    <TableCell>{item.position}</TableCell>
                    <TableCell>{item.duration}</TableCell>
                    <TableCell className="text-muted-foreground">{item.submittedAt}</TableCell>
                    <TableCell>
                      <Badge
                        variant={
                          item.status === "approved" 
                            ? "default" 
                            : item.status === "rejected"
                            ? "destructive"
                            : "secondary"
                        }
                        className={item.status === "approved" ? "bg-accent text-accent-foreground" : ""}
                      >
                        {item.status}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end gap-1">
                        <Button
                          size="sm"
                          variant="ghost"
                          onClick={() => setSelectedItem(item)}
                        >
                          <Eye className="h-4 w-4" />
                        </Button>
                        {item.status === "pending" && (
                          <>
                            <Button
                              size="sm"
                              variant="ghost"
                              className="text-accent hover:text-accent hover:bg-accent/10"
                              onClick={() => {
                                setSelectedItem(item)
                                setShowApproveDialog(true)
                              }}
                            >
                              <CheckCircle className="h-4 w-4" />
                            </Button>
                            <Button
                              size="sm"
                              variant="ghost"
                              className="text-destructive hover:text-destructive hover:bg-destructive/10"
                              onClick={() => {
                                setSelectedItem(item)
                                setShowRejectDialog(true)
                              }}
                            >
                              <XCircle className="h-4 w-4" />
                            </Button>
                          </>
                        )}
                        {item.status === "approved" && (
                          <Button size="sm" variant="outline">
                            <FileText className="mr-1 h-4 w-4" />
                            Generate
                          </Button>
                        )}
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        {/* Detail Dialog */}
        <Dialog open={!!selectedItem && !showApproveDialog && !showRejectDialog} onOpenChange={() => setSelectedItem(null)}>
          <DialogContent className="max-w-2xl">
            {selectedItem && (
              <>
                <DialogHeader>
                  <DialogTitle>Internship Request Details</DialogTitle>
                  <DialogDescription>
                    Review the details before making a decision
                  </DialogDescription>
                </DialogHeader>
                
                <div className="grid gap-6 sm:grid-cols-2">
                  {/* Student Info */}
                  <div className="space-y-4">
                    <div className="flex items-center gap-2">
                      <GraduationCap className="h-5 w-5 text-primary" />
                      <h4 className="font-semibold">Student Information</h4>
                    </div>
                    <div className="rounded-lg border border-border p-4 space-y-2">
                      <p><span className="text-muted-foreground">Name:</span> {selectedItem.student.name}</p>
                      <p><span className="text-muted-foreground">Email:</span> {selectedItem.student.email}</p>
                      <p><span className="text-muted-foreground">University:</span> {selectedItem.student.university}</p>
                      <p><span className="text-muted-foreground">Department:</span> {selectedItem.student.department}</p>
                      <p><span className="text-muted-foreground">Level:</span> {selectedItem.student.level}</p>
                    </div>
                  </div>

                  {/* Company Info */}
                  <div className="space-y-4">
                    <div className="flex items-center gap-2">
                      <Building2 className="h-5 w-5 text-primary" />
                      <h4 className="font-semibold">Company Information</h4>
                    </div>
                    <div className="rounded-lg border border-border p-4 space-y-2">
                      <p><span className="text-muted-foreground">Company:</span> {selectedItem.company.name}</p>
                      <p><span className="text-muted-foreground">Location:</span> {selectedItem.company.location}</p>
                      <p><span className="text-muted-foreground">Supervisor:</span> {selectedItem.company.supervisor}</p>
                    </div>
                  </div>
                </div>

                {/* Internship Details */}
                <div className="space-y-4">
                  <div className="flex items-center gap-2">
                    <Calendar className="h-5 w-5 text-primary" />
                    <h4 className="font-semibold">Internship Details</h4>
                  </div>
                  <div className="rounded-lg border border-border p-4 grid gap-4 sm:grid-cols-3">
                    <div>
                      <p className="text-sm text-muted-foreground">Position</p>
                      <p className="font-medium">{selectedItem.position}</p>
                    </div>
                    <div>
                      <p className="text-sm text-muted-foreground">Duration</p>
                      <p className="font-medium">{selectedItem.duration}</p>
                    </div>
                    <div>
                      <p className="text-sm text-muted-foreground">Start Date</p>
                      <p className="font-medium">{selectedItem.startDate}</p>
                    </div>
                  </div>
                </div>

                <DialogFooter>
                  {selectedItem.status === "pending" && (
                    <>
                      <Button 
                        variant="outline" 
                        onClick={() => {
                          setShowRejectDialog(true)
                        }}
                      >
                        <XCircle className="mr-2 h-4 w-4" />
                        Reject
                      </Button>
                      <Button 
                        onClick={() => {
                          setShowApproveDialog(true)
                        }}
                        className="bg-accent hover:bg-accent/90 text-accent-foreground"
                      >
                        <CheckCircle className="mr-2 h-4 w-4" />
                        Approve
                      </Button>
                    </>
                  )}
                </DialogFooter>
              </>
            )}
          </DialogContent>
        </Dialog>

        {/* Approve Confirmation */}
        <Dialog open={showApproveDialog} onOpenChange={setShowApproveDialog}>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Approve Internship</DialogTitle>
              <DialogDescription>
                Are you sure you want to approve this internship request for {selectedItem?.student.name}? 
                This will generate the official internship agreement (Convention de Stage).
              </DialogDescription>
            </DialogHeader>
            <DialogFooter>
              <Button variant="outline" onClick={() => setShowApproveDialog(false)}>
                Cancel
              </Button>
              <Button onClick={handleApprove} className="bg-accent hover:bg-accent/90 text-accent-foreground">
                Approve & Generate Agreement
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>

        {/* Reject Confirmation */}
        <Dialog open={showRejectDialog} onOpenChange={setShowRejectDialog}>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Reject Internship</DialogTitle>
              <DialogDescription>
                Are you sure you want to reject this internship request for {selectedItem?.student.name}? 
                The student and company will be notified.
              </DialogDescription>
            </DialogHeader>
            <DialogFooter>
              <Button variant="outline" onClick={() => setShowRejectDialog(false)}>
                Cancel
              </Button>
              <Button variant="destructive" onClick={handleReject}>
                Reject Request
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  )
}
