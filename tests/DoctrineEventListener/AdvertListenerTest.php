<?php

use App\DoctrineEventListener\AdvertListener;
use App\Entity\Advert;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Monolog\Test\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AdvertListenerTest extends TestCase {


    
    public function testPostPersist() : void {
        $advert = (new Advert());
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects(self::once())->method('dispatch');
        $listener = new AdvertListener($dispatcher);
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $listener->postPersist(new LifecycleEventArgs($advert, $objectManager));
    }

}