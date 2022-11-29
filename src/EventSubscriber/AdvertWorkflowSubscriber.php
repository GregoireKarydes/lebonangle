<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Workflow\Event\Event;

class AdvertWorkflowSubscriber implements EventSubscriberInterface {
    

    /**
     */
    public function __construct(private readonly NotifierInterface $notifier) {
    }

    public static function getSubscribedEvents()
    {
        return [
            'workflow.advert_publishing.transition.publish' => 'notifyAuthor'
        ];
    }

    public function notifyAuthor(Event $event) : void {
        $advert = $event->getSubject();
        $notification = (new Notification())->subject('Votre annonce')->content('Votre annonce vient est maintenant publiée sur LeBonAngle');

        $recipient = new Recipient($advert->getEmail());
        try {
            $this->notifier->send($notification, $recipient);
            echo "<h1>Mail sent to".$advert->getEmail()."</h1>";
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            echo "<h1>$th</h1>";
        }
    }
}