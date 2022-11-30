<?php

namespace App\DoctrineEventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Event\AdvertCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;



class AdvertUserListener {

    public function __construct(private readonly UserPasswordHasherInterface $passwordhasher)
    {
        
    }

    public function prePersist(LifecycleEventArgs $event,  EventDispatcherInterface $dispatcher) : void {
        $advert = $event->getObject();
/**
 * "When an advert is created, dispatch an event to the event dispatcher."
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is available to all Symfony controllers. 
 * 
 * The event dispatcher is a service that is
 * 
 * @param LifecycleEventArgs event The event object that was triggered.
 * @param EventDispatcherInterface dispatcher The event dispatcher
 */
        //  send mail
        $dispatcher->dispatch(new AdvertCreatedEvent($advert), AdvertCreatedEvent::NAME);
    }

}