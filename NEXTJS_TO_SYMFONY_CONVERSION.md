# تحويل Company Dashboard من Next.js إلى Symfony - مقارنة كاملة

## الجزء 1: البنية والملفات

### Next.js:
```
app/dashboard/company/page.tsx (ملف واحد يحتوي على كل شيء)
- المنطق
- البيانات
- JSX (HTML)
- CSS (Tailwind)
```

### Symfony:
```
src/Controller/CompanyController.php (المنطق)
templates/company/dashboard.html.twig (HTML/Template)
config/routes/company.yaml (المسارات)
```

---

## الجزء 2: البيانات والمنطق

### في Next.js:
```javascript
// البيانات محفوظة مباشرة في الملف
const stats = [
  { label: "Active Offers", value: 4, icon: Briefcase, color: "text-primary" },
  ...
];

const recentCandidates = [
  { id: 1, name: "Ahmed Benali", ... },
  ...
];
```

### في Symfony:
```php
// البيانات تأتي من قاعدة البيانات
public function dashboard(CompanyRepository $companyRepo): Response
{
    $company = $this->getUser()->getCompany();
    
    // إحصائيات ديناميكية
    $stats = [
        'activeOffers' => $companyRepo->countActiveOffers($company->getId()),
        'totalApplications' => $companyRepo->countApplications($company->getId()),
        'interviewsScheduled' => $companyRepo->countScheduledInterviews($company->getId()),
        'positionsFilled' => $companyRepo->countFilledPositions($company->getId()),
    ];
    
    // بيانات ديناميكية من قاعدة البيانات
    $recentCandidates = $companyRepo->findRecentCandidates($company->getId(), 4);
    $activeOffers = $companyRepo->findActiveOffers($company->getId());
    
    return $this->render('company/dashboard.html.twig', [
        'stats' => $stats,
        'recentCandidates' => $recentCandidates,
        'activeOffers' => $activeOffers,
    ]);
}
```

---

## الجزء 3: الواجهة (Template)

### في Next.js (JSX):
```tsx
export default function CompanyDashboard() {
  return (
    <div className="ml-16 lg:ml-64">
      <div className="p-6 lg:p-8">
        {/* Header */}
        <div className="mb-8 flex flex-col gap-4">
          <h1 className="text-2xl font-bold">Company Dashboard</h1>
          <Button asChild>
            <Link href="/dashboard/company/offers/new">
              Post New Offer
            </Link>
          </Button>
        </div>

        {/* Stats Grid */}
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {stats.map((stat) => (
            <Card key={stat.label}>
              <CardContent className="flex items-center gap-4 p-6">
                <stat.icon className="h-6 w-6" />
                <div>
                  <p className="text-2xl font-bold">{stat.value}</p>
                  <p className="text-sm">{stat.label}</p>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </div>
  );
}
```

### في Symfony (Twig):
```twig
{% extends 'base.html.twig' %}

{% block title %}Company Dashboard - Stag.io{% endblock %}

{% block content %}
<div class="ml-16 lg:ml-64">
    <div class="p-6 lg:p-8">
        {# Header #}
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground lg:text-3xl">Company Dashboard</h1>
                <p class="mt-1 text-muted-foreground">Manage your internship offers and candidates</p>
            </div>
            <a href="{{ path('company_create_offer') }}" class="btn btn-primary">
                <i class="icon-briefcase mr-2"></i>
                Post New Offer
            </a>
        </div>

        {# Stats Grid #}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {# Active Offers #}
            <div class="card">
                <div class="card-content flex items-center gap-4 p-6">
                    <div class="rounded-lg bg-muted p-3 text-primary">
                        <i class="icon-briefcase h-6 w-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-foreground">{{ stats.activeOffers }}</p>
                        <p class="text-sm text-muted-foreground">Active Offers</p>
                    </div>
                </div>
            </div>

            {# Total Applications #}
            <div class="card">
                <div class="card-content flex items-center gap-4 p-6">
                    <div class="rounded-lg bg-muted p-3 text-accent">
                        <i class="icon-users h-6 w-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-foreground">{{ stats.totalApplications }}</p>
                        <p class="text-sm text-muted-foreground">Total Applications</p>
                    </div>
                </div>
            </div>

            {# Interviews Scheduled #}
            <div class="card">
                <div class="card-content flex items-center gap-4 p-6">
                    <div class="rounded-lg bg-muted p-3 text-yellow-500">
                        <i class="icon-clock h-6 w-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-foreground">{{ stats.interviewsScheduled }}</p>
                        <p class="text-sm text-muted-foreground">Interviews Scheduled</p>
                    </div>
                </div>
            </div>

            {# Positions Filled #}
            <div class="card">
                <div class="card-content flex items-center gap-4 p-6">
                    <div class="rounded-lg bg-muted p-3 text-green-500">
                        <i class="icon-file-check h-6 w-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-foreground">{{ stats.positionsFilled }}</p>
                        <p class="text-sm text-muted-foreground">Positions Filled</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">
            {# Recent Candidates #}
            <div class="card">
                <div class="card-header flex flex-row items-center justify-between">
                    <div>
                        <h2 class="card-title">Recent Candidates</h2>
                        <p class="card-description">Latest applications to your offers</p>
                    </div>
                    <a href="{{ path('company_candidates') }}" class="btn btn-ghost btn-sm">
                        View All <i class="icon-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-content space-y-4">
                    {% for candidate in recentCandidates %}
                    <div class="flex items-center gap-4 rounded-lg border border-border p-3">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                            {{ candidate.name|first }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-foreground truncate">{{ candidate.name }}</p>
                                {% if candidate.status == 'new' %}
                                <span class="badge badge-accent text-xs">New</span>
                                {% endif %}
                            </div>
                            <p class="text-sm text-muted-foreground truncate">{{ candidate.position }}</p>
                            <p class="text-xs text-muted-foreground">{{ candidate.university }}</p>
                        </div>
                        <div class="flex gap-1">
                            <button class="btn btn-icon btn-ghost h-8 w-8 text-accent">
                                <i class="icon-check-circle h-4 w-4"></i>
                            </button>
                            <button class="btn btn-icon btn-ghost h-8 w-8 text-destructive">
                                <i class="icon-x-circle h-4 w-4"></i>
                            </button>
                        </div>
                    </div>
                    {% endfor %}
                </div>
            </div>

            {# Active Offers #}
            <div class="card">
                <div class="card-header flex flex-row items-center justify-between">
                    <div>
                        <h2 class="card-title">Active Offers</h2>
                        <p class="card-description">Your current internship positions</p>
                    </div>
                    <a href="{{ path('company_offers') }}" class="btn btn-ghost btn-sm">
                        Manage <i class="icon-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-content space-y-4">
                    {% for offer in activeOffers %}
                    <div class="flex items-center justify-between rounded-lg border border-border p-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-medium text-foreground">{{ offer.title }}</h3>
                                {% if offer.status == 'closing' %}
                                <span class="badge badge-outline text-yellow-600 border-yellow-600">
                                    Closing Soon
                                </span>
                                {% endif %}
                            </div>
                            <div class="mt-1 flex items-center gap-4 text-sm text-muted-foreground">
                                <span class="flex items-center gap-1">
                                    <i class="icon-users h-3.5 w-3.5"></i>
                                    {{ offer.applications }} applications
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="icon-eye h-3.5 w-3.5"></i>
                                    {{ offer.views }} views
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Deadline: {{ offer.deadline|date('F d, Y') }}
                            </p>
                        </div>
                        <a href="{{ path('company_offer_view', {id: offer.id}) }}" class="btn btn-outline btn-sm">
                            View
                        </a>
                    </div>
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}
```

---

## الجزء 4: المسارات (Routes)

### في Next.js:
```
المسارات تُنشأ تلقائياً من بنية المجلدات:
/app/dashboard/company/page.tsx → /dashboard/company
/app/dashboard/company/offers/page.tsx → /dashboard/company/offers
```

### في Symfony:
```yaml
# config/routes/company.yaml
company_dashboard:
  path: /dashboard/company
  controller: App\Controller\CompanyController::dashboard
  methods: [GET]

company_create_offer:
  path: /dashboard/company/offers/new
  controller: App\Controller\CompanyController::createOffer
  methods: [GET, POST]

company_offers:
  path: /dashboard/company/offers
  controller: App\Controller\CompanyController::listOffers
  methods: [GET]

company_candidates:
  path: /dashboard/company/candidates
  controller: App\Controller\CompanyController::listCandidates
  methods: [GET]

company_offer_view:
  path: /dashboard/company/offers/{id}
  controller: App\Controller\CompanyController::viewOffer
  methods: [GET]
```

---

## الجزء 5: مقارنة سريعة

| الميزة | Next.js | Symfony |
|--------|---------|---------|
| **الملفات** | ملف واحد (tsx) | 3+ ملفات (Controller, Template, Route) |
| **البيانات** | Hardcoded في الملف | من قاعدة البيانات |
| **البنية** | ديناميكية (file-based) | منظمة (folder-based) |
| **الأداء** | SSR مدمج | يحتاج إعداد إضافي |
| **الأمان** | بسيط | متقدم (Security Bundle) |
| **قاعدة البيانات** | تحتاج إعداد منفصل | Doctrine ORM مدمج |
| **التحقق** | Zod أو JSON Schema | Symfony Validator |
| **الاختبار** | Jest | PHPUnit |

---

## الجزء 6: الخطوات العملية للتحويل

### 1. إنشاء Entity (User, Company, Offer, Application)
```bash
php bin/console make:entity
```

### 2. إنشاء Repository
```bash
php bin/console make:repository
```

### 3. إنشاء Migration
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 4. إنشاء Controller مع Methods
```php
public function dashboard(): Response
{
    // جلب البيانات
    // تمريرها للـ Template
    return $this->render('company/dashboard.html.twig', [...]);
}
```

### 5. إنشاء Template Twig
```twig
{% extends 'base.html.twig' %}
{% block content %}
    {# محتوى الصفحة #}
{% endblock %}
```

### 6. تعريف المسارات في YAML
```yaml
company_dashboard:
  path: /dashboard/company
  controller: App\Controller\CompanyController::dashboard
```

---

## الخلاصة

**الفروقات الأساسية:**
1. Next.js ملف واحد ← Symfony ملفات منفصلة
2. Next.js بيانات ثابتة ← Symfony بيانات ديناميكية
3. Next.js JSX ← Symfony Twig Templates
4. Next.js file-based routing ← Symfony explicit routing

كل نمط له مميزاته:
- **Next.js**: أسرع للمشاريع البسيطة
- **Symfony**: أكثر مرونة وأماناً للمشاريع الكبيرة
