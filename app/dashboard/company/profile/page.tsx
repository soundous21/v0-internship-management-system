"use client"

import { useState } from "react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Textarea } from "@/components/ui/textarea"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Building2,
  MapPin,
  Globe,
  Mail,
  Phone,
  Users,
  Save,
  Upload
} from "lucide-react"

const wilayas = ["Algiers", "Oran", "Constantine", "Annaba", "Setif", "Blida", "Tizi Ouzou"]
const industries = ["Technology", "Finance", "Healthcare", "Education", "Manufacturing", "Consulting", "E-commerce", "Telecommunications"]
const sizes = ["1-10", "11-50", "51-200", "201-500", "500+"]

export default function CompanyProfilePage() {
  const [profile, setProfile] = useState({
    name: "TechCorp Algeria",
    description: "TechCorp Algeria is a leading technology company specializing in software development, cloud solutions, and digital transformation. We help businesses modernize their operations through innovative technology solutions.",
    industry: "Technology",
    size: "51-200",
    wilaya: "Algiers",
    address: "123 Tech Street, Bab Ezzouar",
    website: "https://techcorp-dz.com",
    email: "hr@techcorp-dz.com",
    phone: "+213 21 123 456"
  })

  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8 flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-foreground lg:text-3xl">Company Profile</h1>
            <p className="mt-1 text-muted-foreground">Manage your company information</p>
          </div>
          <Button>
            <Save className="mr-2 h-4 w-4" />
            Save Changes
          </Button>
        </div>

        <div className="grid gap-6 lg:grid-cols-3">
          {/* Main Form */}
          <div className="lg:col-span-2 space-y-6">
            {/* Company Logo */}
            <Card>
              <CardHeader>
                <CardTitle>Company Logo</CardTitle>
                <CardDescription>Upload your company logo</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="flex items-center gap-6">
                  <div className="flex h-24 w-24 items-center justify-center rounded-xl border-2 border-dashed border-border bg-muted">
                    <Building2 className="h-10 w-10 text-muted-foreground" />
                  </div>
                  <div>
                    <Button variant="outline">
                      <Upload className="mr-2 h-4 w-4" />
                      Upload Logo
                    </Button>
                    <p className="mt-2 text-xs text-muted-foreground">
                      PNG, JPG up to 2MB. Recommended size: 200x200px
                    </p>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Basic Information */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Building2 className="h-5 w-5" />
                  Basic Information
                </CardTitle>
                <CardDescription>Your company details</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="name">Company Name *</Label>
                  <Input
                    id="name"
                    value={profile.name}
                    onChange={(e) => setProfile({ ...profile, name: e.target.value })}
                  />
                </div>

                <div className="space-y-2">
                  <Label htmlFor="description">Description *</Label>
                  <Textarea
                    id="description"
                    value={profile.description}
                    onChange={(e) => setProfile({ ...profile, description: e.target.value })}
                    rows={5}
                    placeholder="Tell students about your company, culture, and what you do..."
                  />
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label>Industry *</Label>
                    <Select
                      value={profile.industry}
                      onValueChange={(value) => setProfile({ ...profile, industry: value })}
                    >
                      <SelectTrigger>
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {industries.map((i) => (
                          <SelectItem key={i} value={i}>{i}</SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                  <div className="space-y-2">
                    <Label>Company Size *</Label>
                    <Select
                      value={profile.size}
                      onValueChange={(value) => setProfile({ ...profile, size: value })}
                    >
                      <SelectTrigger>
                        <Users className="mr-2 h-4 w-4" />
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {sizes.map((s) => (
                          <SelectItem key={s} value={s}>{s} employees</SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Location & Contact */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <MapPin className="h-5 w-5" />
                  Location & Contact
                </CardTitle>
                <CardDescription>Where students can find and contact you</CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label>Wilaya *</Label>
                    <Select
                      value={profile.wilaya}
                      onValueChange={(value) => setProfile({ ...profile, wilaya: value })}
                    >
                      <SelectTrigger>
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {wilayas.map((w) => (
                          <SelectItem key={w} value={w}>{w}</SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="address">Address</Label>
                    <Input
                      id="address"
                      value={profile.address}
                      onChange={(e) => setProfile({ ...profile, address: e.target.value })}
                      placeholder="Street address"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="website">
                    <Globe className="mr-1 inline h-4 w-4" />
                    Website
                  </Label>
                  <Input
                    id="website"
                    type="url"
                    value={profile.website}
                    onChange={(e) => setProfile({ ...profile, website: e.target.value })}
                    placeholder="https://company.com"
                  />
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="email">
                      <Mail className="mr-1 inline h-4 w-4" />
                      Contact Email *
                    </Label>
                    <Input
                      id="email"
                      type="email"
                      value={profile.email}
                      onChange={(e) => setProfile({ ...profile, email: e.target.value })}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="phone">
                      <Phone className="mr-1 inline h-4 w-4" />
                      Phone Number
                    </Label>
                    <Input
                      id="phone"
                      value={profile.phone}
                      onChange={(e) => setProfile({ ...profile, phone: e.target.value })}
                    />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Preview */}
          <div>
            <Card className="sticky top-6">
              <CardHeader>
                <CardTitle className="text-base">Profile Preview</CardTitle>
                <CardDescription>How students will see your company</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="rounded-lg border border-border p-4">
                  <div className="flex items-center gap-3">
                    <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                      <Building2 className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-foreground">{profile.name}</h3>
                      <p className="text-sm text-muted-foreground">{profile.industry}</p>
                    </div>
                  </div>
                  
                  <p className="mt-4 text-sm text-muted-foreground line-clamp-3">
                    {profile.description}
                  </p>

                  <div className="mt-4 space-y-2 text-sm">
                    <div className="flex items-center gap-2 text-muted-foreground">
                      <MapPin className="h-4 w-4" />
                      <span>{profile.wilaya}</span>
                    </div>
                    <div className="flex items-center gap-2 text-muted-foreground">
                      <Users className="h-4 w-4" />
                      <span>{profile.size} employees</span>
                    </div>
                    {profile.website && (
                      <div className="flex items-center gap-2 text-muted-foreground">
                        <Globe className="h-4 w-4" />
                        <a href={profile.website} className="text-primary hover:underline truncate">
                          {profile.website.replace(/^https?:\/\//, "")}
                        </a>
                      </div>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  )
}
