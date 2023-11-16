<?php

namespace App\Controller\Admin;

use App\Entity\ProductSize;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductSizeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProductSize::class;
    }

  
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
        ];
    }
  
}
