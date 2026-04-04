#!/usr/bin/env bash

# Stag.io - Installation Script

set -e

echo "🚀 بدء تثبيت Stag.io..."
echo ""

# Check PHP version
echo "✓ التحقق من إصدار PHP..."
php -v

# Check Composer
echo "✓ التحقق من Composer..."
composer --version

# Install dependencies
echo "✓ تثبيت المكتبات..."
composer install

# Copy .env
if [ ! -f .env ]; then
    echo "✓ إنشاء ملف .env..."
    cp .env.example .env
fi

# Generate app secret
echo "✓ توليد مفتاح سري..."
php bin/console security:generate-secret

# Create database
echo "✓ إنشاء قاعدة البيانات..."
php bin/console doctrine:database:create --if-not-exists

# Run migrations
echo "✓ تشغيل الهجرات..."
php bin/console doctrine:migrations:migrate --no-interaction

echo ""
echo "✅ تم التثبيت بنجاح!"
echo ""
echo "الخطوات التالية:"
echo "1. قم بتحديث .env ببيانات قاعدة البيانات"
echo "2. شغل الخادم: php bin/console server:run"
echo "3. افتح http://localhost:8000 في متصفحك"
