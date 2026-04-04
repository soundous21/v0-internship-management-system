# دليل المطورين - Stag.io Symfony

## المحتويات
1. البداية السريعة
2. بنية المشروع
3. Controllers والتوجيه
4. Templates والواجهات
5. قاعدة البيانات
6. الأمان والمصادقة
7. الأخطاء الشائعة والحلول

---

## 1. البداية السريعة

### التثبيت
```bash
# استنساخ المشروع
git clone <url>
cd stag-symfony

# تثبيت المكتبات
composer install

# نسخ ملف البيئة
cp .env.example .env

# تحديث .env ببيانات المصادقة
nano .env

# إنشاء قاعدة البيانات
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# تشغيل الخادم
php bin/console server:run
```

ثم افتح: `http://localhost:8000`

---

## 2. بنية المشروع

```
src/
├── Controller/
│   ├── HomeController.php      # الصفحة الرئيسية
│   ├── AuthController.php      # المصادقة والتسجيل
│   ├── StudentController.php   # لوحة الطالب
│   ├── CompanyController.php   # لوحة الشركة
│   └── AdminController.php     # لوحة الإدارة

├── Entity/
│   └── User.php                # كائن المستخدم

└── Repository/
    └── UserRepository.php      # عمليات قاعدة البيانات

templates/
├── base.html.twig              # القالب الرئيسي
├── home/
│   └── index.html.twig         # الصفحة الرئيسية
├── auth/
│   ├── login.html.twig
│   └── register.html.twig
├── student/
│   ├── dashboard.html.twig
│   ├── offers.html.twig
│   ├── applications.html.twig
│   └── profile.html.twig
├── company/
│   ├── dashboard.html.twig
│   └── offers.html.twig
└── admin/
    ├── dashboard.html.twig
    ├── validations.html.twig
    ├── agreements.html.twig
    └── statistics.html.twig

config/
├── packages/
│   ├── doctrine.yaml           # إعدادات قاعدة البيانات
│   └── security.yaml           # إعدادات الأمان
├── routes/
│   ├── home.yaml
│   ├── auth.yaml
│   ├── student.yaml
│   ├── company.yaml
│   └── admin.yaml
└── services.yaml               # إعدادات الخدمات
```

---

## 3. Controllers والتوجيه

### إنشاء Controller جديد
```bash
php bin/console make:controller FeatureName
```

### مثال: HomeController
```php
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'مرحبا بك في Stag.io',
        ]);
    }
}
```

### التوجيه في YAML
```yaml
app_home:
  path: /
  controller: App\Controller\HomeController::index
  methods: GET
```

---

## 4. Templates والواجهات

### الوراثة في Twig
```twig
{% extends "base.html.twig" %}

{% block title %}عنوان الصفحة{% endblock %}

{% block content %}
    <h1>محتوى الصفحة</h1>
{% endblock %}
```

### المتغيرات والحلقات
```twig
{# عرض متغير #}
{{ variable }}

{# حلقة #}
{% for item in items %}
    {{ item.name }}
{% endfor %}

{# شرط #}
{% if user %}
    مرحبا {{ user.firstName }}
{% else %}
    يرجى تسجيل الدخول
{% endif %}
```

### الروابط
```twig
{# إنشاء رابط #}
<a href="{{ path('student_dashboard') }}">لوحة التحكم</a>

{# مع معاملات #}
<a href="{{ path('student_offer_detail', {id: offer.id}) }}">
    عرض التفاصيل
</a>
```

---

## 5. قاعدة البيانات

### إنشاء Entity
```bash
php bin/console make:entity
```

سيسأل عن:
- اسم الـ Entity
- الخصائص والأنواع
- العلاقات مع الـ Entities الأخرى

### مثال: Student Entity
```php
<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'students')]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $university;

    #[ORM\Column(type: 'string')]
    private string $specialization;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    // Getters and setters...
}
```

### Migration - الهجرات
```bash
# إنشاء migration
php bin/console make:migration

# تنفيذ التغييرات
php bin/console doctrine:migrations:migrate

# إلغاء آخر migration
php bin/console doctrine:migrations:migrate prev
```

### الاستعلام عن البيانات
```php
// في Controller
$userRepository = $this->getDoctrine()->getRepository(User::class);

// البحث عن مستخدم واحد
$user = $userRepository->find($id);
$user = $userRepository->findOneBy(['email' => $email]);

// البحث عن عدة مستخدمين
$users = $userRepository->findAll();
$students = $userRepository->findBy(['role' => 'ROLE_STUDENT']);

// حفظ
$em = $this->getDoctrine()->getManager();
$em->persist($user);
$em->flush();
```

---

## 6. الأمان والمصادقة

### إعدادات الأمان في `config/packages/security.yaml`
```yaml
security:
  password_hashers:
    Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface:
      algorithm: auto

  providers:
    app_user_provider:
      entity:
        class: App\Entity\User
        property: email

  firewalls:
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false

    main:
      lazy: true
      provider: app_user_provider
      form_login:
        login_path: app_login
        check_path: app_login
      logout:
        path: app_logout
```

### الحماية في Controllers
```php
// حماية الـ Route بدور معين
#[IsGranted('ROLE_STUDENT')]
class StudentController extends AbstractController
{
    public function dashboard(): Response
    {
        // فقط ROLE_STUDENT يمكنه الوصول
    }
}

// التحقق في Controller
public function someAction(): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    // ...
}

// الحصول على المستخدم الحالي
$user = $this->getUser();
```

### تشفير كلمات المرور
```php
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

public function register(
    UserPasswordHasherInterface $passwordHasher
): Response
{
    $user = new User();
    $user->setPassword(
        $passwordHasher->hashPassword($user, $plainPassword)
    );
    // ...
}
```

---

## 7. الأخطاء الشائعة والحلول

### الخطأ: "Service not found"
**الحل**: تأكد من تسجيل الخدمة في `config/services.yaml`

### الخطأ: "Column not found" في قاعدة البيانات
**الحل**: 
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### الخطأ: "Access Denied" في صفحة محمية
**الحل**: تحقق من الدور في Controller أو `security.yaml`

### الخطأ: "Template not found"
**الحل**: تأكد من:
1. المسار صحيح: `templates/folder/file.html.twig`
2. الاسم صحيح: `return $this->render('folder/file.html.twig');`

### الخطأ: Form CSRF Token Invalid
**الحل**: تأكد من إضافة الـ token في Form:
```twig
<form method="post">
    {{ form_widget(form._token) }}
</form>
```

---

## نصائح مفيدة

### 1. استخدام Fixtures للاختبار
```bash
php bin/console doctrine:fixtures:load
```

### 2. عرض جميع الـ Routes
```bash
php bin/console debug:router
```

### 3. تنظيف الـ Cache
```bash
php bin/console cache:clear
```

### 4. عرض معلومات الحاوية
```bash
php bin/console debug:container
```

### 5. تشغيل الأوامر المخصصة
```php
// في Command الخاص بك
php bin/console app:custom-command
```

---

## الموارد المفيدة

- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [Twig Template Engine](https://twig.symfony.com/)

---

**آخر تحديث**: 2024
**الإصدار**: 1.0.0
