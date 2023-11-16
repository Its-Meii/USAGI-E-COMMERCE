<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ProductCrudController;
use App\Entity\ProductSize;
use App\Entity\Shipping;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('USAGI - Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Stats', 'fa-solid fa-chart-line', 'admin/stat');
        yield MenuItem::linkToCrud('Users', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Tags', 'fa-solid fa-tags', Tags::class);
        yield MenuItem::linkToCrud('Size', 'fa-solid fa-ruler', ProductSize::class);
        yield MenuItem::linkToCrud('Products', 'fa-solid fa-shirt', Product::class);
        yield MenuItem::linkToCrud('Shipping', 'fa-solid fa-truck-fast', Shipping::class);
        yield MenuItem::linkToCrud('Orders', 'fa fa-truck', Order::class);
    }
}
