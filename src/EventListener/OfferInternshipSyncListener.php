<?php

namespace App\EventListener;

use App\Entity\Offers;
use App\Entity\Internship;
use App\Service\ConventionGeneratorService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;

#[AsEntityListener(event: Events::preUpdate, entity: Offers::class)]
class OfferInternshipSyncListener
{
    public function __construct(
        private ConventionGeneratorService $conventionGenerator,
        private EntityManagerInterface $em
    ) {}

    public function preUpdate(Offers $offer, PreUpdateEventArgs $args): void
    {
        $dateChanged     = $args->hasChangedField('internshipStart');
        $durationChanged = $args->hasChangedField('duration');

        if (!$dateChanged && !$durationChanged) {
            return;
        }

        $newStart = $offer->getInternshipStart();

        $uow             = $this->em->getUnitOfWork();
        $internshipMeta  = $this->em->getClassMetadata(Internship::class);
        $applicationMeta = $this->em->getClassMetadata(\App\Entity\Application::class);

        foreach ($offer->getApplications() as $app) {
            if ($app->getStatus() !== 'accepted') continue;
            $internship = $app->getInternship();
            if (!$internship) continue;

            if ($dateChanged && $newStart) {
                $internship->setStartDate($newStart);
            }

            $startDate = $internship->getStartDate();
            if ($startDate && $offer->getDuration()) {
                $months = (int) filter_var($offer->getDuration(), FILTER_SANITIZE_NUMBER_INT);
                if ($months > 0) {
                    $internship->setEndDate(
                        (clone $startDate)->modify("+{$months} months")
                    );
                }
            }

            $uow->recomputeSingleEntityChangeSet($internshipMeta, $internship);

            try {
                $admin = $app->getStudent()?->getUniversityEntity();
                if ($admin) {
                    $filename = $this->conventionGenerator->generate($app, $admin);
                    $app->setConventionFile($filename);
                    $uow->recomputeSingleEntityChangeSet($applicationMeta, $app);
                }
            } catch (\Throwable $e) {
                // سجّل الخطأ مؤقتاً لتشخيص المشكلة
                error_log('[ConventionGenerator] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
        }
    }
}