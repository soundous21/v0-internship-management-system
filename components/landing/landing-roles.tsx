"use client"

import { Button } from "@/components/ui/button"
import { GraduationCap, Building2, ShieldCheck, ArrowRight, CheckCircle2 } from "lucide-react"
import type { UserRole } from "@/app/page"

interface LandingRolesProps {
  onSelectRole: (role: UserRole) => void
}

const roles = [
  {
    id: "student" as UserRole,
    icon: GraduationCap,
    title: "For Students",
    description: "Find your dream internship and kickstart your career",
    features: [
      "Build a digital CV with technical skills",
      "Search internships by location & tech stack",
      "Track application status in real-time",
      "Receive personalized recommendations"
    ],
    color: "primary"
  },
  {
    id: "company" as UserRole,
    icon: Building2,
    title: "For Companies",
    description: "Find talented interns that match your requirements",
    features: [
      "Post internship offers with required skills",
      "Browse student profiles and portfolios",
      "Manage applications in one dashboard",
      "Streamlined agreement generation"
    ],
    color: "accent"
  },
  {
    id: "admin" as UserRole,
    icon: ShieldCheck,
    title: "For Administrators",
    description: "Oversee and validate the entire internship process",
    features: [
      "Validate internship placements",
      "Generate official documents automatically",
      "Monitor placement statistics",
      "Manage university-company relationships"
    ],
    color: "primary"
  }
]

export function LandingRoles({ onSelectRole }: LandingRolesProps) {
  return (
    <section id="roles" className="px-6 py-20 lg:px-12 lg:py-28 bg-muted/50">
      <div className="mx-auto max-w-6xl">
        <div className="text-center">
          <h2 className="text-3xl font-bold tracking-tight text-foreground sm:text-4xl text-balance">
            Built for Everyone in the Ecosystem
          </h2>
          <p className="mx-auto mt-4 max-w-2xl text-muted-foreground text-pretty">
            Whether you&apos;re a student, company, or university administrator, Stag.io has the tools you need.
          </p>
        </div>

        <div className="mt-16 grid gap-8 lg:grid-cols-3">
          {roles.map((role) => (
            <div 
              key={role.id}
              className="group relative flex flex-col rounded-2xl border border-border bg-card p-8 transition-all hover:border-primary/50 hover:shadow-xl"
            >
              <div className={`mb-6 inline-flex h-14 w-14 items-center justify-center rounded-xl ${
                role.color === "accent" ? "bg-accent/10 text-accent" : "bg-primary/10 text-primary"
              }`}>
                <role.icon className="h-7 w-7" />
              </div>
              
              <h3 className="text-xl font-bold text-card-foreground">{role.title}</h3>
              <p className="mt-2 text-muted-foreground">{role.description}</p>
              
              <ul className="mt-6 flex-1 space-y-3">
                {role.features.map((feature) => (
                  <li key={feature} className="flex items-start gap-3">
                    <CheckCircle2 className={`h-5 w-5 mt-0.5 flex-shrink-0 ${
                      role.color === "accent" ? "text-accent" : "text-primary"
                    }`} />
                    <span className="text-sm text-muted-foreground">{feature}</span>
                  </li>
                ))}
              </ul>
              
              <Button 
                className={`mt-8 w-full ${
                  role.color === "accent" 
                    ? "bg-accent hover:bg-accent/90 text-accent-foreground" 
                    : "bg-primary hover:bg-primary/90 text-primary-foreground"
                }`}
                onClick={() => onSelectRole(role.id)}
              >
                Get Started
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
