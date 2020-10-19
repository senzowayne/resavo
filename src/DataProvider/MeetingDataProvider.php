<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Meeting;
use App\Repository\MeetingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class MeetingDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private RequestStack $requestStack;
    private MeetingRepository $meetingRepository;

    public function __construct(RequestStack $requestStack, MeetingRepository $meetingRepository)
    {
        $this->requestStack = $requestStack;
        $this->meetingRepository = $meetingRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Meeting::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
            /** @var Request $request */
            $request = $this->requestStack->getCurrentRequest();

            $date = $request->query->get('date');
            $room = $request->query->get('room');

            /** @var array[] $meetingBlocked */
            $meetingBlocked = ($date !== null) ? $this->meetingRepository->meetingBlocked($room, $date) : [];

            return $this->meetingRepository->meetingAvailable($room, $meetingBlocked);
        }
}