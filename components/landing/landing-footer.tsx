import { GraduationCap } from "lucide-react"

export function LandingFooter() {
  return (
    <footer id="about" className="border-t border-border bg-card px-6 py-12 lg:px-12">
      <div className="mx-auto max-w-6xl">
        <div className="grid gap-8 md:grid-cols-4">
          {/* Brand */}
          <div className="md:col-span-2">
            <div className="flex items-center gap-2">
              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary">
                <GraduationCap className="h-6 w-6 text-primary-foreground" />
              </div>
              <span className="text-xl font-bold text-foreground">Stag.io</span>
            </div>
            <p className="mt-4 max-w-md text-sm text-muted-foreground leading-relaxed">
              Stag.io is an internship management platform developed as part of the L3TI Workshop 2025-2026. 
              It connects students with companies while streamlining administrative processes for universities.
            </p>
          </div>

          {/* Links */}
          <div>
            <h4 className="font-semibold text-foreground">Platform</h4>
            <ul className="mt-4 space-y-2">
              <li>
                <a href="#features" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Features
                </a>
              </li>
              <li>
                <a href="#roles" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  For Students
                </a>
              </li>
              <li>
                <a href="#roles" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  For Companies
                </a>
              </li>
              <li>
                <a href="#roles" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  For Admins
                </a>
              </li>
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-semibold text-foreground">Contact</h4>
            <ul className="mt-4 space-y-2">
              <li className="text-sm text-muted-foreground">
                University of Technology
              </li>
              <li className="text-sm text-muted-foreground">
                Department of Computer Science
              </li>
              <li>
                <a href="mailto:contact@stagio.edu" className="text-sm text-primary hover:underline">
                  contact@stagio.edu
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-12 flex flex-col items-center justify-between gap-4 border-t border-border pt-8 md:flex-row">
          <p className="text-sm text-muted-foreground">
            2025-2026 Stag.io. L3TI Workshop Project.
          </p>
          <p className="text-sm text-muted-foreground">
            Supervised by Dr. Adil Chekati
          </p>
        </div>
      </div>
    </footer>
  )
}
