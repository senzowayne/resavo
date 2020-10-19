<?php

namespace App\Events;

use App\Entity\Booking;
use App\Entity\User;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ResolveBookingSubscriber implements EventSubscriber
{
    protected const SVC_NAME = '[ResolveBookingSubscriber] :: ';

    private LoggerInterface $logger;
    private TokenStorageInterface $tokenStorage;
    private RequestStack $requestStack;

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
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            $entity->setUser($user);

            $this->logger->debug(
                sprintf('%s setCurrentUser %s', self::SVC_NAME, $user->getName())
            );
        }
    }

    public function resolveBookingName(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Booking) {
            /** @var User $user */
            $user = $entity->getUser();
            $entity->setName(sprintf('b%d-%s&%d#%d',
                $entity->getId(),
                substr($user, 0, 3),
                (new \DateTime('now'))->format('dmY'),
                $user->getId()
            ));
            $this->logger->debug(
                sprintf('%s resolveBookingName %s', self::SVC_NAME, $entity->getName())
            );
        }
    }
}
