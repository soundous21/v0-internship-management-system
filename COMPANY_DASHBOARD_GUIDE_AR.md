خيارات التحويل الكامل: صفحة Company Dashboard من Next.js إلى Symfony

## شرح صفحة Company Dashboard في المشروع الأول (Next.js)

### 1. البيانات (DATA)

الصفحة تحتوي على 3 مصادر بيانات رئيسية:

#### أ) الإحصائيات (Stats) - 4 بطاقات:
```
- Active Offers: 4 فرص مفتوحة
- Total Applications: 47 تطبيق
- Interviews Scheduled: 8 مقابلات
- Positions Filled: 3 وظائف مملوءة
```
كل بطاقة لها:
- رقم (value)
- تسمية (label)
- أيقونة (icon)
- لون مخصص (color)

#### ب) المرشحون الأخيرون (recentCandidates) - 4 مرشحين:
```
- الاسم: Ahmed Benali, Fatima Zohra, ...
- الوظيفة: Frontend Developer, Backend Developer
- الجامعة: USTHB, ESI Algiers
- المهارات: React, Python, Node.js
- وقت التقديم: 2 hours ago, 5 hours ago
- الحالة: new, reviewed, interview
```

#### ج) الفرص النشطة (activeOffers) - 4 فرص:
```
- العنوان: Frontend Developer Intern
- عدد التطبيقات: 18
- عدد المشاهدات: 124
- الحالة: active, closing
- آخر موعد: April 30, 2026
```

---

### 2. المكونات الرئيسية

#### أ) رأس الصفحة (Header):
- عنوان: "Company Dashboard"
- وصف: "Manage your internship offers and candidates"
- زر: "Post New Offer" يرابط إلى `/dashboard/company/offers/new`

#### ب) شبكة الإحصائيات (Stats Grid):
- 4 بطاقات في صف واحد (على الشاشات الكبيرة)
- تتجاوب مع الشاشات الصغيرة (2x2 على الموبايل)
- كل بطاقة تعرض أيقونة وقيمة

#### ج) بطاقة المرشحين الأخيرين (Recent Candidates):
- عنوان: "Recent Candidates"
- وصف: "Latest applications to your offers"
- زر "View All" يرابط إلى `/dashboard/company/candidates`
- قائمة 4 مرشحين يعرض:
  - صورة الملف (Avatar)
  - الاسم والوظيفة والجامعة
  - بطاقة "New" إذا كان جديداً
  - زرين: قبول (checkmark) ورفض (x)

#### د) بطاقة الفرص النشطة (Active Offers):
- عنوان: "Active Offers"
- وصف: "Your current internship positions"
- زر "Manage" يرابط إلى `/dashboard/company/offers`
- قائمة 4 فرص يعرض:
  - العنوان مع بطاقة "Closing Soon" إذا لزم
  - عدد التطبيقات والمشاهدات
  - تاريخ آخر موعد
  - زر "View"

---

### 3. التفاعلات (Interactions)

1. **الأزرار:**
   - "Post New Offer" → ينقلك لإنشاء فرصة جديدة
   - "View All" (المرشحون) → قائمة كاملة بالمرشحين
   - "Manage" (الفرص) → إدارة الفرص
   - checkmark/x buttons → قبول/رفض مرشح
   - "View" → عرض تفاصيل الفرصة

2. **التصميم التفاعلي:**
   - الصفوف تتغير اللون عند المرور عليها (hover)
   - الأزرار الصغيرة لتقبيل/رفض المرشحين

---

### 4. الميزات التصميمية

- **Responsive**: تتجاوب مع الشاشات (mobile, tablet, desktop)
- **Grid Layout**: استخدام Tailwind CSS grid
- **Cards**: بطاقات احترافية مع ظلال
- **Colors**: استخدام الألوان (accent, destructive, yellow)
- **Icons**: استخدام أيقونات Lucide React
- **Badges**: بطاقات صغيرة للحالات (New, Closing Soon)

---

### 5. البيانات المطلوبة من قاعدة البيانات (في Symfony)

إذا أردنا جعل هذه البيانات ديناميكية:

```sql
-- الإحصائيات تأتي من:
SELECT COUNT(*) FROM offers WHERE company_id = ? AND status = 'active'
SELECT COUNT(*) FROM applications WHERE company_id = ?
SELECT COUNT(*) FROM interviews WHERE company_id = ? AND scheduled_date >= NOW()
SELECT COUNT(*) FROM applications WHERE company_id = ? AND status = 'hired'

-- المرشحون الأخيرون:
SELECT u.*, app.* FROM users u 
JOIN applications app ON u.id = app.student_id 
WHERE app.company_id = ? 
ORDER BY app.created_at DESC LIMIT 4

-- الفرص النشطة:
SELECT * FROM offers 
WHERE company_id = ? 
ORDER BY created_at DESC LIMIT 4
```

---

## الخلاصة

صفحة Company Dashboard هي **لوحة تحكم رئيسية** للشركة توضح:
- ✅ الإحصائيات الرئيسية بسرعة
- ✅ آخر المرشحين والتطبيقات
- ✅ الفرص المفتوحة والنشطة
- ✅ روابط سريعة للعمليات الأخرى

كل شيء بتصميم احترافي وسهل الاستخدام!
