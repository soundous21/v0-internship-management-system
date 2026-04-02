"use client"

import Link from "next/link"
import { usePathname } from "next/navigation"
import { cn } from "@/lib/utils"
import {
  GraduationCap,
  LayoutDashboard,
  Briefcase,
  FileText,
  User,
  Settings,
  LogOut,
  Building2,
  Users,
  BarChart3,
  CheckCircle,
  ClipboardList
} from "lucide-react"
import { Button } from "@/components/ui/button"
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip"

const studentNav = [
  { href: "/dashboard/student", label: "Dashboard", icon: LayoutDashboard },
  { href: "/dashboard/student/offers", label: "Internship Offers", icon: Briefcase },
  { href: "/dashboard/student/applications", label: "My Applications", icon: ClipboardList },
  { href: "/dashboard/student/profile", label: "My Profile", icon: User },
]

const companyNav = [
  { href: "/dashboard/company", label: "Dashboard", icon: LayoutDashboard },
  { href: "/dashboard/company/offers", label: "Manage Offers", icon: Briefcase },
  { href: "/dashboard/company/candidates", label: "Candidates", icon: Users },
  { href: "/dashboard/company/profile", label: "Company Profile", icon: Building2 },
]

const adminNav = [
  { href: "/dashboard/admin", label: "Dashboard", icon: LayoutDashboard },
  { href: "/dashboard/admin/agreements", label: "Agreements", icon: FileText },
  { href: "/dashboard/admin/validations", label: "Validations", icon: CheckCircle },
  { href: "/dashboard/admin/statistics", label: "Statistics", icon: BarChart3 },
]

export function DashboardSidebar() {
  const pathname = usePathname()
  
  // Determine which nav to show based on path
  let navItems = studentNav
  let roleIcon = GraduationCap
  let roleLabel = "Student"
  
  if (pathname.includes("/company")) {
    navItems = companyNav
    roleIcon = Building2
    roleLabel = "Company"
  } else if (pathname.includes("/admin")) {
    navItems = adminNav
    roleIcon = CheckCircle
    roleLabel = "Admin"
  }

  const RoleIcon = roleIcon

  return (
    <TooltipProvider delayDuration={0}>
      <aside className="fixed inset-y-0 left-0 z-50 flex w-16 flex-col bg-sidebar border-r border-sidebar-border lg:w-64">
        {/* Logo */}
        <div className="flex h-16 items-center gap-2 border-b border-sidebar-border px-4">
          <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-sidebar-primary">
            <GraduationCap className="h-6 w-6 text-sidebar-primary-foreground" />
          </div>
          <span className="hidden text-xl font-bold text-sidebar-foreground lg:block">Stag.io</span>
        </div>

        {/* Role Badge */}
        <div className="hidden border-b border-sidebar-border p-4 lg:block">
          <div className="flex items-center gap-3 rounded-lg bg-sidebar-accent p-3">
            <RoleIcon className="h-5 w-5 text-sidebar-primary" />
            <div>
              <p className="text-sm font-medium text-sidebar-foreground">{roleLabel} Portal</p>
              <p className="text-xs text-sidebar-foreground/60">Welcome back</p>
            </div>
          </div>
        </div>

        {/* Navigation */}
        <nav className="flex-1 space-y-1 p-2">
          {navItems.map((item) => {
            const isActive = pathname === item.href
            return (
              <Tooltip key={item.href}>
                <TooltipTrigger asChild>
                  <Link
                    href={item.href}
                    className={cn(
                      "flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors",
                      isActive
                        ? "bg-sidebar-accent text-sidebar-primary"
                        : "text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground"
                    )}
                  >
                    <item.icon className="h-5 w-5 flex-shrink-0" />
                    <span className="hidden lg:block">{item.label}</span>
                  </Link>
                </TooltipTrigger>
                <TooltipContent side="right" className="lg:hidden">
                  {item.label}
                </TooltipContent>
              </Tooltip>
            )
          })}
        </nav>

        {/* Bottom Actions */}
        <div className="border-t border-sidebar-border p-2">
          <Tooltip>
            <TooltipTrigger asChild>
              <Link
                href="/dashboard/settings"
                className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground transition-colors"
              >
                <Settings className="h-5 w-5 flex-shrink-0" />
                <span className="hidden lg:block">Settings</span>
              </Link>
            </TooltipTrigger>
            <TooltipContent side="right" className="lg:hidden">
              Settings
            </TooltipContent>
          </Tooltip>
          
          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                variant="ghost"
                className="w-full justify-start gap-3 px-3 py-2.5 text-sm font-medium text-sidebar-foreground/70 hover:bg-destructive/10 hover:text-destructive"
                asChild
              >
                <Link href="/">
                  <LogOut className="h-5 w-5 flex-shrink-0" />
                  <span className="hidden lg:block">Sign Out</span>
                </Link>
              </Button>
            </TooltipTrigger>
            <TooltipContent side="right" className="lg:hidden">
              Sign Out
            </TooltipContent>
          </Tooltip>
        </div>
      </aside>
    </TooltipProvider>
  )
}
