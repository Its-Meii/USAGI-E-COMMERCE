<?php

namespace App\Service;

use Stripe\Stripe;
use App\Entity\Order;
use Stripe\Checkout\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class StripeService{
    private string $stripekey;
    private string $urlSuccess;
    private string $urlError;
    private CsrfTokenManagerInterface $tokenManager;
    public function __construct(private ContainerBagInterface $params, CsrfTokenManagerInterface $tokenManager){
        $this->stripekey = $params->get('stripe_key');
        $this->urlSuccess = $params->get('success_url');
        $this->urlError = $params->get('error_url');
        $this->tokenManager = $tokenManager;
        Stripe::setApiKey($this->stripekey);
    }
    public function createCart($data) {     
        $token = $this->tokenManager->getToken('stripe_token')->getValue();
        $session = Session::create([
            'line_items' => [$data],
            'mode' => 'payment',
            'success_url' => $this->urlSuccess.'/'.$token,
            'cancel_url' => $this->urlError
        ]);

        return $session->url;
    }

    
}


