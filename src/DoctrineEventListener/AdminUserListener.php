<?php

namespace App\DoctrineEventListener;

use App\Entity\AdminUser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class AdminUserListener {

    public function __construct(private readonly UserPasswordHasherInterface $passwordhasher)
    {
        
    }

/**
 * If the object being persisted is an AdminUser, and the plain password is not empty, then hash the
 * password and set it on the AdminUser.
 * 
 * @param LifecycleEventArgs event The event that triggered the listener.
 * 
 * @return void The return value is the object that was passed in.
 */
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

    
/**
 * If the object being updated is an AdminUser, and the plain password is not empty, then hash the
 * password and set it.
 * 
 * @param LifecycleEventArgs event The event that was triggered.
 * 
 * @return void The return value is the value that is returned by the function.
 */
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