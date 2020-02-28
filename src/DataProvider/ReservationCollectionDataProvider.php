<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Component\Security\Core\Security;

final class ReservationCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    /**
     * ReservationCollectionDataProvider constructor.
     */
    public function __construct(Security $security, BookingRepository $bookingRepository)
    {
        $this->security = $security;
        $this->bookingRepository = $bookingRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Booking::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        if ($operationName === 'get') {
            return $this->bookingRepository->findBy(['user' => $this->security->getUser()]);
        }
    }
}

