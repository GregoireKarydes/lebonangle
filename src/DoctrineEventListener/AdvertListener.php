<?php

namespace App\DoctrineEventListener;

use App\Entity\Advert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Event\AdvertCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;



class AdvertListener {

    public function __construct(private readonly   EventDispatcherInterface $dispatcher)
    {
        
    }

    public function postPersist(LifecycleEventArgs $event) : void {
        $advert = $event->getObject();
        if(!$advert instanceof Advert) {
            return;
        }
        //  send mail
        $this->dispatcher->dispatch(new AdvertCreatedEvent($advert), AdvertCreatedEvent::NAME);
    }

}