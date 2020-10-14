<?php

namespace App\Events;

use App\Entity\Booking;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ResolveBookingSubscriber implements EventSubscriber
{
    protected const SVC_NAME = '[ResolveBookingSubscriber] :: ';

    private $logger;
    private $tokenStorage;
    private $requestStack;

    /**
     * @param LoggerInterface $logger
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     */
    public function __construct(
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    )
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ('easyadmin' !== $request->get('_route')) {
            $this->setCurrentUser($args);
        }
        $this->resolveBookingName($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if ('easyadmin' !== $request->get('_route')) {
            $this->setCurrentUser($args);
        }
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
            $entity->setName(sprintf('b%d-%s&%d#%d',
                $entity->getId(),
                substr($entity->getUser(), 0, 3),
                (new \DateTime('now'))->format('dmY'),
                $entity->getUser()->getId()
            ));
            $this->logger->debug(
                sprintf('%s resolveBookingName %s', self::SVC_NAME, $entity->getName())
            );
        }
    }
}
