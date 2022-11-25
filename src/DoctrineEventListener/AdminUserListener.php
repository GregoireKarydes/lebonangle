<?php

namespace App\DoctrineEventListener;

use App\Entity\AdminUser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class AdminUserListener {

    public function __construct(private readonly UserPasswordHasherInterface $passwordhasher)
    {
        
    }

    public function prePersist(LifecycleEventArgs $event) : void {
        $adminUser = $event->getObject();
        if(!$adminUser instanceof AdminUser) {
            return;
        }
        if(!empty($adminUser->getPlainPassword())) {
            $adminUser->setPassword(
                $this->passwordhasher->hashPassword($adminUser, $adminUser->getPlainPassword())
            );
        }
    }

    
    public function preUpdated(LifecycleEventArgs $event) : void {
        $adminUser = $event->getObject();
        if(!$adminUser instanceof AdminUser) {
            return;
        }
        if(!empty($adminUser->getPlainPassword())) {
            $adminUser->setPassword(
                $this->passwordhasher->hashPassword($adminUser, $adminUser->getPlainPassword())
            );
        }
    }
}