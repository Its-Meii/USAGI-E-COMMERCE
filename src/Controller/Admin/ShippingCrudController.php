<?php

namespace App\Controller\Admin;

use App\Entity\Shipping;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShippingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shipping::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            MoneyField::new('price')->setCurrency('EUR'),
        ];
    }
    
}
