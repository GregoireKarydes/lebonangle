<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Workflow\Event\Event;

/* It's a subscriber to the event 'workflow.advert_publishing.transition.publish' and when this event
is triggered, it calls the notifyAuthor method */
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

  /**
   * It sends a notification to the author of an ad when it's published.
   * 
   * @param Event event The event object
   */
    public function notifyAuthor(Event $event) : void {
        $advert = $event->getSubject();
        $notification = (new Notification())
        ->subject('Votre annonce')
        ->content('Votre annonce vient est maintenant publiée sur LeBonAngle')
        ;

        $recipient = new Recipient($advert->getEmail());
        $this->notifier->send($notification, $recipient);
    }
}