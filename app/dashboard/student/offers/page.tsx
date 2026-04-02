"use client"

import { useState } from "react"
import { Card, CardContent } from "@/components/ui/card"
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
  Search,
  MapPin,
  Building2,
  Calendar,
  Clock,
  Filter,
  Bookmark,
  BookmarkCheck
} from "lucide-react"

const allOffers = [
  {
    id: 1,
    title: "Frontend Developer Intern",
    company: "TechCorp Algeria",
    location: "Algiers",
    wilaya: "Algiers",
    type: "6 months",
    tags: ["React", "TypeScript", "Tailwind"],
    description: "Join our team to build modern web applications using React and TypeScript. You will work on real projects and learn best practices.",
    requirements: ["3rd year CS student", "Strong JavaScript knowledge", "Git proficiency"],
    postedAt: "2 days ago",
    deadline: "April 30, 2026"
  },
  {
    id: 2,
    title: "Full Stack Developer",
    company: "StartUp DZ",
    location: "Oran",
    wilaya: "Oran",
    type: "3 months",
    tags: ["Node.js", "React", "MongoDB"],
    description: "Work on our SaaS platform, implementing new features and improving existing ones.",
    requirements: ["L3/Master student", "Experience with MERN stack", "Problem-solving skills"],
    postedAt: "1 week ago",
    deadline: "May 15, 2026"
  },
  {
    id: 3,
    title: "Mobile App Developer",
    company: "Mobile Solutions",
    location: "Constantine",
    wilaya: "Constantine",
    type: "4 months",
    tags: ["React Native", "JavaScript", "Firebase"],
    description: "Develop cross-platform mobile applications for our diverse client base.",
    requirements: ["Mobile development experience", "UI/UX awareness", "Team player"],
    postedAt: "3 days ago",
    deadline: "April 20, 2026"
  },
  {
    id: 4,
    title: "Backend Developer Intern",
    company: "DataTech",
    location: "Algiers",
    wilaya: "Algiers",
    type: "6 months",
    tags: ["Python", "Django", "PostgreSQL"],
    description: "Build robust APIs and backend services for our data analytics platform.",
    requirements: ["Python proficiency", "Database knowledge", "API design experience"],
    postedAt: "5 days ago",
    deadline: "May 1, 2026"
  },
  {
    id: 5,
    title: "DevOps Intern",
    company: "CloudHost DZ",
    location: "Algiers",
    wilaya: "Algiers",
    type: "3 months",
    tags: ["Docker", "Linux", "CI/CD"],
    description: "Learn cloud infrastructure management and help automate deployment pipelines.",
    requirements: ["Linux knowledge", "Basic scripting", "Interest in cloud technologies"],
    postedAt: "1 day ago",
    deadline: "April 25, 2026"
  },
  {
    id: 6,
    title: "Data Science Intern",
    company: "AI Labs Algeria",
    location: "Algiers",
    wilaya: "Algiers",
    type: "6 months",
    tags: ["Python", "Machine Learning", "TensorFlow"],
    description: "Apply machine learning techniques to solve real business problems.",
    requirements: ["Statistics background", "Python/ML experience", "Research mindset"],
    postedAt: "4 days ago",
    deadline: "May 10, 2026"
  },
]

const wilayas = ["All Locations", "Algiers", "Oran", "Constantine", "Annaba", "Setif"]
const technologies = ["All Technologies", "React", "Python", "Node.js", "Java", "Mobile"]
const durations = ["All Durations", "3 months", "4 months", "6 months"]

export default function OffersPage() {
  const [searchQuery, setSearchQuery] = useState("")
  const [selectedWilaya, setSelectedWilaya] = useState("All Locations")
  const [selectedTech, setSelectedTech] = useState("All Technologies")
  const [selectedDuration, setSelectedDuration] = useState("All Durations")
  const [savedOffers, setSavedOffers] = useState<number[]>([])

  const filteredOffers = allOffers.filter((offer) => {
    const matchesSearch = offer.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
      offer.company.toLowerCase().includes(searchQuery.toLowerCase()) ||
      offer.tags.some(tag => tag.toLowerCase().includes(searchQuery.toLowerCase()))
    
    const matchesWilaya = selectedWilaya === "All Locations" || offer.wilaya === selectedWilaya
    const matchesTech = selectedTech === "All Technologies" || 
      offer.tags.some(tag => tag.toLowerCase().includes(selectedTech.toLowerCase()))
    const matchesDuration = selectedDuration === "All Durations" || offer.type === selectedDuration
    
    return matchesSearch && matchesWilaya && matchesTech && matchesDuration
  })

  const toggleSave = (id: number) => {
    setSavedOffers(prev => 
      prev.includes(id) ? prev.filter(i => i !== id) : [...prev, id]
    )
  }

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Internship Offers</h1>
          <p className="mt-1 text-muted-foreground">Browse and apply to internship opportunities</p>
        </div>

        {/* Filters */}
        <Card className="mb-6">
          <CardContent className="p-4">
            <div className="flex flex-col gap-4 lg:flex-row lg:items-center">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                  placeholder="Search by title, company, or skills..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10"
                />
              </div>
              <div className="flex flex-wrap gap-2">
                <Select value={selectedWilaya} onValueChange={setSelectedWilaya}>
                  <SelectTrigger className="w-[150px]">
                    <MapPin className="mr-2 h-4 w-4" />
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {wilayas.map((w) => (
                      <SelectItem key={w} value={w}>{w}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <Select value={selectedTech} onValueChange={setSelectedTech}>
                  <SelectTrigger className="w-[160px]">
                    <Filter className="mr-2 h-4 w-4" />
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {technologies.map((t) => (
                      <SelectItem key={t} value={t}>{t}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <Select value={selectedDuration} onValueChange={setSelectedDuration}>
                  <SelectTrigger className="w-[150px]">
                    <Clock className="mr-2 h-4 w-4" />
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {durations.map((d) => (
                      <SelectItem key={d} value={d}>{d}</SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Results Count */}
        <p className="mb-4 text-sm text-muted-foreground">
          Showing {filteredOffers.length} of {allOffers.length} offers
        </p>

        {/* Offers List */}
        <div className="space-y-4">
          {filteredOffers.map((offer) => (
            <Card key={offer.id} className="transition-all hover:border-primary/50 hover:shadow-md">
              <CardContent className="p-6">
                <div className="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                  <div className="flex-1">
                    <div className="flex items-start justify-between">
                      <div>
                        <h3 className="text-lg font-semibold text-foreground">{offer.title}</h3>
                        <div className="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted-foreground">
                          <span className="flex items-center gap-1">
                            <Building2 className="h-4 w-4" />
                            {offer.company}
                          </span>
                          <span className="flex items-center gap-1">
                            <MapPin className="h-4 w-4" />
                            {offer.location}
                          </span>
                          <span className="flex items-center gap-1">
                            <Clock className="h-4 w-4" />
                            {offer.type}
                          </span>
                          <span className="flex items-center gap-1">
                            <Calendar className="h-4 w-4" />
                            Deadline: {offer.deadline}
                          </span>
                        </div>
                      </div>
                      <button
                        onClick={() => toggleSave(offer.id)}
                        className="text-muted-foreground hover:text-primary transition-colors"
                      >
                        {savedOffers.includes(offer.id) ? (
                          <BookmarkCheck className="h-5 w-5 text-primary" />
                        ) : (
                          <Bookmark className="h-5 w-5" />
                        )}
                      </button>
                    </div>
                    
                    <p className="mt-3 text-sm text-muted-foreground line-clamp-2">
                      {offer.description}
                    </p>
                    
                    <div className="mt-4 flex flex-wrap gap-2">
                      {offer.tags.map((tag) => (
                        <Badge key={tag} variant="secondary">
                          {tag}
                        </Badge>
                      ))}
                    </div>

                    <p className="mt-4 text-xs text-muted-foreground">
                      Posted {offer.postedAt}
                    </p>
                  </div>
                  
                  <div className="flex gap-2 lg:flex-col">
                    <Button className="flex-1 lg:flex-none">
                      Apply Now
                    </Button>
                    <Button variant="outline" className="flex-1 lg:flex-none">
                      View Details
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {filteredOffers.length === 0 && (
          <Card className="p-12 text-center">
            <div className="mx-auto max-w-md">
              <Search className="mx-auto h-12 w-12 text-muted-foreground/50" />
              <h3 className="mt-4 text-lg font-semibold text-foreground">No offers found</h3>
              <p className="mt-2 text-sm text-muted-foreground">
                Try adjusting your filters or search query to find more opportunities.
              </p>
            </div>
          </Card>
        )}
      </div>
    </div>
  )
}
