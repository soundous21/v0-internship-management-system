<?php
// src/Service/ConventionGeneratorService.php
namespace App\Service;

use App\Entity\Application;
use App\Entity\User;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Table as TableStyle;

class ConventionGeneratorService
{
    public function __construct(
        private string $conventionsDir, // %kernel.project_dir%/public/conventions/
        private string $stampsDir,      // %kernel.project_dir%/public/uploads/stamps/
    ) {}

    /**
     * يولّد ملف DOCX للاتفاقية بناءً على كيانات المشروع الفعلية.
     *
     * @param Application $application  الطلب المقبول
     * @param User        $university   مستخدم الجامعة (ROLE_ADMIN أو ROLE_UNIVERSITY)
     * @return string                   اسم الملف المُولَّد فقط
     */
    public function generate(Application $application, User $university): string
    {
        /** @var User $student */
        $student = $application->getStudent();

        /** @var \App\Entity\Offers $offer */
        $offer   = $application->getOffer();

        /** @var User $company */
        $company = $offer->getCompany();

        // ── إنشاء مجلد الاتفاقيات ─────────────────────────────────────────
        if (!is_dir($this->conventionsDir)) {
            mkdir($this->conventionsDir, 0755, true);
        }

        $filename   = sprintf(
            'convention_%s_%s_%s.docx',
            $student->getId(),
            $application->getId(),
            date('Ymd_His')
        );
        $outputPath = $this->conventionsDir . $filename;

        // ── إعداد PhpWord ─────────────────────────────────────────────────
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(12);

        $section = $phpWord->addSection([
            'marginTop'    => Converter::cmToTwip(2.5),
            'marginBottom' => Converter::cmToTwip(2.5),
            'marginLeft'   => Converter::cmToTwip(2.5),
            'marginRight'  => Converter::cmToTwip(2.5),
        ]);

        // ════════════════════════════════════════════════════════════════════
        // 1. رأس الوثيقة: شعار/ختم الجامعة + العام الجامعي
        // ════════════════════════════════════════════════════════════════════
        $this->addDocumentHeader($section, $university);

        // ════════════════════════════════════════════════════════════════════
        // 2. العنوان الرئيسي
        // ════════════════════════════════════════════════════════════════════
        $section->addText(
            'Convention de Stage',
            ['bold' => true, 'size' => 20, 'color' => '1a3c6e'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 240, 'spaceAfter' => 240]
        );
        $section->addText(
            'ENTRE',
            ['bold' => true, 'size' => 13],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 120, 'spaceAfter' => 120]
        );

        // ════════════════════════════════════════════════════════════════════
        // 3. طرفا الاتفاقية
        // ════════════════════════════════════════════════════════════════════

        // ─── الشركة ──────────────────────────────────────────────────────
        $this->addPartyBlock($section, "L'Entreprise", [
            $company->getCompanyName() ?? ($company->getFirstName() . ' ' . $company->getLastName()),
            'Wilaya : ' . ($company->getWilaya() ?? '—'),
            'Secteur : ' . ($company->getIndustry() ?? '—'),
            'Représentée par : ' . $company->getFirstName() . ' ' . $company->getLastName(),
            'Tél : ' . ($company->getPhone() ?? '—'),
            'Mail : ' . $company->getEmail(),
            ($company->getWebsite() ? 'Web : ' . $company->getWebsite() : ''),
        ]);

        $section->addText(
            'ET',
            ['bold' => true, 'size' => 13],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 120, 'spaceAfter' => 120]
        );

        // ─── الجامعة ─────────────────────────────────────────────────────
        $this->addPartyBlock($section, "L'Université", [
            $university->getUniversityName() ?? $university->getCompanyName() ?? 'Université',
            'Représentée par : ' . $university->getFirstName() . ' ' . $university->getLastName(),
            'Tél : ' . ($university->getPhone() ?? '—'),
            'Mail : ' . $university->getEmail(),
        ]);

        // ════════════════════════════════════════════════════════════════════
        // 4. بيانات الطالب
        // ════════════════════════════════════════════════════════════════════
        $section->addTextBreak(1);
        $section->addText(
            "Données Relatives à l'Étudiant",
            ['bold' => true, 'size' => 13, 'color' => '1a3c6e'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 200, 'spaceAfter' => 160]
        );

        // بناء قائمة المهارات كنص
        $skillsList = implode(', ', $student->getSkills()->map(
            fn($s) => $s->getTagName()
        )->toArray()) ?: '—';

        $fields = [
            'Nom et Prénom'             => $student->getLastName() . ' ' . $student->getFirstName(),
            "Faculté / Université"      => $student->getUniversityName() ?? $student->getUniversity() ?? '—',
            'Spécialité'                => $student->getSpecialty() ?? '—',
            'Niveau'                    => $student->getLevel() ?? '—',
            "Carte d'étudiant N°"       => '—',           // ليس في كيانك حالياً
            'Tél'                       => $student->getPhone() ?? '—',
            'Email'                     => $student->getEmail(),
            'Compétences'               => $skillsList,
            'Thème du stage'            => $offer->getTitle(),
            'Description'               => $offer->getDescription() ?? '—',
            'Durée du stage'            => ($offer->getDuration() ?? '—') . ' semaines',
            'Type de stage'             => $offer->getLocationType() ?? '—',
            'Wilaya'                    => $offer->getWilaya() ?? '—',
            'Date de début'             => $offer->getStartDate()?->format('d/m/Y') ?? '—',
            'Date de fin (deadline)'    => $offer->getDeadline()?->format('d/m/Y') ?? '—',
        ];

        $this->addFieldsTable($section, $fields);

        // ════════════════════════════════════════════════════════════════════
        // 5. المواد القانونية
        // ════════════════════════════════════════════════════════════════════
        $section->addTextBreak(1);
        foreach ($this->getArticles($company->getCompanyName() ?? $company->getFirstName()) as $title => $body) {
            $section->addText(
                $title,
                ['bold' => true, 'size' => 12],
                ['spaceBefore' => 200, 'spaceAfter' => 60]
            );
            $section->addText(
                $body,
                ['size' => 11],
                ['alignment' => Jc::BOTH, 'spaceAfter' => 80]
            );
        }

        // ════════════════════════════════════════════════════════════════════
        // 6. كتلة التوقيعات والختم
        // ════════════════════════════════════════════════════════════════════
        $section->addTextBreak(1);
        $this->addSignatureBlock($section, $university, $company, $student);

        // ════════════════════════════════════════════════════════════════════
        // 7. التذييل
        // ════════════════════════════════════════════════════════════════════
        $section->addText(
            'Etablie en 03 exemplaires originaux : 1 pour l\'université, 1 pour l\'entreprise, 1 pour le département.',
            ['size' => 9, 'italic' => true, 'color' => '888888'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 300]
        );

        // ── حفظ الملف ─────────────────────────────────────────────────────
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($outputPath);

        return $filename;
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /** رأس الصفحة: ختم الجامعة + اسمها + العام الجامعي */
    private function addDocumentHeader($section, User $university): void
    {
        $stampFilename = $university->getStampFilename();
        $stampPath     = $this->stampsDir . $stampFilename;

        if ($stampFilename && file_exists($stampPath)) {
            $section->addImage($stampPath, [
                'width'         => 70,
                'height'        => 70,
                'alignment'     => Jc::CENTER,
                'wrappingStyle' => 'inline',
            ]);
            $section->addTextBreak(1);
        }

        $universityLabel = $university->getUniversityName()
            ?? $university->getCompanyName()
            ?? ($university->getFirstName() . ' ' . $university->getLastName());

        $section->addText(
            $universityLabel,
            ['bold' => true, 'size' => 15, 'color' => '1a3c6e'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 80]
        );

        // العام الجامعي الحالي تلقائياً
        $year = (int) date('Y');
        $month = (int) date('m');
        $academicYear = $month >= 9
            ? $year . '/' . ($year + 1)
            : ($year - 1) . '/' . $year;

        $section->addText(
            'Année Universitaire ' . $academicYear,
            ['bold' => true, 'size' => 12],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
        );

        // خط فاصل
        $section->addText('', [], [
            'borderBottomColor' => '1a3c6e',
            'borderBottomSize'  => 12,
            'spaceAfter'        => 200,
        ]);
    }

    /** كتلة نصية لطرف من أطراف الاتفاقية */
    private function addPartyBlock($section, string $role, array $lines): void
    {
        $section->addText(
            $role,
            ['bold' => true, 'size' => 12, 'color' => '1a3c6e'],
            ['spaceAfter' => 60]
        );
        foreach ($lines as $line) {
            if ($line === '') continue;
            $section->addText(
                $line,
                ['size' => 11],
                ['indent' => Converter::cmToTwip(0.8), 'spaceAfter' => 40]
            );
        }
    }

    /** جدول بيانات الطالب (2 عمود: التسمية | القيمة) */
    private function addFieldsTable($section, array $fields): void
    {
        $table = $section->addTable([
            'borderColor' => 'cccccc',
            'borderSize'  => 6,
            'cellMargin'  => 80,
        ]);

        foreach ($fields as $label => $value) {
            $table->addRow();

            $cell1 = $table->addCell(Converter::cmToTwip(5.5), [
                'bgColor' => 'EBF3FB',
            ]);
            $cell1->addText($label, ['bold' => true, 'size' => 10]);

            $cell2 = $table->addCell(Converter::cmToTwip(10.5));
            $cell2->addText((string) ($value ?? '—'), ['size' => 10]);
        }
    }

    /** كتلة التوقيعات مع ختم الجامعة */
    private function addSignatureBlock($section, User $university, User $company, User $student): void
    {
        $section->addText(
            'Fait à ............, le : ' . date('d/m/Y'),
            ['size' => 10],
            ['spaceBefore' => 200, 'spaceAfter' => 200]
        );

        // جدول التوقيعات (4 أعمدة)
        $table = $section->addTable([
            'borderColor' => 'cccccc',
            'borderSize'  => 6,
            'cellMargin'  => 80,
        ]);
        $table->addRow(Converter::cmToTwip(4.5));

        $columns = [
            'Chef de Département',
            'Faculté / Institut',
            "Pour l'Université\n(+ Cachet)",
            "Pour l'Entreprise\n(+ Cachet)",
        ];

        foreach ($columns as $index => $header) {
            $cell = $table->addCell(Converter::cmToTwip(3.9), [
                'borderColor' => 'cccccc',
                'borderSize'  => 6,
            ]);

            $cell->addText(
                $header,
                ['bold' => true, 'size' => 9],
                ['alignment' => Jc::CENTER]
            );

            // ختم الجامعة في العمود الثالث (index 2)
            if ($index === 2) {
                $stampFilename = $university->getStampFilename();
                $stampPath     = $this->stampsDir . $stampFilename;

                if ($stampFilename && file_exists($stampPath)) {
                    $cell->addImage($stampPath, [
                        'width'         => 55,
                        'height'        => 55,
                        'alignment'     => Jc::CENTER,
                        'wrappingStyle' => 'inline',
                    ]);
                } else {
                    // مساحة فارغة للتوقيع اليدوي
                    $cell->addText('', ['size' => 30]);
                }
            } else {
                $cell->addText('', ['size' => 30]); // مساحة للتوقيع
            }
        }
    }

    /** نصوص المواد القانونية */
    private function getArticles(string $companyName): array
    {
        return [
            'Article 1 : Objet' =>
                "La présente convention a pour objet de définir les conditions de prise en charge au sein de l'entreprise {$companyName}, d'étudiants de l'université pour l'accomplissement de stage pratique avec élaboration par chaque étudiant, en fin de formation, d'un rapport de stage.",

            'Article 2 : But du Stage' =>
                "Le stage de formation a pour but essentiel d'assurer l'illustration ou l'application pratique de l'enseignement dispensé à l'université, en faisant participer l'étudiant à un travail dans l'entreprise. L'organisation du stage est établie en fonction du programme des études poursuivies.",

            "Article 3 : Statut de l'étudiant" =>
                "Pendant son séjour à l'entreprise, l'étudiant stagiaire conserve son statut d'étudiant à l'université.",

            'Article 4 : Règlement intérieur' =>
                "Pendant son séjour à l'entreprise, l'étudiant est soumis aux droits et obligations du règlement intérieur de celle-ci, notamment en ce qui concerne la discipline, les horaires de travail, et le secret professionnel.",

            'Article 5 : Protection sociale' =>
                "Pendant son séjour à l'entreprise, l'étudiant continue de bénéficier du régime d'assurance maladie étudiant conformément à la législation relative aux assurances sociales.",

            'Article 6 : Responsabilité' =>
                "En cas d'accident survenu à l'étudiant au cours du travail ou du trajet, l'entreprise s'engage à aviser rapidement l'université et à procéder à la déclaration auprès de la CNAS.",

            'Article 7 : Confidentialité et rapport' =>
                "Le stage donne lieu à la rédaction d'un rapport que l'étudiant remet au responsable pédagogique. Ce rapport peut rester la propriété de l'entreprise en cas de sauvegarde du secret industriel.",
        ];
    }
}