<?php

namespace App\Service;

use App\Entity\Application;
use App\Entity\User;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ConventionGeneratorService
{
    public function __construct(
        private string $conventionsDir,
        private string $stampsDir,
        private string $appBaseUrl,
    ) {}

    public function generate(Application $application, User $admin): string
    {
        $student = $application->getStudent();
        $offer   = $application->getOffer();
        $company = $offer->getCompany();

        // ── 1. جلب وتأمين بيانات الجامعة والقسم والأدمن ──
        $universityName = 'University';
        $universityAddress = 'Not Provided';
        $departmentName = 'Not Provided';

        if ($admin->getUniversityRef()) {
            $university = $admin->getUniversityRef();
            $universityName = $university->getName();
            $universityAddress = $university->getAddress() ?? 'Not Provided';
        }

        if ($admin->getDepartmentRef()) {
            $departmentName = $admin->getDepartmentRef()->getName();
        }

        // بناء اسم الأدمن الكامل وإيميله بدقة
        $adminFullName = trim(($admin->getFirstName() ?? '') . ' ' . ($admin->getLastName() ?? ''));
        if (empty($adminFullName)) {
            $adminFullName = 'Authorized Administrator';
        }
        $adminEmail = $admin->getEmail() ?? 'No Email';

        if (!is_dir($this->conventionsDir)) {
            mkdir($this->conventionsDir, 0755, true);
        }

        $filename = sprintf(
            'internship_agreement_%s_%s.docx',
            $student->getId(),
            date('Ymd_His')
        );
        $outputPath = $this->conventionsDir . $filename;

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop'    => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(2),
            'marginLeft'   => Converter::cmToTwip(2),
            'marginRight'  => Converter::cmToTwip(2),
        ]);

        // ── 2. الـ Header العلوي (تم إزالة العنوان الفرعي والـ Address من هنا تماماً بناءً على طلبك) ──
        $section->addText(
            strtoupper($universityName),
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(1);

        // العنوان الرئيسي للوثيقة
        $section->addText(
            'INTERNSHIP AGREEMENT',
            ['bold' => true, 'size' => 18, 'color' => '1a3c6e'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 240, 'spaceAfter' => 240]
        );

        $section->addTextBreak(1);
        $section->addText('BETWEEN', ['bold' => true], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);
        // ── 3. جدول الأطراف المتقابل ──
        $section->addTextBreak(1);
        $partiesTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 100]);
        $partiesTable->addRow();

        // جهة اليسار: الشركة المستضيفة
        $leftCell = $partiesTable->addCell(5000);
        $leftCell->addText("The Host Company", ['bold' => true, 'underlined' => 'single', 'color' => '1a3c6e']);
        $leftCell->addText('Company Name: ' . ($company->getCompanyName() ?? $company->getFirstName() ?? 'TechCorp'));
        $leftCell->addText('Represented by: ' . trim(($company->getFirstName() ?? '') . ' ' . ($company->getLastName() ?? '')));
        $leftCell->addText('Email: ' . ($company->getEmail() ?? 'company@gmail.com'));
        $leftCell->addText('Phone: ' . ($company->getPhone() ?? '—'));

        // جهة اليمين: الجامعة (تعرض بيانات الأدمن المستخرجة مباشرة بشكل متقابل ومنظم)
        $rightCell = $partiesTable->addCell(5000);
        $rightCell->addText("The University:", ['bold' => true, 'underlined' => 'single', 'color' => '1a3c6e']);
        $rightCell->addText('University Name: ' . $universityName);
        $rightCell->addText('Represented by: ' . $adminFullName);
        $rightCell->addText('Email: ' . $adminEmail);
        $rightCell->addText('Department: ' . $departmentName);
        $rightCell->addText('Address: ' . $universityAddress);


        // معلومات الطالب
        $this->addPartySection($section, "The Student", [
            'Full Name: ' . trim(($student->getFirstName() ?? '') . ' ' . ($student->getLastName() ?? '')),
            'Specialty: ' . ($student->getSpecialty() ?? 'N/A'),
            'Level: '     . ($student->getLevel() ?? 'N/A'),
            'Email: '     . ($student->getEmail() ?? 'N/A'),
            'Phone: '     . ($student->getPhone() ?? '—'), // تم إضافة رقم الهاتف هنا
        ]);

        // ── 4. تفاصيل التربص ──
        $section->addTextBreak(1);
        $section->addText('INTERNSHIP DETAILS', ['bold' => true, 'color' => '1a3c6e']);

        $details = [
            'Project Title' => $offer->getTitle(),
            'Duration'      => ($offer->getDuration() ?? '—'),
            'Start Date'    => $offer->getInternshipStart()?->format('d/m/Y') ?? 'N/A',
            'Location'      => $offer->getWilaya() ?? 'N/A',
        ];
        foreach ($details as $label => $value) {
            $section->addText($label . ': ' . $value);
        }

        // ── 5. مربعات التوقيع والأختام ──
        $section->addTextBreak(2);
        $this->addSignatureBlock($section, $admin, $company);

        // ── 6. رمز الـ QR Code ──
        $downloadUrl = $this->appBaseUrl . '/student/applications/' . $application->getId() . '/convention';
        $qrPath = $this->generateQrCode($downloadUrl);

        if ($qrPath) {
            $section->addTextBreak(2);
            $section->addText(
                'Scan to download this agreement:',
                ['size' => 9, 'italic' => true],
                ['alignment' => Jc::CENTER]
            );
            $section->addImage($qrPath, [
                'width'     => 80,
                'height'    => 80,
                'alignment' => Jc::CENTER,
            ]);
            @unlink($qrPath);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($outputPath);

        return $filename;
    }

    private function generateQrCode(string $url): ?string
    {
        try {
            $qrCode = new QrCode($url);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $tmpPath = sys_get_temp_dir() . '/qr_' . uniqid() . '.png';
            file_put_contents($tmpPath, $result->getString());

            return $tmpPath;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function addPartySection($section, string $title, array $info): void
    {
        $section->addText($title, ['bold' => true, 'underlined' => 'single']);
        foreach ($info as $line) {
            $section->addText($line);
        }
        $section->addTextBreak(1);
    }

    private function addSignatureBlock($section, User $admin, User $company): void
    {
        $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 100]);
        $table->addRow(1500);

        $uniCell = $table->addCell(5000);
        $uniCell->addText("For the University", ['bold' => true], ['alignment' => Jc::CENTER]);

        $adminName = trim(($admin->getFirstName() ?? '') . ' ' . ($admin->getLastName() ?? ''));
        if (empty($adminName)) {
            $adminName = "Authorized Admin";
        }
        $uniCell->addText("Admin: " . $adminName, ['italic' => true, 'size' => 10], ['alignment' => Jc::CENTER]);
        $uniCell->addText("(Signature & Stamp)", ['size' => 9], ['alignment' => Jc::CENTER]);

        $stampFilename = $admin->getStampFilename();
        if ($stampFilename && file_exists($this->stampsDir . $stampFilename)) {
            $uniCell->addImage($this->stampsDir . $stampFilename, [
                'width' => 80, 'height' => 80, 'alignment' => Jc::CENTER,
            ]);
        }

        $compCell = $table->addCell(5000);
        $compCell->addText("For the Company", ['bold' => true], ['alignment' => Jc::CENTER]);
        $compCell->addText("(Signature & Cachet)", ['size' => 9], ['alignment' => Jc::CENTER]);

        $companyStamp = $company->getStampFilename();
        if ($companyStamp) {
            $companyStampPath = realpath($this->stampsDir . $companyStamp);
            if ($companyStampPath && file_exists($companyStampPath) && @getimagesize($companyStampPath) !== false) {
                $compCell->addImage($companyStampPath, [
                    'width' => 80, 'height' => 80, 'alignment' => Jc::CENTER,
                ]);
            }
        }
    }
}