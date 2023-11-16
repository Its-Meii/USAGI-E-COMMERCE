<?php

namespace App\EventSubscriber;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetLocal implements EventSubscriberInterface{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest',20]]
        ];
    }
    public function onKernelRequest(RequestEvent $event):void{
            $session = $event->getRequest()->getSession();
            $locale = $session->get('_locale');
            if($locale){
                $event->getRequest()->setLocale($locale);
            }
    }
}   