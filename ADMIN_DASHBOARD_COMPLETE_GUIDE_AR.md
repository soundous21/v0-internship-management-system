# شرح Admin Dashboard الشامل لـ Symfony و Bootstrap

## مقدمة
Admin Dashboard هي لوحة تحكم قوية للمسؤولين توفر رؤية شاملة على نظام التدريبات الجامعية مع إحصائيات وتحليلات متقدمة.

---

## 1️⃣ الصفحة الرئيسية (Main Dashboard)

### المحتوى الأساسي:

#### أولاً: 4 بطاقات KPI (Key Performance Indicators)

```
┌─────────────────────┬──────────────────────┬─────────────────────┬──────────────────────┐
│  Total Students     │ Partner Companies   │ Active Agreements  │ Placements This Year │
│      324            │        45           │        87          │        156           │
│     +12%            │        +3           │        +8          │        +24%          │
└─────────────────────┴──────────────────────┴─────────────────────┴──────────────────────┘
```

**البيانات المطلوبة من قاعدة البيانات:**
- عدد الطلاب المسجلين
- عدد الشركات المتعاونة
- عدد الاتفاقيات النشطة
- عدد الطلاب الموظفين

#### ثانياً: رسم بياني Placement Trends (يساراً - 2/3 العرض)

**نوع الرسم البياني:** Bar Chart
**المحاور:**
- X-axis: الأشهر (Jan, Feb, Mar, Apr, May, Jun)
- Y-axis: العدد (0-50)

**البيانات:**
```
Jan: Placed=12, Pending=5
Feb: Placed=18, Pending=8
Mar: Placed=24, Pending=12
Apr: Placed=31, Pending=15
May: Placed=28, Pending=10
Jun: Placed=43, Pending=18
```

**الألوان:**
- Placed (تم التوظيف): أخضر `#00B384`
- Pending (قيد الانتظار): أزرق فاتح `#3B82F6`

#### ثالثاً: رسم Pie Chart - Student Status (يميناً - 1/3 العرض)

**نوع الرسم البياني:** Donut Chart
**البيانات:**
```
Placed: 156 (أخضر)
In Progress: 87 (أصفر)
Searching: 81 (أزرق)
```

**المتطلبات البرمجية:**
- استخدام مكتبة Chart.js أو Apex Charts
- تحديث البيانات ديناميكياً من قاعدة البيانات

---

#### رابعاً: جدول Pending Validations (يساراً)

**الهيكل:**
```
┌──────────────────────┬─────────────────────┬──────────────────────┬──────────────┐
│ Student Name         │ Position            │ Company              │ Submitted    │
├──────────────────────┼─────────────────────┼──────────────────────┼──────────────┤
│ 🔴 Ahmed Benali      │ Frontend Developer  │ TechCorp Algeria     │ 2 hours ago  │
│ ○ Fatima Zohra       │ Backend Developer   │ DataTech             │ 5 hours ago  │
│ ○ Mohamed Karim      │ Full Stack Dev      │ StartUp DZ           │ 1 day ago    │
└──────────────────────┴─────────────────────┴──────────────────────┴──────────────┘
```

**الميزات:**
- الدائرة الحمراء تشير إلى أولوية عالية
- زر "Review" لكل صف
- يعرض آخر 3-4 تحققات

#### خامساً: جدول Recent Agreements (يميناً)

**الهيكل:**
```
┌──────────────────────┬─────────────────────┬──────────────┬──────────────┐
│ Student Name         │ Company             │ Status       │ Date         │
├──────────────────────┼─────────────────────┼──────────────┼──────────────┤
│ Youssef Hamdi        │ CloudHost DZ        │ Signed ✓     │ March 28     │
│ Sara Benmoussa       │ Mobile Solutions    │ Pending ⏳    │ March 27     │
│ Karim Mansouri       │ AI Labs Algeria     │ Approved ✓   │ March 26     │
└──────────────────────┴─────────────────────┴──────────────┴──────────────┘
```

**الحالات الممكنة:**
- ✅ Signed = أخضر
- ⏳ Pending = أصفر
- ✓ Approved = أزرق

---

## 2️⃣ صفحة Validations (التحققات)

### الغرض:
مراجعة والموافقة على طلبات التدريب قبل إنشاء الاتفاقيات الرسمية.

### المحتوى:

#### أولاً: 3 بطاقات ملخص

```
┌──────────────────┬──────────────────┬──────────────────┐
│ Pending: 3       │ Approved: 1      │ Rejected: 0      │
│ ⏳ (أصفر)        │ ✓ (أزرق)        │ ✗ (أحمر)         │
└──────────────────┴──────────────────┴──────────────────┘
```

#### ثانياً: جدول التحققات الرئيسي

**الأعمدة:**
1. Student Name + University
2. Company Name + Location
3. Position
4. Duration
5. Submitted Date
6. Status Badge
7. Actions (عرض، موافقة، رفض)

**مثال:**
```
Ahmed Benali (USTHB) | TechCorp | Frontend Dev | 6 months | 3/28 | Pending | 👁️ ✅ ❌
Fatima Zohra (ESI)   | DataTech | Backend Dev  | 6 months | 3/27 | Pending | 👁️ ✅ ❌
```

#### ثالثاً: Dialog/Modal للتفاصيل

عند الضغط على "View" أو أي من الأزرار، يفتح dialog يحتوي على:

**قسم 1: معلومات الطالب**
```
🎓 Student Information
├─ Name: Ahmed Benali
├─ Email: ahmed.benali@univ.edu
├─ University: USTHB
├─ Department: Computer Science
└─ Level: L3
```

**قسم 2: معلومات الشركة**
```
🏢 Company Information
├─ Company: TechCorp Algeria
├─ Location: Algiers
└─ Supervisor: Mr. Karim Mansouri
```

**قسم 3: تفاصيل التدريب**
```
📅 Internship Details
├─ Position: Frontend Developer Intern
├─ Duration: 6 months
└─ Start Date: May 1, 2026
```

#### رابعاً: أزرار الإجراء

عند الموافقة:
- Dialog تأكيد: "هل تريد الموافقة؟ سيتم إنشاء الاتفاقية الرسمية"
- أزرار: Cancel أو "Approve & Generate Agreement"

عند الرفض:
- Dialog تأكيد: "هل تريد رفض هذا الطلب؟ سيتم إخطار الطالب والشركة"
- أزرار: Cancel أو "Reject Request"

---

## 3️⃣ صفحة Agreements (الاتفاقيات)

### الغرض:
إدارة وتنزيل اتفاقيات التدريب (Convention de Stage).

### المحتوى:

#### أولاً: 4 بطاقات ملخص

```
┌──────────────────┬──────────────────┬──────────────────┬──────────────────┐
│ Total: 6         │ Signed: 3        │ Awaiting Sig: 2  │ Pending: 1       │
│ 📄               │ ✅               │ ✏️               │ ⏳               │
└──────────────────┴──────────────────┴──────────────────┴──────────────────┘
```

#### ثانياً: شريط البحث والفلترة

```
┌────────────────────────────────────────────────────┬─────────────────┐
│ 🔍 Search by student, company, or agreement ID... │ Filter: All ▼   │
└────────────────────────────────────────────────────┴─────────────────┘
```

**خيارات الفلترة:**
- All Status
- Signed
- Approved
- Pending

#### ثالثاً: جدول الاتفاقيات

**الأعمدة:**
1. Agreement ID (مثل: CONV-2026-001)
2. Student Name
3. Company Name
4. Position
5. Period (From - To)
6. Status
7. Actions (عرض، تنزيل)

**مثال:**
```
CONV-2026-001 | Youssef Hamdi   | CloudHost DZ      | DevOps Intern | Apr 1 - Jun 30 | Signed ✓ | 👁️ ⬇️
CONV-2026-002 | Sara Benmoussa  | Mobile Solutions  | Mobile Dev    | May 1 - Aug 31 | Pending ⏳ | 👁️ ⬇️
CONV-2026-003 | Karim Mansouri  | AI Labs Algeria   | Data Science  | Apr 15 - Oct 14| Approved ✓ | 👁️ ⬇️
```

**Status Colors:**
- Signed: أخضر `#00B384`
- Approved: أزرق `#3B82F6`
- Pending: أصفر `#FCD34D`

#### رابعاً: زر التنزيل

- إذا كانت الحالة "Signed" أو "Approved": الزر مفعّل يسمح بالتنزيل
- إذا كانت "Pending": الزر معطّل (Disabled)

---

## 4️⃣ صفحة Statistics (الإحصائيات)

### الغرض:
عرض تحليلات متقدمة وتقارير شاملة عن أداء البرنامج.

### المحتوى:

#### أولاً: Selector لاختيار الفترة الزمنية

```
┌──────────────────────────────┐
│ This Month ▼                 │
├──────────────────────────────┤
│ This Month                   │
│ This Quarter                 │
│ This Year                    │
└──────────────────────────────┘
```

#### ثانياً: 4 بطاقات KPI متقدمة

```
┌──────────────────────┬──────────────────────┬──────────────────────┬──────────────────────┐
│ Registered Students  │ Students Placed      │ Partner Companies    │ Placement Rate       │
│      324             │       156            │        45            │       48%            │
│     +18%             │       +24%           │        +5            │       +8%            │
└──────────────────────┴──────────────────────┴──────────────────────┴──────────────────────┘
```

#### ثالثاً: Growth Trends Chart (يساراً - 50%)

**نوع الرسم:** Line Chart
**المتغيرات:**
- Total Students: خط أزرق
- Placements: خط أخضر

**البيانات:**
```
Sep: Students=45, Placements=12
Oct: Students=68, Placements=24
Nov: Students=92, Placements=38
...
Apr: Students=324, Placements=156
```

#### رابعاً: Industry Distribution Chart (يميناً - 50%)

**نوع الرسم:** Pie Chart
**البيانات:**
```
Technology: 45% (أزرق)
Finance: 18% (أخضر)
Telecom: 15% (أصفر)
E-commerce: 12% (بنفسجي)
Other: 10% (رمادي)
```

#### خامساً: Placement Rate by Department (يساراً - 66%)

**نوع الرسم:** Horizontal Bar Chart
**البيانات:**
```
Computer Science: 72% (124 students, 89 placed)
Software Engineering: 79% (86 students, 68 placed)
Information Systems: 66% (64 students, 42 placed)
Networks & Security: 64% (50 students, 32 placed)
```

#### سادساً: Top Skills in Demand (يميناً - 33%)

**عرض:** Progress Bars مع أرقام

```
React               89 offers ████████████████████
Python              76 offers ████████████████
Node.js             68 offers ███████████████
Java                54 offers ███████████
Docker              42 offers ████████
TypeScript          38 offers ███████
```

#### سابعاً: Top Partner Companies

**عرض:** بطاقات بتنسيق Grid

```
┌──────────────┬──────────────┬──────────────┬──────────────┬──────────────┐
│ #1           │ #2           │ #3           │ #4           │ #5           │
│              │              │              │              │              │
│ TechCorp     │ DataTech     │ CloudHost    │ StartUp DZ   │ AI Labs      │
│ 24           │ 18           │ 15           │ 12           │ 10           │
│ placements   │ placements   │ placements   │ placements   │ placements   │
│              │              │              │              │              │
│ 4.8 rating   │ 4.6 rating   │ 4.7 rating   │ 4.5 rating   │ 4.9 rating   │
└──────────────┴──────────────┴──────────────┴──────────────┴──────────────┘
```

---

## المتطلبات البرمجية Symfony + Bootstrap

### 1. Controllers المطلوبة:

```php
// AdminController.php
- dashboardAction()      // الصفحة الرئيسية
- validationsAction()    // صفحة التحققات
- agreementsAction()     // صفحة الاتفاقيات
- statisticsAction()     // صفحة الإحصائيات
- approveValidation()    // موافقة على التحقق
- rejectValidation()     // رفض التحقق
- downloadAgreement()    // تنزيل الاتفاقية
```

### 2. Entities المطلوبة:

```php
- User (موجود)
- Offer
- Application
- Internship
- Agreement
- Validation
```

### 3. مكتبات البيانات المطلوبة:

```bash
composer require symfony/orm-pack
composer require symfony/maker-bundle --dev
composer require symfony/form
composer require symfony/validator
```

### 4. مكتبات الرسوم البيانية:

```bash
composer require friendsofsymfony/jsrouting-bundle
npm install chart.js
```

### 5. قالب Bootstrap الأساسي:

```html
<!-- admin/dashboard.html.twig -->
{% extends 'base.html.twig' %}

{% block content %}
<div class="container-fluid mt-4">
    <!-- KPI Cards -->
    <div class="row mb-4">
        {% for stat in stats %}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="card-title">{{ stat.value }}</h3>
                            <p class="text-muted">{{ stat.label }}</p>
                        </div>
                        <span class="badge bg-primary">{{ stat.change }}</span>
                    </div>
                </div>
            </div>
        </div>
        {% endfor %}
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Placement Trends -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>Placement Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="placementChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Pie Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Student Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Pending Validations -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Pending Validations</h5>
                    <a href="{{ path('admin_validations') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <table class="table">
                        {% for validation in recentValidations %}
                        <tr>
                            <td>
                                {% if validation.priority == 'high' %}
                                <span class="badge bg-danger"></span>
                                {% endif %}
                                {{ validation.student }}
                            </td>
                            <td>{{ validation.position }}</td>
                            <td>
                                <a href="{{ path('admin_validation_detail', {'id': validation.id}) }}" class="btn btn-sm btn-outline-primary">
                                    Review
                                </a>
                            </td>
                        </tr>
                        {% endfor %}
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Agreements -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Agreements</h5>
                    <a href="{{ path('admin_agreements') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <table class="table">
                        {% for agreement in recentAgreements %}
                        <tr>
                            <td>
                                <strong>{{ agreement.student }}</strong>
                                <br>
                                <small>{{ agreement.company }}</small>
                            </td>
                            <td>
                                <span class="badge 
                                    {% if agreement.status == 'signed' %}bg-success
                                    {% elseif agreement.status == 'approved' %}bg-primary
                                    {% else %}bg-warning{% endif %}">
                                    {{ agreement.status }}
                                </span>
                            </td>
                        </tr>
                        {% endfor %}
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize Charts
const placementCtx = document.getElementById('placementChart').getContext('2d');
new Chart(placementCtx, {
    type: 'bar',
    data: {
        labels: {{ months|json_encode }},
        datasets: [
            {
                label: 'Placed',
                data: {{ placedData|json_encode }},
                backgroundColor: '#00B384'
            },
            {
                label: 'Pending',
                data: {{ pendingData|json_encode }},
                backgroundColor: '#3B82F6'
            }
        ]
    }
});
</script>
{% endblock %}
```

---

## ملاحظات هامة:

1. **الألوان الموصى بها:**
   - Primary: `#3B82F6` (أزرق)
   - Success: `#00B384` (أخضر)
   - Warning: `#FCD34D` (أصفر)
   - Danger: `#EF4444` (أحمر)

2. **البيانات الديناميكية:**
   - يجب جلب كل البيانات من قاعدة البيانات
   - استخدام Query Builder أو Doctrine

3. **الأمان:**
   - تطبيق صلاحيات ROLE_ADMIN
   - التحقق من البيانات قبل الحفظ

4. **الأداء:**
   - استخدام eager loading للعلاقات
   - تجنب N+1 queries

---

هذا شرح شامل! هل تريد توضيح أي جزء معين؟
