"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import {
  Briefcase,
  Plus,
  MoreVertical,
  Edit,
  Trash2,
  Eye,
  Users,
  Calendar,
  Pause,
  Play
} from "lucide-react"
import Link from "next/link"

const offers = [
  {
    id: 1,
    title: "Frontend Developer Intern",
    description: "Join our team to build modern web applications using React and TypeScript.",
    duration: "6 months",
    tags: ["React", "TypeScript", "Tailwind"],
    applications: 18,
    views: 124,
    status: "active",
    createdAt: "March 15, 2026",
    deadline: "April 30, 2026"
  },
  {
    id: 2,
    title: "Backend Developer Intern",
    description: "Work on our Python/Django backend and help build scalable APIs.",
    duration: "6 months",
    tags: ["Python", "Django", "PostgreSQL"],
    applications: 12,
    views: 89,
    status: "active",
    createdAt: "March 10, 2026",
    deadline: "May 15, 2026"
  },
  {
    id: 3,
    title: "Full Stack Developer",
    description: "End-to-end development opportunity working on our SaaS platform.",
    duration: "3 months",
    tags: ["Node.js", "React", "MongoDB"],
    applications: 9,
    views: 67,
    status: "active",
    createdAt: "March 5, 2026",
    deadline: "April 20, 2026"
  },
  {
    id: 4,
    title: "DevOps Intern",
    description: "Learn cloud infrastructure and help automate deployment pipelines.",
    duration: "3 months",
    tags: ["Docker", "Linux", "CI/CD"],
    applications: 8,
    views: 45,
    status: "paused",
    createdAt: "February 28, 2026",
    deadline: "April 10, 2026"
  },
  {
    id: 5,
    title: "UI/UX Designer Intern",
    description: "Design beautiful and intuitive user interfaces for our products.",
    duration: "4 months",
    tags: ["Figma", "UI Design", "Prototyping"],
    applications: 15,
    views: 98,
    status: "closed",
    createdAt: "February 20, 2026",
    deadline: "March 30, 2026"
  },
]

export default function CompanyOffersPage() {
  const [offersList, setOffersList] = useState(offers)

  const toggleStatus = (id: number) => {
    setOffersList(prev => prev.map(offer => {
      if (offer.id === id) {
        return {
          ...offer,
          status: offer.status === "active" ? "paused" : "active"
        }
      }
      return offer
    }))
  }

  const activeCount = offersList.filter(o => o.status === "active").length
  const totalApplications = offersList.reduce((sum, o) => sum + o.applications, 0)

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Manage Offers</h1>
            <p className="mt-1 text-muted-foreground">Create and manage your internship positions</p>
          </div>
          <Button asChild>
            <Link href="/dashboard/company/offers/new">
              <Plus className="mr-2 h-4 w-4" />
              Create New Offer
            </Link>
          </Button>
        </div>

        {/* Summary Stats */}
        <div className="mb-8 grid gap-4 sm:grid-cols-3">
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-primary/10 p-2">
                  <Briefcase className="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">{offersList.length}</p>
                  <p className="text-sm text-muted-foreground">Total Offers</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-accent/10 p-2">
                  <Play className="h-5 w-5 text-accent" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">{activeCount}</p>
                  <p className="text-sm text-muted-foreground">Active</p>
                </div>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="p-4">
              <div className="flex items-center gap-3">
                <div className="rounded-lg bg-yellow-500/10 p-2">
                  <Users className="h-5 w-5 text-yellow-600" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-foreground">{totalApplications}</p>
                  <p className="text-sm text-muted-foreground">Total Applications</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Offers List */}
        <Card>
          <CardHeader>
            <CardTitle>All Offers</CardTitle>
            <CardDescription>Manage your internship positions</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {offersList.map((offer) => (
                <div
                  key={offer.id}
                  className={`rounded-lg border p-4 transition-colors ${
                    offer.status === "closed" 
                      ? "border-border bg-muted/30" 
                      : "border-border hover:border-primary/50"
                  }`}
                >
                  <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div className="flex-1">
                      <div className="flex items-start gap-3">
                        <div className={`rounded-lg p-2 ${
                          offer.status === "active" 
                            ? "bg-accent/10" 
                            : offer.status === "paused"
                            ? "bg-yellow-500/10"
                            : "bg-muted"
                        }`}>
                          <Briefcase className={`h-5 w-5 ${
                            offer.status === "active" 
                              ? "text-accent" 
                              : offer.status === "paused"
                              ? "text-yellow-600"
                              : "text-muted-foreground"
                          }`} />
                        </div>
                        <div className="flex-1">
                          <div className="flex items-center gap-2">
                            <h3 className="font-semibold text-foreground">{offer.title}</h3>
                            <Badge
                              variant={
                                offer.status === "active" 
                                  ? "default" 
                                  : offer.status === "paused"
                                  ? "secondary"
                                  : "outline"
                              }
                              className={
                                offer.status === "active"
                                  ? "bg-accent text-accent-foreground"
                                  : ""
                              }
                            >
                              {offer.status}
                            </Badge>
                          </div>
                          <p className="mt-1 text-sm text-muted-foreground line-clamp-1">
                            {offer.description}
                          </p>
                          <div className="mt-3 flex flex-wrap gap-2">
                            {offer.tags.map((tag) => (
                              <Badge key={tag} variant="outline" className="text-xs">
                                {tag}
                              </Badge>
                            ))}
                          </div>
                          <div className="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted-foreground">
                            <span className="flex items-center gap-1">
                              <Users className="h-3.5 w-3.5" />
                              {offer.applications} applications
                            </span>
                            <span className="flex items-center gap-1">
                              <Eye className="h-3.5 w-3.5" />
                              {offer.views} views
                            </span>
                            <span className="flex items-center gap-1">
                              <Calendar className="h-3.5 w-3.5" />
                              Deadline: {offer.deadline}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div className="flex items-center gap-2">
                      <Button size="sm" variant="outline" asChild>
                        <Link href={`/dashboard/company/candidates?offer=${offer.id}`}>
                          <Users className="mr-1 h-4 w-4" />
                          Candidates
                        </Link>
                      </Button>
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button size="icon" variant="ghost">
                            <MoreVertical className="h-4 w-4" />
                          </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem>
                            <Edit className="mr-2 h-4 w-4" />
                            Edit Offer
                          </DropdownMenuItem>
                          <DropdownMenuItem>
                            <Eye className="mr-2 h-4 w-4" />
                            Preview
                          </DropdownMenuItem>
                          {offer.status !== "closed" && (
                            <DropdownMenuItem onClick={() => toggleStatus(offer.id)}>
                              {offer.status === "active" ? (
                                <>
                                  <Pause className="mr-2 h-4 w-4" />
                                  Pause Offer
                                </>
                              ) : (
                                <>
                                  <Play className="mr-2 h-4 w-4" />
                                  Activate Offer
                                </>
                              )}
                            </DropdownMenuItem>
                          )}
                          <DropdownMenuItem className="text-destructive">
                            <Trash2 className="mr-2 h-4 w-4" />
                            Delete
                          </DropdownMenuItem>
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
