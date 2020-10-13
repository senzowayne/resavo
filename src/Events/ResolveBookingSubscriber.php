<?php

namespace App\Events;

use App\Entity\Booking;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ResolveBookingSubscriber implements EventSubscriber
{
    protected const SVC_NAME = '[ResolveBookingSubscriber] :: ';

    private $logger;
    private $tokenStorage;

    /**
     * @param LoggerInterface $logger
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setCurrentUser($args);
        $this->resolveBookingName($args);
    }

    public function setCurrentUser(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Booking) {
            $user = $this->tokenStorage->getToken()->getUser();
            $entity->setUser($user);

            $this->logger->debug(
                sprintf('%s setCurrentUser %s', self::SVC_NAME, $entity->getUser()->getName())
            );
        }
    }

    public function resolveBookingName(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Booking) {
            $user = $this->tokenStorage->getToken()->getUser();

            $entity->setName(sprintf('b%d-%s&%d#%d',
                $entity->getId(),
                substr($user->getName(), 0, 3),
                (new \DateTime('now'))->format('dmY'),
                $user->getId()
            ));
            $this->logger->debug(
                sprintf('%s resolveBookingName %s', self::SVC_NAME, $entity->getName())
            );
        }
    }
}
