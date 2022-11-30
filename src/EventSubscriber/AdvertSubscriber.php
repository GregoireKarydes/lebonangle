<?php

namespace App\EventSubscriber;
use App\Event\AdvertCreatedEvent;
use App\Repository\AdminUserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\Notification\Notification;


class AdvertSubscriber implements EventSubscriberInterface{

    public function __construct(private  MailerInterface $mailer, private readonly AdminUserRepository $adminUserRepository)
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
        $advert = $event->getAdvert();
        $baseUrl = $_SERVER['SERVER_NAME'];
        $allAdmins = $this->adminUserRepository->findAll();
        foreach ($allAdmins as $admin){
             $recipient = $admin->getEmail();
             $notification = new NotificationEmail();
             $notification->to($recipient)
             ->subject('Une nouvelle annonce a été crée')
             ->context(['admin'=>$admin, 'url'=>$baseUrl, 'advertId'=>$advert->getId()])
             ->htmlTemplate('/email/advert-created.html.twig');
            
             $this->mailer->send($notification);
        }
    }

   




} 