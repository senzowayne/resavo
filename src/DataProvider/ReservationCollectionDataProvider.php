<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Component\Security\Core\Security;

final class ReservationCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $security;
    private $reservationRepo;

    /**
     * ReservationCollectionDataProvider constructor.
     * @param Security $security
     * @param ReservationRepository $reservationRepo
     */
    public function __construct(Security $security, ReservationRepository $reservationRepo)
    {
        $this->security = $security;
        $this->reservationRepo = $reservationRepo;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Reservation::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        if ($operationName === 'get')
        {
            return $this->reservationRepo->findBy(['user' => $this->security->getUser()]);
        }
    }
}
