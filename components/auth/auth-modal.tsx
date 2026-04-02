"use client"

import { useState } from "react"
import { useRouter } from "next/navigation"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { GraduationCap, Building2, ShieldCheck, Loader2, Eye, EyeOff } from "lucide-react"
import type { UserRole } from "@/app/page"

interface AuthModalProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  mode: "login" | "register"
  onModeChange: (mode: "login" | "register") => void
  selectedRole: UserRole
  onRoleChange: (role: UserRole) => void
}

const roleConfig = {
  student: {
    icon: GraduationCap,
    label: "Student",
    description: "Find internships and build your career"
  },
  company: {
    icon: Building2,
    label: "Company",
    description: "Post offers and find talented interns"
  },
  admin: {
    icon: ShieldCheck,
    label: "Administrator",
    description: "Manage and validate internships"
  }
}

export function AuthModal({
  open,
  onOpenChange,
  mode,
  onModeChange,
  selectedRole,
  onRoleChange
}: AuthModalProps) {
  const router = useRouter()
  const [isLoading, setIsLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [formData, setFormData] = useState({
    email: "",
    password: "",
    name: "",
    confirmPassword: ""
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsLoading(true)
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    setIsLoading(false)
    onOpenChange(false)
    
    // Navigate to appropriate dashboard
    if (selectedRole === "student") {
      router.push("/dashboard/student")
    } else if (selectedRole === "company") {
      router.push("/dashboard/company")
    } else {
      router.push("/dashboard/admin")
    }
  }

  const RoleIcon = roleConfig[selectedRole].icon

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
              <RoleIcon className="h-5 w-5 text-primary" />
            </div>
            <span>{mode === "login" ? "Welcome Back" : "Create Account"}</span>
          </DialogTitle>
          <DialogDescription>
            {mode === "login" 
              ? "Sign in to access your dashboard" 
              : `Register as ${roleConfig[selectedRole].label.toLowerCase()}`
            }
          </DialogDescription>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="space-y-4">
          {/* Role Selector (only for register) */}
          {mode === "register" && (
            <div className="space-y-2">
              <Label>I am a...</Label>
              <div className="grid grid-cols-3 gap-2">
                {(Object.keys(roleConfig) as UserRole[]).map((role) => {
                  const Icon = roleConfig[role].icon
                  return (
                    <button
                      key={role}
                      type="button"
                      onClick={() => onRoleChange(role)}
                      className={`flex flex-col items-center gap-1 rounded-lg border p-3 text-xs transition-all ${
                        selectedRole === role
                          ? "border-primary bg-primary/5 text-primary"
                          : "border-border hover:border-primary/50"
                      }`}
                    >
                      <Icon className="h-5 w-5" />
                      <span className="font-medium">{roleConfig[role].label}</span>
                    </button>
                  )
                })}
              </div>
            </div>
          )}

          {/* Name (only for register) */}
          {mode === "register" && (
            <div className="space-y-2">
              <Label htmlFor="name">
                {selectedRole === "company" ? "Company Name" : "Full Name"}
              </Label>
              <Input
                id="name"
                placeholder={selectedRole === "company" ? "Tech Corp Inc." : "John Doe"}
                value={formData.name}
                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                required
              />
            </div>
          )}

          {/* Email */}
          <div className="space-y-2">
            <Label htmlFor="email">Email</Label>
            <Input
              id="email"
              type="email"
              placeholder={selectedRole === "student" ? "student@univ.edu" : "email@example.com"}
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
              required
            />
          </div>

          {/* Password */}
          <div className="space-y-2">
            <Label htmlFor="password">Password</Label>
            <div className="relative">
              <Input
                id="password"
                type={showPassword ? "text" : "password"}
                placeholder="Enter your password"
                value={formData.password}
                onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                required
              />
              <button
                type="button"
                onClick={() => setShowPassword(!showPassword)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              >
                {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
              </button>
            </div>
          </div>

          {/* Confirm Password (only for register) */}
          {mode === "register" && (
            <div className="space-y-2">
              <Label htmlFor="confirmPassword">Confirm Password</Label>
              <Input
                id="confirmPassword"
                type="password"
                placeholder="Confirm your password"
                value={formData.confirmPassword}
                onChange={(e) => setFormData({ ...formData, confirmPassword: e.target.value })}
                required
              />
            </div>
          )}

          {/* Submit Button */}
          <Button type="submit" className="w-full" disabled={isLoading}>
            {isLoading ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                {mode === "login" ? "Signing in..." : "Creating account..."}
              </>
            ) : (
              mode === "login" ? "Sign In" : "Create Account"
            )}
          </Button>

          {/* Toggle Mode */}
          <p className="text-center text-sm text-muted-foreground">
            {mode === "login" ? (
              <>
                Don&apos;t have an account?{" "}
                <button
                  type="button"
                  onClick={() => onModeChange("register")}
                  className="text-primary hover:underline"
                >
                  Sign up
                </button>
              </>
            ) : (
              <>
                Already have an account?{" "}
                <button
                  type="button"
                  onClick={() => onModeChange("login")}
                  className="text-primary hover:underline"
                >
                  Sign in
                </button>
              </>
            )}
          </p>
        </form>
      </DialogContent>
    </Dialog>
  )
}
