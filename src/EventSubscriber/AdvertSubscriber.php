<?php

namespace App\EventSubscriber;
use App\Event\AdvertCreatedEvent;
use App\Repository\AdminUserRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;



/* It's a subscriber class that listens to the event AdvertCreatedEvent and when it's triggered, it
sends a notification email to all the admins. */
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

  /**
   * It gets all the admins from the database, then for each admin, it creates a new notification
   * email, sets the recipient, subject, context and template, and finally sends the email
   * 
   * @param AdvertCreatedEvent event The event that was triggered
   */
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