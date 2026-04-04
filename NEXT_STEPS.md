# ما الذي تفعله الآن؟

## 🎯 الخطوات التالية الفورية

### 1️⃣ اختبر المشروع (الآن)
```bash
# تشغيل الخادم
php bin/console server:run

# افتح المتصفح
http://localhost:8000
```

**ستشاهد:**
- الصفحة الرئيسية جميلة
- نموذج التسجيل
- نموذج تسجيل الدخول

---

### 2️⃣ استكشف الملفات (15 دقيقة)

اقرأ هذه الملفات بالترتيب:
1. `QUICKSTART.md` - (3 دقائق)
2. `README.md` - (5 دقائق)
3. `FILES_GUIDE.md` - (7 دقائق)

**الهدف:** فهم البنية الأساسية

---

### 3️⃣ ادرس البنية (30 دقيقة)

اقرأ DEVELOPER_GUIDE_AR.md وركز على:
- Controllers والتحكم
- Templates والواجهات
- Routes والمسارات
- قاعدة البيانات

**الهدف:** فهم كيفية عمل Symfony

---

### 4️⃣ ابدأ بإضافة Entities (ساعة واحدة)

اتبع هذه الخطوات:

```bash
# 1. إنشاء Entity Student
php bin/console make:entity

# 2. أضف الخصائص:
#    - id (تلقائي)
#    - user (ManyToOne)
#    - university
#    - specialization
#    - graduation_year
#    - skills (المهارات)

# 3. إنشاء Migration
php bin/console make:migration

# 4. تشغيل Migration
php bin/console doctrine:migrations:migrate
```

**الهدف:** فهم الـ Entities والعلاقات

---

## 📚 قراءة مقترحة

### لا تفوّت هذه!
| الملف | الوقت | الأولوية |
|------|-------|---------|
| QUICKSTART.md | 5 دقائق | 🔴 عالية جداً |
| README.md | 10 دقائق | 🔴 عالية جداً |
| DEVELOPER_GUIDE_AR.md | 30 دقيقة | 🟠 عالية |
| ROADMAP.md | 15 دقيقة | 🟡 متوسطة |
| PROJECT_INFO.md | 10 دقائق | 🟡 متوسطة |

### موارد خارجية مفيدة
- [Symfony Docs](https://symfony.com/doc/)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [Twig Guide](https://twig.symfony.com/)

---

## 🔧 الأوامر التي ستحتاجها

```bash
# إنشاء Entity
php bin/console make:entity

# إنشاء Controller
php bin/console make:controller

# إنشاء Form
php bin/console make:form

# المهاجرة
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# التصحيح
php bin/console debug:router
php bin/console debug:container
```

---

## 🛣️ خارطة الطريق - الأولويات

### الأسبوع الأول
1. [ ] اختبر المشروع الحالي
2. [ ] اقرأ التوثيق الأساسية
3. [ ] أنشئ Student Entity
4. [ ] أنشئ Company Entity
5. [ ] اختبر الـ Entities

### الأسبوع الثاني
1. [ ] أنشئ InternshipOffer Entity
2. [ ] أنشئ Application Entity
3. [ ] ضع العلاقات
4. [ ] إنشاء Repositories
5. [ ] اختبر الاستعلامات

### الأسبوع الثالث
1. [ ] أضف Forms
2. [ ] أكمل Controllers
3. [ ] أنشئ Templates المتقدمة
4. [ ] أضف Validation
5. [ ] اختبر الميزات

### الأسبوع الرابع
1. [ ] نظام الملفات
2. [ ] البحث والفلترة
3. [ ] الإشعارات
4. [ ] الاختبارات
5. [ ] التصحيح النهائي

---

## 💡 نصائح مهمة

### 1. استخدم Doctrine بذكاء
```php
// ✅ صحيح
$users = $this->getDoctrine()
    ->getRepository(User::class)
    ->findBy(['role' => 'ROLE_STUDENT']);

// ❌ خطأ
$users = $this->someCustomQuery();
```

### 2. استخدم Twig بشكل صحيح
```twig
{# ✅ صحيح #}
{% for user in users %}
    {{ user.email }}
{% endfor %}

{# ❌ خطأ #}
<% for user in users %>...
```

### 3. احم كل شيء
```php
#[IsGranted('ROLE_STUDENT')]
class StudentController extends AbstractController
{
    // فقط ROLE_STUDENT يمكنه الوصول
}
```

### 4. استخدم Services للمنطق المعقد
```php
class MyService
{
    public function doSomething()
    {
        // المنطق المعقد هنا
    }
}
```

---

## 🐛 استكشاف الأخطاء

### الخطأ: "Service not found"
```bash
# الحل: تأكد من تسجيل الخدمة
php bin/console debug:container service_name
```

### الخطأ: "Column not found"
```bash
# الحل: شغّل الهجرات
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### الخطأ: "Access Denied"
```bash
# الحل: تحقق من الدور
#[IsGranted('ROLE_ADMIN')]
```

---

## 📝 نموذج Entity

استخدم هذا النموذج للـ Entities الجديدة:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'example_table')]
class ExampleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    // Getters and setters...
}
```

---

## 🎓 أهداف التعلم

### هذا الأسبوع
- [ ] فهم بنية Symfony
- [ ] كيفية عمل Controllers
- [ ] كيفية عمل Templates
- [ ] المسارات والتوجيه

### الأسبوع القادم
- [ ] Entities والعلاقات
- [ ] Repository و Queries
- [ ] Forms والتحقق
- [ ] الأمان والمصادقة

### الشهر القادم
- [ ] API RESTful
- [ ] الاختبارات
- [ ] الأداء
- [ ] التطوير المتقدم

---

## 🚀 نصيحتك الذهبية

**لا تقرأ الأكواد فقط، اكتبها!**

1. ابدأ بـ Entity بسيطة
2. اكتبها بنفسك
3. شغّلها واختبرها
4. شاهد الخطأ
5. اصلح الخطأ
6. كرّر!

---

## 📞 في حالة الاستعجال

### أسرع طريقة للبدء
```bash
bash setup.sh
php bin/console server:run
```

### أول شيء تفعله
اذهب إلى: `http://localhost:8000`

### أول شيء تقرأه
اقرأ: `QUICKSTART.md`

### أول شيء تكتبه
اكتب: `Student Entity`

---

## ✅ قائمة فحص البدء

- [ ] تشغيل الخادم بنجاح
- [ ] فتح الصفحة الرئيسية
- [ ] قراءة QUICKSTART.md
- [ ] قراءة README.md
- [ ] استكشاف البنية
- [ ] إنشاء Entity جديد
- [ ] تشغيل Migration
- [ ] شاهد الجدول في قاعدة البيانات

---

## 🎉 أنت جاهز!

الآن لديك:
✅ مشروع Symfony كامل
✅ بنية احترافية
✅ توثيق شاملة
✅ أمثلة جاهزة
✅ خطة عمل واضحة

**ابدأ الآن وامرح مع التطوير!** 🚀

---

**في أي وقت تحتاج إلى المساعدة:**
1. اقرأ التوثيق ذات الصلة
2. ادرس الأمثلة الموجودة
3. استكشف الملفات المشابهة
4. اتبع الأنماط الموجودة

**النجاح يأتي من البدء، لا من الانتظار!** 💪
