<?php
namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\Tags;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    // public function __construct(private string $slugger)
    // {}

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setSlug'],
            BeforeEntityUpdatedEvent::class =>['setSlug']
        ];
    }

    public function setSlug(BeforeEntityUpdatedEvent|BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Product | $entity instanceof Tags) {
            $slug = str_replace(" ","-",trim(strtolower($entity->getName())));
            $entity->setSlug($slug);
        }else{
            return;
        }
    }
}