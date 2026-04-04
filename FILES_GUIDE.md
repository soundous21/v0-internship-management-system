# الملفات والمجلدات الرئيسية في المشروع

## 📋 الملفات الأساسية

### ملفات الإعدادات
```
├── .env                    # متغيرات البيئة (لا تضفها للـ Git)
├── .env.example           # مثال لـ .env
├── composer.json          # المكتبات والمتطلبات
├── .gitignore            # الملفات المستبعدة من Git
└── docker-compose.yml    # إعدادات Docker
```

### ملفات التشغيل
```
├── public/index.php       # نقطة الدخول الرئيسية
├── bin/console            # سطر الأوامر الخاص بـ Symfony
├── Makefile               # أوامر سريعة
├── setup.sh               # سكريبت التثبيت
└── install.sh             # سكريبت التثبيت البديل
```

### ملفات التوثيق
```
├── README.md              # ملف التوثيق الرئيسي
├── DEVELOPER_GUIDE_AR.md  # دليل المطورين بالعربية
├── ROADMAP.md             # خارطة الطريق
└── INSTALLATION.md        # تعليمات التثبيط (قادم)
```

---

## 📁 المجلدات الرئيسية

### src/ - أكواد التطبيق الرئيسية
```
src/
├── Controller/                     # معالجات الطلبات
│   ├── HomeController.php          # الصفحة الرئيسية
│   ├── AuthController.php          # المصادقة (تسجيل/دخول)
│   ├── StudentController.php       # لوحة الطالب
│   ├── CompanyController.php       # لوحة الشركة
│   └── AdminController.php         # لوحة الإدارة
│
├── Entity/                         # كائنات قاعدة البيانات
│   ├── User.php                    # المستخدم (✓ مكتمل)
│   ├── Student.php                 # الطالب (قادم)
│   ├── Company.php                 # الشركة (قادم)
│   ├── InternshipOffer.php         # فرصة التدريب (قادم)
│   ├── Application.php             # الطلب/التطبيق (قادم)
│   └── Agreement.php               # الاتفاقية (قادم)
│
├── Repository/                     # عمليات قاعدة البيانات
│   └── UserRepository.php          # عمليات المستخدم (قادم)
│
├── Form/                           # نماذج HTML (قادم)
│   ├── StudentFormType.php
│   ├── OfferFormType.php
│   └── ApplicationFormType.php
│
├── Service/                        # الخدمات المساعدة (قادم)
│   ├── EmailService.php
│   ├── FileUploadService.php
│   └── NotificationService.php
│
└── Kernel.php                      # نواة التطبيق
```

### templates/ - قوالب الواجهات
```
templates/
├── base.html.twig                  # القالب الأساسي
├── home/                           # صفحات الهبوط
│   ├── index.html.twig             # الرئيسية
│   ├── hero.html.twig              # قسم البطل
│   ├── features.html.twig          # الميزات
│   ├── roles.html.twig             # الأدوار
│   └── footer.html.twig            # التذييل
├── auth/                           # صفحات المصادقة
│   ├── login.html.twig             # تسجيل الدخول
│   ├── register.html.twig          # التسجيل الجديد
│   └── reset-password.html.twig    # إعادة تعيين كلمة المرور (قادم)
├── student/                        # لوحة الطالب
│   ├── dashboard.html.twig
│   ├── offers.html.twig
│   ├── offer-detail.html.twig
│   ├── apply.html.twig
│   ├── profile.html.twig
│   └── applications.html.twig
├── company/                        # لوحة الشركة
│   ├── dashboard.html.twig
│   ├── offers.html.twig
│   ├── create-offer.html.twig
│   ├── edit-offer.html.twig
│   ├── candidates.html.twig
│   └── profile.html.twig
├── admin/                          # لوحة الإدارة
│   ├── dashboard.html.twig
│   ├── validations.html.twig
│   ├── agreements.html.twig
│   ├── statistics.html.twig
│   └── users.html.twig             # (قادم)
└── components/                     # مكونات قابلة لإعادة الاستخدام (قادم)
    ├── navbar.html.twig
    ├── sidebar.html.twig
    ├── pagination.html.twig
    └── alerts.html.twig
```

### config/ - ملفات الإعدادات
```
config/
├── packages/                       # إعدادات الحزم
│   ├── doctrine.yaml               # إعدادات قاعدة البيانات
│   ├── security.yaml               # إعدادات الأمان والمصادقة
│   ├── framework.yaml              # إعدادات Symfony
│   └── twig.yaml                   # إعدادات Twig
├── routes/                         # ملفات التوجيه
│   ├── home.yaml                   # مسارات الصفحة الرئيسية
│   ├── auth.yaml                   # مسارات المصادقة
│   ├── student.yaml                # مسارات لوحة الطالب
│   ├── company.yaml                # مسارات لوحة الشركة
│   └── admin.yaml                  # مسارات لوحة الإدارة
├── services.yaml                   # إعدادات الخدمات
├── routes.yaml                     # ملف التوجيه الرئيسي
└── bundles.php                     # تسجيل الحزم
```

### public/ - الملفات الثابتة
```
public/
├── index.php                       # نقطة الدخول الرئيسية
├── css/                            # ملفات الأنماط
│   └── style.css                   # الأنماط الأساسية
├── js/                             # ملفات JavaScript
│   └── main.js                     # السكريبتات الأساسية
├── images/                         # الصور
│   ├── logo.png
│   └── hero.jpg
└── uploads/                        # تحميلات المستخدمين
    ├── avatars/
    ├── cvs/
    └── logos/
```

### var/ - ملفات النظام
```
var/
├── cache/                          # ملفات التخزين المؤقت
├── log/                            # ملفات السجلات
└── sessions/                       # جلسات المستخدمين
```

### migrations/ - هجرات قاعدة البيانات
```
migrations/
├── Version20240101000000.php        # الهجرة الأولى
├── Version20240101120000.php        # الهجرة الثانية
└── ...
```

---

## 📊 معلومات الملفات

### عدد الملفات الحالية
| النوع | العدد | الحالة |
|--------|--------|--------|
| Controllers | 5 | ✅ مكتمل |
| Templates | 15+ | ✅ مكتمل |
| Entities | 1 | 🔄 جاري |
| Services | 0 | ⏳ قادم |
| Tests | 0 | ⏳ قادم |
| Config Files | 10+ | ✅ مكتمل |

### حجم الأكواد
```
- PHP Files: ~2000 سطر
- Twig Templates: ~1500 سطر
- Config Files: ~500 سطر
- Documentation: ~2000 سطر
─────────────────────────
Total: ~6000 سطر كود
```

---

## 🔄 الملفات المخطط إضافتها

### المرحلة 2 - قاعدة البيانات
```
src/Entity/
├── Student.php              # ✅ قادم قريباً
├── Company.php              # ✅ قادم قريباً
├── InternshipOffer.php      # ✅ قادم قريباً
├── Application.php          # ✅ قادم قريباً
└── Agreement.php            # ✅ قادم قريباً
```

### المرحلة 3 - الميزات المتقدمة
```
src/
├── Form/                    # نماذج HTML
├── Service/                 # خدمات مخصصة
├── Event/                   # أحداث مخصصة
└── Listener/                # مستمعو الأحداث
```

### المرحلة 4 - الاختبارات
```
tests/
├── Unit/                    # اختبارات الوحدات
├── Functional/              # اختبارات الميزات
└── API/                     # اختبارات API
```

---

## 📝 الملفات الموصى بقراءتها

### للمبتدئين
1. **README.md** - نظرة عامة على المشروع
2. **DEVELOPER_GUIDE_AR.md** - دليل شامل بالعربية
3. **setup.sh** - كيفية التثبيت

### للمطورين
1. **src/Controller/HomeController.php** - مثال Controller
2. **templates/base.html.twig** - مثال Template
3. **config/packages/security.yaml** - إعدادات الأمان
4. **ROADMAP.md** - خارطة الطريق

### للمسؤولين
1. **docker-compose.yml** - بيئة التطوير
2. **.env.example** - متغيرات البيئة
3. **Makefile** - أوامر سريعة

---

## 🎯 الخطوات التالية

1. **اقرأ README.md** للحصول على نظرة عامة
2. **اتبع التعليمات في setup.sh** للتثبيت
3. **ادرس DEVELOPER_GUIDE_AR.md** لفهم البنية
4. **ابدأ في إضافة الميزات** حسب ROADMAP.md

---

**آخر تحديث**: 2024
**الإصدار**: 1.0.0-beta
