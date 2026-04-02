"use client"

import { Button } from "@/components/ui/button"
import { GraduationCap, Building2, ShieldCheck, ArrowRight } from "lucide-react"

interface LandingHeroProps {
  onLogin: () => void
  onGetStarted: () => void
}

export function LandingHero({ onLogin, onGetStarted }: LandingHeroProps) {
  return (
    <section className="relative overflow-hidden">
      {/* Background gradient */}
      <div className="absolute inset-0 bg-gradient-to-br from-sidebar via-sidebar to-primary/20" />
      
      {/* Grid pattern overlay */}
      <div 
        className="absolute inset-0 opacity-5"
        style={{
          backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`
        }}
      />

      <div className="relative">
        {/* Navigation */}
        <nav className="flex items-center justify-between px-6 py-4 lg:px-12">
          <div className="flex items-center gap-2">
            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary">
              <GraduationCap className="h-6 w-6 text-primary-foreground" />
            </div>
            <span className="text-xl font-bold text-sidebar-foreground">Stag.io</span>
          </div>
          
          <div className="hidden md:flex items-center gap-8">
            <a href="#features" className="text-sm text-sidebar-foreground/80 hover:text-sidebar-foreground transition-colors">
              Features
            </a>
            <a href="#roles" className="text-sm text-sidebar-foreground/80 hover:text-sidebar-foreground transition-colors">
              For Who
            </a>
            <a href="#about" className="text-sm text-sidebar-foreground/80 hover:text-sidebar-foreground transition-colors">
              About
            </a>
          </div>

          <Button 
            variant="outline" 
            onClick={onLogin}
            className="border-sidebar-border bg-transparent text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
          >
            Sign In
          </Button>
        </nav>

        {/* Hero Content */}
        <div className="px-6 py-20 lg:px-12 lg:py-32">
          <div className="mx-auto max-w-4xl text-center">
            <div className="mb-6 inline-flex items-center gap-2 rounded-full border border-sidebar-border bg-sidebar-accent/50 px-4 py-2">
              <span className="h-2 w-2 rounded-full bg-accent animate-pulse" />
              <span className="text-sm text-sidebar-foreground/80">University-Enterprise Bridge</span>
            </div>
            
            <h1 className="text-4xl font-bold tracking-tight text-sidebar-foreground sm:text-5xl lg:text-6xl text-balance">
              Connecting Students with{" "}
              <span className="text-accent">Career Opportunities</span>
            </h1>
            
            <p className="mx-auto mt-6 max-w-2xl text-lg text-sidebar-foreground/70 leading-relaxed text-pretty">
              Stag.io streamlines internship management between students, companies, and universities. 
              Find the perfect match, digitize agreements, and track placements effortlessly.
            </p>

            <div className="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
              <Button 
                size="lg" 
                onClick={onGetStarted}
                className="w-full sm:w-auto bg-primary hover:bg-primary/90 text-primary-foreground"
              >
                Get Started
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
              <Button 
                size="lg" 
                variant="outline"
                className="w-full sm:w-auto border-sidebar-border bg-transparent text-sidebar-foreground hover:bg-sidebar-accent"
              >
                Learn More
              </Button>
            </div>

            {/* Stats */}
            <div className="mt-16 grid grid-cols-3 gap-8 border-t border-sidebar-border pt-8">
              <div>
                <div className="flex items-center justify-center gap-2 text-accent">
                  <GraduationCap className="h-5 w-5" />
                  <span className="text-2xl font-bold text-sidebar-foreground">500+</span>
                </div>
                <p className="mt-1 text-sm text-sidebar-foreground/60">Students Placed</p>
              </div>
              <div>
                <div className="flex items-center justify-center gap-2 text-accent">
                  <Building2 className="h-5 w-5" />
                  <span className="text-2xl font-bold text-sidebar-foreground">50+</span>
                </div>
                <p className="mt-1 text-sm text-sidebar-foreground/60">Partner Companies</p>
              </div>
              <div>
                <div className="flex items-center justify-center gap-2 text-accent">
                  <ShieldCheck className="h-5 w-5" />
                  <span className="text-2xl font-bold text-sidebar-foreground">98%</span>
                </div>
                <p className="mt-1 text-sm text-sidebar-foreground/60">Success Rate</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
