<?php

use App\DoctrineEventListener\AdminUserListener;
use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Monolog\Test\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserListenerTest extends TestCase {


    
    public function testPrePersist() : void {
        $adminUser = (new AdminUser())->setPlainPassword('ede5165132');
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher
        ->expects(self::once())
        ->method('hashPassword')
        ->with($adminUser, 'ede5165132')
        ->willReturn('hashedpassword');

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $listener = new AdminUserListener($hasher);
        $listener->prePersist(new LifecycleEventArgs($adminUser, $objectManager));
        self::assertSame('hashedpassword', $adminUser->getPassword());
    }

    public function testPreUpdated() : void {
        $adminUser = (new AdminUser())->setPlainPassword('ede5165132');
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher
        ->expects(self::once())
        ->method('hashPassword')
        ->with($adminUser, 'ede5165132')
        ->willReturn('hashedpassword');

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $listener = new AdminUserListener($hasher);
        $listener->preUpdated(new LifecycleEventArgs($adminUser, $objectManager));
        self::assertSame('hashedpassword', $adminUser->getPassword());
    }
}