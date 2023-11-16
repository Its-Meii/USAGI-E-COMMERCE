<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\OrderDetailType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
              ->disable(Action::DELETE, Action::NEW)
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        $config = parent::configureFields($pageName);
        $config[]= CollectionField::new('orderDetails')
        ->setEntryType(OrderDetailType::class)
        ->allowAdd(false);
        
        return $config;
    }
}