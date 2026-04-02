"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import {
  Search,
  Filter,
  CheckCircle,
  XCircle,
  Eye,
  Github,
  Linkedin,
  Globe,
  Mail,
  GraduationCap,
  MapPin
} from "lucide-react"

const candidates = [
  {
    id: 1,
    name: "Ahmed Benali",
    email: "ahmed.benali@univ.edu",
    position: "Frontend Developer Intern",
    university: "USTHB",
    department: "Computer Science",
    level: "L3",
    wilaya: "Algiers",
    skills: ["React", "TypeScript", "Tailwind", "Git"],
    bio: "Passionate computer science student specializing in web development.",
    github: "https://github.com/ahmedbenali",
    linkedin: "https://linkedin.com/in/ahmedbenali",
    portfolio: "https://ahmed-portfolio.dev",
    appliedAt: "March 28, 2026",
    status: "new"
  },
  {
    id: 2,
    name: "Fatima Zohra",
    email: "fatima.zohra@esi.dz",
    position: "Backend Developer Intern",
    university: "ESI Algiers",
    department: "Software Engineering",
    level: "M1",
    wilaya: "Algiers",
    skills: ["Python", "Django", "PostgreSQL", "Docker"],
    bio: "Master's student focused on backend development and system design.",
    github: "https://github.com/fatimazohra",
    linkedin: "https://linkedin.com/in/fatimazohra",
    portfolio: null,
    appliedAt: "March 27, 2026",
    status: "reviewed"
  },
  {
    id: 3,
    name: "Mohamed Karim",
    email: "m.karim@usthb.dz",
    position: "Full Stack Developer",
    university: "USTHB",
    department: "Computer Science",
    level: "L3",
    wilaya: "Algiers",
    skills: ["Node.js", "React", "MongoDB", "Express"],
    bio: "Full stack developer with experience in MERN stack.",
    github: "https://github.com/mkarim",
    linkedin: null,
    portfolio: "https://mkarim.dev",
    appliedAt: "March 25, 2026",
    status: "interview"
  },
  {
    id: 4,
    name: "Sara Benmoussa",
    email: "sara.b@univ-constantine.dz",
    position: "Frontend Developer Intern",
    university: "U. Constantine",
    department: "Information Systems",
    level: "L3",
    wilaya: "Constantine",
    skills: ["Vue.js", "JavaScript", "CSS", "Figma"],
    bio: "Creative developer interested in UI/UX and frontend development.",
    github: "https://github.com/sarab",
    linkedin: "https://linkedin.com/in/sarabenmoussa",
    portfolio: null,
    appliedAt: "March 24, 2026",
    status: "new"
  },
  {
    id: 5,
    name: "Youssef Hamdi",
    email: "y.hamdi@univ-oran.dz",
    position: "DevOps Intern",
    university: "U. Oran",
    department: "Computer Science",
    level: "M2",
    wilaya: "Oran",
    skills: ["Docker", "Kubernetes", "Linux", "AWS"],
    bio: "DevOps enthusiast with cloud infrastructure experience.",
    github: "https://github.com/yhamdi",
    linkedin: "https://linkedin.com/in/youssefhamdi",
    portfolio: null,
    appliedAt: "March 23, 2026",
    status: "accepted"
  },
]

const positions = ["All Positions", "Frontend Developer Intern", "Backend Developer Intern", "Full Stack Developer", "DevOps Intern"]
const statuses = ["All Status", "new", "reviewed", "interview", "accepted", "rejected"]

export default function CandidatesPage() {
  const [searchQuery, setSearchQuery] = useState("")
  const [selectedPosition, setSelectedPosition] = useState("All Positions")
  const [selectedStatus, setSelectedStatus] = useState("All Status")
  const [candidatesList, setCandidatesList] = useState(candidates)
  const [selectedCandidate, setSelectedCandidate] = useState<typeof candidates[0] | null>(null)
  const [showAcceptDialog, setShowAcceptDialog] = useState(false)
  const [candidateToAccept, setCandidateToAccept] = useState<typeof candidates[0] | null>(null)

  const filteredCandidates = candidatesList.filter((candidate) => {
    const matchesSearch = candidate.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      candidate.skills.some(skill => skill.toLowerCase().includes(searchQuery.toLowerCase()))
    const matchesPosition = selectedPosition === "All Positions" || candidate.position === selectedPosition
    const matchesStatus = selectedStatus === "All Status" || candidate.status === selectedStatus
    return matchesSearch && matchesPosition && matchesStatus
  })

  const handleAccept = (candidate: typeof candidates[0]) => {
    setCandidateToAccept(candidate)
    setShowAcceptDialog(true)
  }

  const confirmAccept = () => {
    if (candidateToAccept) {
      setCandidatesList(prev => prev.map(c => 
        c.id === candidateToAccept.id ? { ...c, status: "accepted" } : c
      ))
    }
    setShowAcceptDialog(false)
    setCandidateToAccept(null)
  }

  const handleReject = (id: number) => {
    setCandidatesList(prev => prev.map(c => 
      c.id === id ? { ...c, status: "rejected" } : c
    ))
  }

  const statusConfig: Record<string, { label: string; variant: "default" | "secondary" | "outline" | "destructive" }> = {
    new: { label: "New", variant: "default" },
    reviewed: { label: "Reviewed", variant: "secondary" },
    interview: { label: "Interview", variant: "outline" },
    accepted: { label: "Accepted", variant: "default" },
    rejected: { label: "Rejected", variant: "destructive" }
  }

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Candidates</h1>
          <p className="mt-1 text-muted-foreground">Review and manage applicants for your positions</p>
        </div>

        {/* Filters */}
        <Card className="mb-6">
          <CardContent className="p-4">
            <div className="flex flex-col gap-4 lg:flex-row lg:items-center">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder="Search by name or skills..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10"
                />
              </div>
              <div className="flex gap-2">
                <Select value={selectedPosition} onValueChange={setSelectedPosition}>
                  <SelectTrigger className="w-[200px]">
                    <Filter className="mr-2 h-4 w-4" />
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {positions.map((p) => (
                      <SelectItem key={p} value={p}>{p}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <Select value={selectedStatus} onValueChange={setSelectedStatus}>
                  <SelectTrigger className="w-[140px]">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {statuses.map((s) => (
                      <SelectItem key={s} value={s}>
                        {s === "All Status" ? s : statusConfig[s]?.label || s}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Results */}
        <p className="mb-4 text-sm text-muted-foreground">
          Showing {filteredCandidates.length} candidates
        </p>

        {/* Candidates Grid */}
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {filteredCandidates.map((candidate) => (
            <Card key={candidate.id} className="transition-all hover:border-primary/50 hover:shadow-md">
              <CardContent className="p-6">
                <div className="flex items-start gap-4">
                  <Avatar className="h-12 w-12">
                    <AvatarFallback className="bg-primary/10 text-primary">
                      {candidate.name.split(" ").map(n => n[0]).join("")}
                    </AvatarFallback>
                  </Avatar>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <h3 className="font-semibold text-foreground truncate">{candidate.name}</h3>
                      <Badge 
                        variant={statusConfig[candidate.status]?.variant || "secondary"}
                        className={candidate.status === "accepted" ? "bg-accent text-accent-foreground" : ""}
                      >
                        {statusConfig[candidate.status]?.label || candidate.status}
                      </Badge>
                    </div>
                    <p className="text-sm text-muted-foreground truncate">{candidate.position}</p>
                    <div className="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
                      <GraduationCap className="h-3 w-3" />
                      <span>{candidate.university}</span>
                      <span>•</span>
                      <span>{candidate.level}</span>
                    </div>
                  </div>
                </div>

                <div className="mt-4 flex flex-wrap gap-1">
                  {candidate.skills.slice(0, 4).map((skill) => (
                    <Badge key={skill} variant="outline" className="text-xs">
                      {skill}
                    </Badge>
                  ))}
                  {candidate.skills.length > 4 && (
                    <Badge variant="outline" className="text-xs">
                      +{candidate.skills.length - 4}
                    </Badge>
                  )}
                </div>

                <div className="mt-4 flex items-center gap-2">
                  {candidate.github && (
                    <a href={candidate.github} target="_blank" rel="noopener noreferrer" className="text-muted-foreground hover:text-foreground">
                      <Github className="h-4 w-4" />
                    </a>
                  )}
                  {candidate.linkedin && (
                    <a href={candidate.linkedin} target="_blank" rel="noopener noreferrer" className="text-muted-foreground hover:text-foreground">
                      <Linkedin className="h-4 w-4" />
                    </a>
                  )}
                  {candidate.portfolio && (
                    <a href={candidate.portfolio} target="_blank" rel="noopener noreferrer" className="text-muted-foreground hover:text-foreground">
                      <Globe className="h-4 w-4" />
                    </a>
                  )}
                </div>

                <div className="mt-4 flex gap-2">
                  <Button 
                    size="sm" 
                    variant="outline" 
                    className="flex-1"
                    onClick={() => setSelectedCandidate(candidate)}
                  >
                    <Eye className="mr-1 h-4 w-4" />
                    View
                  </Button>
                  {candidate.status !== "accepted" && candidate.status !== "rejected" && (
                    <>
                      <Button 
                        size="sm" 
                        className="bg-accent hover:bg-accent/90 text-accent-foreground"
                        onClick={() => handleAccept(candidate)}
                      >
                        <CheckCircle className="h-4 w-4" />
                      </Button>
                      <Button 
                        size="sm" 
                        variant="outline"
                        className="text-destructive hover:bg-destructive/10"
                        onClick={() => handleReject(candidate.id)}
                      >
                        <XCircle className="h-4 w-4" />
                      </Button>
                    </>
                  )}
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Candidate Detail Dialog */}
        <Dialog open={!!selectedCandidate} onOpenChange={() => setSelectedCandidate(null)}>
          <DialogContent className="max-w-2xl">
            {selectedCandidate && (
              <>
                <DialogHeader>
                  <DialogTitle className="flex items-center gap-3">
                    <Avatar className="h-10 w-10">
                      <AvatarFallback className="bg-primary/10 text-primary">
                        {selectedCandidate.name.split(" ").map(n => n[0]).join("")}
                      </AvatarFallback>
                    </Avatar>
                    <div>
                      <span>{selectedCandidate.name}</span>
                      <p className="text-sm font-normal text-muted-foreground">{selectedCandidate.position}</p>
                    </div>
                  </DialogTitle>
                </DialogHeader>
                
                <div className="space-y-4">
                  <div className="grid gap-4 sm:grid-cols-2">
                    <div className="flex items-center gap-2 text-sm">
                      <Mail className="h-4 w-4 text-muted-foreground" />
                      <a href={`mailto:${selectedCandidate.email}`} className="text-primary hover:underline">
                        {selectedCandidate.email}
                      </a>
                    </div>
                    <div className="flex items-center gap-2 text-sm">
                      <MapPin className="h-4 w-4 text-muted-foreground" />
                      <span>{selectedCandidate.wilaya}</span>
                    </div>
                    <div className="flex items-center gap-2 text-sm">
                      <GraduationCap className="h-4 w-4 text-muted-foreground" />
                      <span>{selectedCandidate.university} - {selectedCandidate.department}</span>
                    </div>
                    <div className="flex items-center gap-2 text-sm">
                      <span className="text-muted-foreground">Level:</span>
                      <span>{selectedCandidate.level}</span>
                    </div>
                  </div>

                  <div>
                    <h4 className="font-medium mb-2">About</h4>
                    <p className="text-sm text-muted-foreground">{selectedCandidate.bio}</p>
                  </div>

                  <div>
                    <h4 className="font-medium mb-2">Skills</h4>
                    <div className="flex flex-wrap gap-2">
                      {selectedCandidate.skills.map((skill) => (
                        <Badge key={skill} variant="secondary">{skill}</Badge>
                      ))}
                    </div>
                  </div>

                  <div className="flex gap-4">
                    {selectedCandidate.github && (
                      <a href={selectedCandidate.github} target="_blank" rel="noopener noreferrer" className="flex items-center gap-2 text-sm text-primary hover:underline">
                        <Github className="h-4 w-4" /> GitHub
                      </a>
                    )}
                    {selectedCandidate.linkedin && (
                      <a href={selectedCandidate.linkedin} target="_blank" rel="noopener noreferrer" className="flex items-center gap-2 text-sm text-primary hover:underline">
                        <Linkedin className="h-4 w-4" /> LinkedIn
                      </a>
                    )}
                    {selectedCandidate.portfolio && (
                      <a href={selectedCandidate.portfolio} target="_blank" rel="noopener noreferrer" className="flex items-center gap-2 text-sm text-primary hover:underline">
                        <Globe className="h-4 w-4" /> Portfolio
                      </a>
                    )}
                  </div>
                </div>

                <DialogFooter>
                  {selectedCandidate.status !== "accepted" && selectedCandidate.status !== "rejected" && (
                    <>
                      <Button variant="outline" onClick={() => handleReject(selectedCandidate.id)}>
                        <XCircle className="mr-2 h-4 w-4" />
                        Reject
                      </Button>
                      <Button onClick={() => handleAccept(selectedCandidate)} className="bg-accent hover:bg-accent/90 text-accent-foreground">
                        <CheckCircle className="mr-2 h-4 w-4" />
                        Accept
                      </Button>
                    </>
                  )}
                </DialogFooter>
              </>
            )}
          </DialogContent>
        </Dialog>

        {/* Accept Confirmation Dialog */}
        <Dialog open={showAcceptDialog} onOpenChange={setShowAcceptDialog}>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Accept Candidate</DialogTitle>
              <DialogDescription>
                Are you sure you want to accept {candidateToAccept?.name} for the {candidateToAccept?.position} position? 
                This will notify the university administration to validate the internship agreement.
              </DialogDescription>
            </DialogHeader>
            <DialogFooter>
              <Button variant="outline" onClick={() => setShowAcceptDialog(false)}>
                Cancel
              </Button>
              <Button onClick={confirmAccept} className="bg-accent hover:bg-accent/90 text-accent-foreground">
                Confirm Accept
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  )
}
