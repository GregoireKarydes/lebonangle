<?php


namespace App\Event;
use App\Entity\Advert;

class AdvertCreatedEvent {

    public const NAME ='advert.created';

    public function __construct(private readonly Advert $advert) {

    }
}