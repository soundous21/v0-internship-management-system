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
        private string $appBaseUrl,  // ★ رابط الموقع مثل https://monsite.com
    ) {}

    public function generate(Application $application, User $university): string
    {
        $student = $application->getStudent();
        $offer   = $application->getOffer();
        $company = $offer->getCompany();

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

        // 1. Header
        $section->addText(
            strtoupper($university->getUniversityName() ?? 'University Name'),
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(1);

        // 2. Title
        $section->addText(
            'INTERNSHIP AGREEMENT',
            ['bold' => true, 'size' => 18, 'color' => '1a3c6e'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 240, 'spaceAfter' => 240]
        );

        // 3. Parties
        $this->addPartySection($section, "The Host Company", [
            'Company Name: '  . ($company->getCompanyName() ?? $company->getFirstName()),
            'Represented by: '. $company->getFirstName() . ' ' . $company->getLastName(),
            'Email: '         . $company->getEmail(),
            'Phone: '         . ($company->getPhone() ?? 'N/A'),
        ]);

        $section->addText('BETWEEN', ['bold' => true], ['alignment' => Jc::CENTER]);

        $this->addPartySection($section, "The Student", [
            'Full Name: ' . $student->getFirstName() . ' ' . $student->getLastName(),
            'Specialty: ' . ($student->getSpecialty() ?? 'N/A'),
            'Level: '     . ($student->getLevel() ?? 'N/A'),
            'Email: '     . $student->getEmail(),
        ]);

        // 4. Internship Details
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

        // 5. Signature Block
        $section->addTextBreak(2);
        $this->addSignatureBlock($section, $university, $company);

        // ★ 6. QR Code — رابط تحميل الوثيقة
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
            // حذف الملف المؤقت
            @unlink($qrPath);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($outputPath);

        return $filename;
    }

    // ★ توليد QR Code مؤقت
    // ★ توليد QR Code مؤقت
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

    private function addSignatureBlock($section, User $university, User $company): void
    {
        $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 100]);
        $table->addRow(1500);

        $uniCell = $table->addCell(5000);
        $uniCell->addText("For the University", ['bold' => true], ['alignment' => Jc::CENTER]);
        $uniCell->addText("(Signature & Stamp)", ['size' => 9], ['alignment' => Jc::CENTER]);

        $stampFilename = $university->getStampFilename();
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