import { 
  Search, 
  FileText, 
  BarChart3, 
  Tags, 
  Bell, 
  Shield 
} from "lucide-react"

const features = [
  {
    icon: Search,
    title: "Smart Matching",
    description: "Find the perfect internship based on your skills, location, and preferences with our intelligent filtering system."
  },
  {
    icon: FileText,
    title: "Digital Agreements",
    description: "Automatically generate internship agreements (Conventions de Stage) pre-filled with all necessary information."
  },
  {
    icon: BarChart3,
    title: "Analytics Dashboard",
    description: "Track placement rates, monitor applications, and gain insights with comprehensive statistics."
  },
  {
    icon: Tags,
    title: "Skill-Based Profiles",
    description: "Create detailed profiles with technical tags to help companies find the right candidates."
  },
  {
    icon: Bell,
    title: "Real-time Notifications",
    description: "Stay updated with instant alerts for new offers, application status changes, and approvals."
  },
  {
    icon: Shield,
    title: "Secure & Centralized",
    description: "All data is securely stored and accessible to authorized users for seamless workflow management."
  }
]

export function LandingFeatures() {
  return (
    <section id="features" className="px-6 py-20 lg:px-12 lg:py-28 bg-background">
      <div className="mx-auto max-w-6xl">
        <div className="text-center">
          <h2 className="text-3xl font-bold tracking-tight text-foreground sm:text-4xl text-balance">
            Everything You Need for Internship Management
          </h2>
          <p className="mx-auto mt-4 max-w-2xl text-muted-foreground text-pretty">
            A comprehensive platform designed to simplify the internship process for everyone involved.
          </p>
        </div>

        <div className="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          {features.map((feature) => (
            <div 
              key={feature.title}
              className="group relative rounded-xl border border-border bg-card p-6 transition-all hover:border-primary/50 hover:shadow-lg"
            >
              <div className="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-primary-foreground transition-colors">
                <feature.icon className="h-6 w-6" />
              </div>
              <h3 className="text-lg font-semibold text-card-foreground">{feature.title}</h3>
              <p className="mt-2 text-sm text-muted-foreground leading-relaxed">
                {feature.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
