# Quick Start - البدء السريع

## إذا كنت على عجلة من أمرك...

### ⚡ التثبيت في 5 دقائق

```bash
# 1. استنساخ المشروع
git clone <url>
cd stag-symfony

# 2. تثبيت المكتبات
composer install

# 3. نسخ الإعدادات
cp .env.example .env

# 4. إنشاء قاعدة البيانات
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. تشغيل الخادم
php bin/console server:run
```

ثم افتح: **http://localhost:8000**

---

## 📋 الملفات الأساسية فقط

| الملف | الغرض |
|------|-------|
| `composer.json` | المكتبات |
| `.env.example` | متغيرات البيئة |
| `setup.sh` | سكريبت التثبيت |
| `src/Controller/` | معالجات الطلبات |
| `templates/` | الواجهات |
| `config/` | الإعدادات |

---

## 🔑 الأوامر المهمة

```bash
# تشغيل الخادم
php bin/console server:run

# إنشاء Entity
php bin/console make:entity

# إنشاء Controller
php bin/console make:controller

# عرض الـ Routes
php bin/console debug:router

# تنظيف الـ Cache
php bin/console cache:clear
```

---

## 🏗️ البنية الأساسية

```
src/Controller/         → معالجات HTTP
templates/              → صفحات HTML (Twig)
config/                 → الإعدادات
public/                 → الملفات الثابتة
```

---

## 📚 توثيق إضافية

- **DEVELOPER_GUIDE_AR.md** - شرح شامل
- **ROADMAP.md** - الخطط المستقبلية
- **FILES_GUIDE.md** - دليل الملفات

---

**هذا كل شيء! ابدأ بـ `php bin/console server:run` 🚀**
