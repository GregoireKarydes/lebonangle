<?php

namespace App\DoctrineEventListener;

use App\Entity\Advert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Picture;
use App\Event\AdvertCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;



class AdvertUserListener {

    public function __construct(private readonly UserPasswordHasherInterface $passwordhasher)
    {
        
    }

    public function prePersist(LifecycleEventArgs $event,  EventDispatcherInterface $dispatcher) : void {
        $advert = $event->getObject();
        //  send mail
        $dispatcher->dispatch(new AdvertCreatedEvent($advert), AdvertCreatedEvent::NAME);
    }

}