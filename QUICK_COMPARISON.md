# شرح سريع: Company Dashboard - الفروقات بين Next.js و Symfony

## ما يوجد في Company Dashboard (المشروع الأول)؟

### 📊 1. الإحصائيات (4 بطاقات في الأعلى)
```
┌─────────────────┬─────────────────┬─────────────────┬─────────────────┐
│ 🎯 4 Offers     │ 👥 47 Apps      │ ⏰ 8 Interviews │ ✅ 3 Positions  │
│ Active Offers   │ Total Apps      │ Scheduled       │ Filled          │
└─────────────────┴─────────────────┴─────────────────┴─────────────────┘
```
- البيانات: أرقام ثابتة (hardcoded)
- في Symfony: ستكون ديناميكية من قاعدة البيانات

---

### 👥 2. المرشحون الأخيرون (Recent Candidates)
```
┌──────────────────────────────────────────────────────────┐
│ Recent Candidates                                        │
│ Latest applications to your offers                       │
│                                                          │
│ 1. Ahmed Benali                          ✅  ❌          │
│    Frontend Developer Intern - USTHB                    │
│    React, TypeScript, Tailwind                          │
│    2 hours ago                                          │
│                                                          │
│ 2. Fatima Zohra                          ✅  ❌          │
│    Backend Developer - ESI Algiers                      │
│    Python, Django, PostgreSQL                          │
│    5 hours ago                                          │
│                                                          │
│ 3. Mohamed Karim                         ✅  ❌          │
│    Full Stack Developer - USTHB                         │
│    Node.js, React, MongoDB                             │
│    1 day ago                                            │
│                                                          │
│ 4. Sara Benmoussa                        ✅  ❌          │
│    Frontend Developer - U. Constantine                  │
│    Vue.js, JavaScript, CSS                             │
│    2 days ago                           [View All →]    │
└──────────────────────────────────────────────────────────┘
```

**تفاصيل المرشح:**
- الاسم + صورة الملف (Avatar)
- الوظيفة المتقدم لها
- الجامعة
- المهارات (Skills)
- وقت التقديم (appliedAt)
- الحالة (status): new, reviewed, interview
- زرين: قبول ✅ ورفض ❌

---

### 💼 3. الفرص النشطة (Active Offers)
```
┌──────────────────────────────────────────────────────────┐
│ Active Offers                                            │
│ Your current internship positions        [Manage →]     │
│                                                          │
│ 1. Frontend Developer Intern                            │
│    👥 18 applications    👁️ 124 views                    │
│    Deadline: April 30, 2026              [View]         │
│                                                          │
│ 2. Backend Developer Intern                             │
│    👥 12 applications    👁️ 89 views                     │
│    Deadline: May 15, 2026                [View]         │
│                                                          │
│ 3. Full Stack Developer                                 │
│    👥 9 applications     👁️ 67 views                     │
│    Deadline: April 20, 2026              [View]         │
│                                                          │
│ 4. DevOps Intern 🔶 Closing Soon                        │
│    👥 8 applications     👁️ 45 views                     │
│    Deadline: April 10, 2026              [View]         │
└──────────────────────────────────────────────────────────┘
```

**تفاصيل الفرصة:**
- العنوان (title)
- عدد التطبيقات (applications count)
- عدد المشاهدات (views count)
- تاريخ آخر موعد (deadline)
- الحالة (status): active أو closing
- زر View لعرض التفاصيل

---

## 🔄 كيفية التحويل إلى Symfony

### النقاط الرئيسية:

#### 1️⃣ **البيانات (Data)**
```javascript
// ❌ في Next.js (ثابتة)
const stats = [
  { label: "Active Offers", value: 4 },
  ...
];
```

```php
// ✅ في Symfony (ديناميكية)
$stats = [
    'activeOffers' => $this->getDoctrine()
        ->getRepository(Offer::class)
        ->countByCompany($company->getId()),
];
```

---

#### 2️⃣ **الملفات**
```
❌ Next.js:  app/dashboard/company/page.tsx (ملف واحد فقط)

✅ Symfony:
   - src/Controller/CompanyController.php (المنطق)
   - templates/company/dashboard.html.twig (HTML)
   - config/routes/company.yaml (المسارات)
   - src/Repository/OfferRepository.php (قاعدة البيانات)
```

---

#### 3️⃣ **المنطق**
```tsx
// ❌ في Next.js
export default function CompanyDashboard() {
  return (
    <div>
      {stats.map((stat) => (...))} {/* إظهار البيانات الثابتة */}
    </div>
  );
}
```

```php
// ✅ في Symfony
public function dashboard(): Response
{
    // 1. جلب البيانات من قاعدة البيانات
    $stats = $this->getStats();
    $candidates = $this->getCandidates();
    
    // 2. تمرير البيانات للـ Template
    return $this->render('company/dashboard.html.twig', [
        'stats' => $stats,
        'candidates' => $candidates,
    ]);
}
```

---

#### 4️⃣ **الـ Template (HTML)**
```jsx
// ❌ في Next.js: JSX
{stats.map((stat) => (
  <Card key={stat.label}>
    <p>{stat.value}</p>
    <p>{stat.label}</p>
  </Card>
))}
```

```twig
{# ✅ في Symfony: Twig #}
{% for stat in stats %}
<div class="card">
  <p>{{ stat.value }}</p>
  <p>{{ stat.label }}</p>
</div>
{% endfor %}
```

---

## 📋 الخطوات العملية

### الخطوة 1: إنشاء Entities
```bash
php bin/console make:entity Company
php bin/console make:entity Offer
php bin/console make:entity Application
```

### الخطوة 2: إنشاء Repository
```bash
php bin/console make:repository CompanyRepository
```

### الخطوة 3: عمل Migration
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### الخطوة 4: إنشاء Controller
```bash
php bin/console make:controller CompanyController
```

### الخطوة 5: إضافة Methods
```php
// src/Controller/CompanyController.php
public function dashboard(): Response
{
    // البيانات من قاعدة البيانات
    $company = $this->getUser()->getCompany();
    $stats = [...];
    $candidates = [...];
    
    return $this->render('company/dashboard.html.twig', [
        'stats' => $stats,
        'candidates' => $candidates,
    ]);
}
```

### الخطوة 6: إنشاء Template
```twig
{# templates/company/dashboard.html.twig #}
{% extends 'base.html.twig' %}

{% block content %}
    {# محتوى الصفحة #}
{% endblock %}
```

### الخطوة 7: إضافة الـ Route
```yaml
# config/routes/company.yaml
company_dashboard:
  path: /dashboard/company
  controller: App\Controller\CompanyController::dashboard
```

---

## 🎯 الفوائس الرئيسية

| الجانب | Next.js | Symfony |
|--------|---------|---------|
| **سهولة البدء** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **الأداء** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **الأمان** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **قابلية التوسع** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **إدارة البيانات** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **المجتمع** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |

---

## 💡 نصائح مهمة

1. **ابدأ بـ Entities**: عرّف الجداول والعلاقات أولاً
2. **استخدم Repository Pattern**: اكتب queries معقدة هناك
3. **فصل المنطق عن الـ Template**: Controller → Database
4. **استخدم Twig Filters**: للتنسيق والمعالجة
5. **اختبر دائماً**: Unit Tests مهمة جداً

---

**جاهز للبدء؟ اقرأ DEVELOPER_GUIDE_AR.md للمزيد من التفاصيل! 🚀**
