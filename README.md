# Stag.io - منصة إدارة التدريبات الجامعية

تحويل كامل لمنصة Stag.io من Next.js إلى Symfony 6 مع MySQL

![Stag.io](https://img.shields.io/badge/Symfony-6.4-blue)
![PHP](https://img.shields.io/badge/PHP-8.2-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)

## نظرة عامة

Stag.io هي منصة شاملة لإدارة التدريبات الجامعية، تربط الطلاب مع الشركات وتوفر للجامعات أدوات إدارة فعالة.

### الأدوار الرئيسية
- **الطلاب**: البحث عن فرص والتقديم والمتابعة
- **الشركات**: نشر الفرص والتعاون مع الطلاب
- **المسؤولون**: إدارة النظام والتحقق والإحصائيات

## المتطلبات
- PHP 8.2+
- MySQL 8.0+
- Composer
- Symfony CLI (اختياري)

## التثبيت السريع

### 1. استنساخ المشروع
```bash
git clone <repository-url>
cd stag-symfony
```

### 2. تثبيت المكتبات
```bash
composer install
```

### 3. إعداد قاعدة البيانات

قم بإنشاء قاعدة بيانات جديدة:
```bash
mysql -u root -p
CREATE DATABASE stag_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'stag_user'@'localhost' IDENTIFIED BY 'stag_password';
GRANT ALL PRIVILEGES ON stag_db.* TO 'stag_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. تكوين متغيرات البيئة

قم بنسخ ملف `.env.example` إلى `.env`:
```bash
cp .env.example .env
```

ثم قم بتحديث `DATABASE_URL` في `.env`:
```
DATABASE_URL="mysql://stag_user:stag_password@127.0.0.1:3306/stag_db?serverVersion=8.0"
```

### 5. إنشاء قاعدة البيانات والجداول
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. تشغيل الخادم
```bash
php bin/console server:run
```

ثم افتح http://localhost:8000 في متصفحك.

## استخدام Docker (اختياري)

```bash
docker-compose up -d
```

## البنية الهندسية

```
stag-symfony/
├── src/
│   ├── Controller/          # Controllers لكل دور
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── StudentController.php
│   │   ├── CompanyController.php
│   │   └── AdminController.php
│   ├── Entity/             # Doctrine Entities
│   │   └── User.php
│   └── Repository/         # Database Repositories
├── templates/              # Twig Templates
│   ├── base.html.twig      # القالب الأساسي
│   ├── home/               # صفحات الهبوط
│   ├── auth/               # نماذج المصادقة
│   ├── student/            # لوحة الطالب
│   ├── company/            # لوحة الشركة
│   └── admin/              # لوحة الإدارة
├── config/                 # ملفات الإعدادات
│   ├── packages/
│   ├── routes/
│   └── services.yaml
├── public/                 # الملفات الثابتة
├── migrations/             # Database migrations
├── composer.json
└── .env
```

## المسارات الرئيسية

### الصفحات العامة
| المسار | الوصف |
|--------|-------|
| `/` | الصفحة الرئيسية |
| `/about` | حول المشروع |
| `/login` | تسجيل الدخول |
| `/register` | التسجيل الجديد |

### لوحة الطالب
| المسار | الوصف |
|--------|-------|
| `/dashboard/student/` | الرئيسية |
| `/dashboard/student/offers` | الفرص المتاحة |
| `/dashboard/student/applications` | تطبيقاتي |
| `/dashboard/student/profile` | ملفي الشخصي |

### لوحة الشركة
| المسار | الوصف |
|--------|-------|
| `/dashboard/company/` | الرئيسية |
| `/dashboard/company/offers` | فرصي |
| `/dashboard/company/offers/new` | إضافة فرصة |
| `/dashboard/company/candidates` | المرشحون |
| `/dashboard/company/profile` | ملف الشركة |

### لوحة الإدارة
| المسار | الوصف |
|--------|-------|
| `/dashboard/admin/` | الرئيسية |
| `/dashboard/admin/validations` | التحققات |
| `/dashboard/admin/agreements` | الاتفاقيات |
| `/dashboard/admin/statistics` | الإحصائيات |

## الميزات

### للطلاب
✅ البحث عن فرص التدريب
✅ تصفية حسب الموقع والمهارات
✅ تقديم طلبات
✅ تتبع التطبيقات
✅ إدارة الملف الشخصي
✅ إضافة المهارات التقنية

### للشركات
✅ نشر فرص جديدة
✅ إدارة الفرص (تعديل/حذف)
✅ عرض المرشحين
✅ قبول أو رفض الطلاب
✅ إدارة ملف الشركة

### للمسؤولين
✅ التحقق من الحسابات الجديدة
✅ إدارة الاتفاقيات
✅ عرض الإحصائيات المفصلة
✅ تقارير الأداء

## المراحل القادمة

### المرحلة 1: ✅ البنية الأساسية
- إنشاء Controllers
- إنشاء Templates Twig
- إعداد التوجيه والأمان

### المرحلة 2: 🔄 قاعدة البيانات
- إنشاء Entities الكاملة
- إعداد العلاقات بين الجداول
- إنشاء Database Migrations

### المرحلة 3: ميزات متقدمة
- نظام الملفات والتحميل
- البحث والفلترة المتقدمة
- الإشعارات والبريد الإلكتروني
- الصور والمرفقات

### المرحلة 4: الأمان والاختبار
- التحقق من البيانات (Validation)
- Unit Tests
- Integration Tests
- Security Testing

## أوامر مفيدة

### إنشاء Entity
```bash
php bin/console make:entity
```

### إنشاء Migration
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### إنشاء Controller
```bash
php bin/console make:controller
```

### تشغيل الاختبارات
```bash
php bin/phpunit
```

### مسح الـ Cache
```bash
php bin/console cache:clear
```

## أمثلة سريعة

### تسجيل طالب جديد
1. اذهب إلى `/register`
2. أدخل بريدك الإلكتروني وكلمة مرورك
3. اختر "طالب" كدور
4. انقر على "إنشاء حساب"

### نشر فرصة جديدة (للشركات)
1. سجل الدخول كشركة
2. اذهب إلى `/dashboard/company/offers`
3. انقر على "إضافة فرصة جديدة"
4. ملء البيانات والمهارات المطلوبة

## المساهمة

نرحب بمساهماتك! للبدء:
1. Fork المشروع
2. إنشاء فرع للميزة الجديدة (`git checkout -b feature/AmazingFeature`)
3. قم بعمل Commit (`git commit -m 'Add some AmazingFeature'`)
4. اضغط على الفرع (`git push origin feature/AmazingFeature`)
5. افتح Pull Request

## الترخيص

هذا المشروع مرخص تحت MIT License - انظر ملف [LICENSE](LICENSE) للتفاصيل.

## الدعم

إذا واجهت أي مشاكل، يرجى فتح [Issue](../../issues) على GitHub.

---

**تم التطوير بواسطة:** فريق Stag.io
**آخر تحديث:** 2024
