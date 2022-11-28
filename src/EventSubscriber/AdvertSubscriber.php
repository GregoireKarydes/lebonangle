<?php

namespace App\EventSubscriber;
use App\Event\AdvertCreatedEvent;
use App\Repository\AdminUserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class AdvertSubscriber implements EventSubscriberInterface {

    public function __construct(private readonly NotifierInterface $notifier, private readonly AdminUserRepository $adminUserRepository)
    {
        
    }
   
    public static function getSubscribedEvents() : array
    {
        return [
            AdvertCreatedEvent::NAME=>'sendNotificationToAdmin',
        ];
    }

    public function sendNotificationToAdmin(AdvertCreatedEvent $event) 
    {
        $notification = new Notification();
        $notification
        ->subject('Nouvelle annonce crée')
        ->content('Une nouvelle annonce a été crée');

        $allAdmins = $this->adminUserRepository->findAll();
        $arrayOfEmails = [];
        foreach ($allAdmins as $admin){
             array_push($arrayOfEmails, $admin->getEmail());
        }

        // $recipient = new Recipient(...$arrayOfEmails);
        $recipient = new Recipient('gregoire.karydes@gmail.com');
        $this->notifier->send($notification, $recipient);

    }


} 