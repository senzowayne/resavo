<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DateBlocked;
use App\Repository\DateBlockedRepository;
use App\Repository\MeetingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class DateBlockedDateProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $dateBlockedRepo;

    public function __construct(DateBlockedRepository $dateBlockedRepo)
    {
        $this->dateBlockedRepo = $dateBlockedRepo;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return DateBlocked::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $dates = [];
        $allDays = $this->dateBlockedRepo->findAll();

        foreach ($allDays as $date) {
            $begin = $date->getStart();
            $end = $date->getEnd();
            $end = $end->modify( '+1 day' );

            $interval = new \DateInterval('P1D');
            $daterange = new \DatePeriod($begin, $interval ,$end);

            foreach($daterange as $d){
                $dates[] = $d->format("Y-m-d");
            }
        }
        return $dates;
    }
}