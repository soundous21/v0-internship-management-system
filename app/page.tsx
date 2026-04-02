"use client"

import { useState } from "react"
import { LandingHero } from "@/components/landing/landing-hero"
import { LandingFeatures } from "@/components/landing/landing-features"
import { LandingRoles } from "@/components/landing/landing-roles"
import { LandingFooter } from "@/components/landing/landing-footer"
import { AuthModal } from "@/components/auth/auth-modal"

export type UserRole = "student" | "company" | "admin"

export default function Home() {
  const [authModalOpen, setAuthModalOpen] = useState(false)
  const [selectedRole, setSelectedRole] = useState<UserRole>("student")
  const [authMode, setAuthMode] = useState<"login" | "register">("login")

  const handleGetStarted = (role: UserRole) => {
    setSelectedRole(role)
    setAuthMode("register")
    setAuthModalOpen(true)
  }

  const handleLogin = () => {
    setAuthMode("login")
    setAuthModalOpen(true)
  }

  return (
    <main className="min-h-screen bg-background">
      <LandingHero onLogin={handleLogin} onGetStarted={() => handleGetStarted("student")} />
      <LandingFeatures />
      <LandingRoles onSelectRole={handleGetStarted} />
      <LandingFooter />
      
      <AuthModal
        open={authModalOpen}
        onOpenChange={setAuthModalOpen}
        mode={authMode}
        onModeChange={setAuthMode}
        selectedRole={selectedRole}
        onRoleChange={setSelectedRole}
      />
    </main>
  )
}
