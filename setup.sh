#!/bin/bash

# Stag.io - Complete Setup Script

set -e

BLUE='\033[0;34m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Stag.io - منصة إدارة التدريبات الجامعية           ║${NC}"
echo -e "${BLUE}║  Installation Setup                                ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════╝${NC}"
echo ""

# Step 1: Check requirements
echo -e "${YELLOW}[1/8]${NC} التحقق من المتطلبات..."
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP غير مثبت${NC}"
    exit 1
fi
echo -e "${GREEN}✓ PHP $(php -v | head -1 | cut -d' ' -f2)${NC}"

if ! command -v composer &> /dev/null; then
    echo -e "${RED}✗ Composer غير مثبت${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Composer$(NC)"

if ! command -v mysql &> /dev/null; then
    echo -e "${YELLOW}⚠ MySQL قد لا يكون في PATH - تأكد من تثبيته${NC}"
fi
echo ""

# Step 2: Install dependencies
echo -e "${YELLOW}[2/8]${NC} تثبيت المكتبات..."
composer install --no-interaction --prefer-dist
echo -e "${GREEN}✓ تم تثبيت المكتبات${NC}"
echo ""

# Step 3: Copy .env
echo -e "${YELLOW}[3/8]${NC} إعداد ملف البيئة..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo -e "${GREEN}✓ تم نسخ .env.example إلى .env${NC}"
else
    echo -e "${GREEN}✓ .env موجود بالفعل${NC}"
fi
echo ""

# Step 4: Generate APP_SECRET
echo -e "${YELLOW}[4/8]${NC} توليد مفتاح سري..."
php bin/console security:generate-secret
echo -e "${GREEN}✓ تم توليد المفتاح السري${NC}"
echo ""

# Step 5: Ask for database config
echo -e "${YELLOW}[5/8]${NC} إعدادات قاعدة البيانات..."
echo ""
echo "تم العثور على قيمة DATABASE_URL الحالية في .env"
echo "إذا أردت تغييرها، قم بتعديل ملف .env يدويأ"
echo ""
echo -e "${YELLOW}تنسيق DATABASE_URL:${NC}"
echo 'mysql://user:password@localhost:3306/database_name?serverVersion=8.0'
echo ""
read -p "هل تريد المتابعة؟ (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${GREEN}✓ سيتم استخدام إعدادات .env الحالية${NC}"
else
    echo -e "${YELLOW}⚠ يرجى تحديث .env يدويأ${NC}"
fi
echo ""

# Step 6: Create database
echo -e "${YELLOW}[6/8]${NC} إنشاء قاعدة البيانات..."
php bin/console doctrine:database:create --if-not-exists
echo -e "${GREEN}✓ تم إنشاء قاعدة البيانات${NC}"
echo ""

# Step 7: Run migrations
echo -e "${YELLOW}[7/8]${NC} تشغيل الهجرات..."
php bin/console doctrine:migrations:migrate --no-interaction
echo -e "${GREEN}✓ تم تشغيل الهجرات${NC}"
echo ""

# Step 8: Clear cache
echo -e "${YELLOW}[8/8]${NC} تنظيف الـ Cache..."
php bin/console cache:clear
echo -e "${GREEN}✓ تم تنظيف الـ Cache${NC}"
echo ""

echo -e "${GREEN}╔════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║            ✓ تم التثبيت بنجاح!                    ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BLUE}الخطوات التالية:${NC}"
echo -e "1. تشغيل الخادم: ${YELLOW}php bin/console server:run${NC}"
echo -e "2. افتح المتصفح: ${YELLOW}http://localhost:8000${NC}"
echo -e "3. اقرأ الوثائق: ${YELLOW}README.md${NC}"
echo -e "4. دليل المطورين: ${YELLOW}DEVELOPER_GUIDE_AR.md${NC}"
echo ""

echo -e "${BLUE}الحسابات الافتراضية:${NC}"
echo "✓ سجل حساب جديد أو استخدم بيانات الاختبار"
echo ""

echo -e "${GREEN}شكراً لاستخدام Stag.io! 🎉${NC}"
