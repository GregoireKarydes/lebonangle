<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\AdvertWorkflowSubscriber;
use PHPUnit\Framework\TestCase;


class AdvertWorkflowSubscriberTest extends TestCase {
    

    public function testEventSubscription() {
        $this->assertArrayHasKey('workflow.advert_publishing.transition.publish', AdvertWorkflowSubscriber::getSubscribedEvents());
    }

    public function testOnSendEmail() {
        $mailer = $this->createMock(\Symfony\Component\Notifier\NotifierInterface::class);
        $subscriber = new AdvertWorkflowSubscriber($mailer);
        $event = $this->createMock(\Symfony\Component\Workflow\Event\Event::class);
        $mailer->expects($this->once())->method('send');
        $subscriber->notifyAuthor($event);
    }
}