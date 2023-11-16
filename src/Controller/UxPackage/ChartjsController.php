<?php

namespace App\Controller\UxPackage;

use App\Repository\OrderRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Service\UxPackageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChartjsController extends AbstractController
{
    #[Route('/admin/stat', name: 'admin_stat')]
    public function __invoke(ChartBuilderInterface $chartBuilder, OrderRepository $orderRepository,TranslatorInterface $translator): Response
    {
        // dd($orderRepository->getCA());
        $dbCA = $orderRepository->getCA();

        $labels = [];
        $data = [];

        foreach($dbCA as $ca){
            $labels[]=$ca['orderDate'];
            $data[]=$ca['total']/100;
        }
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $translator->trans('ca'),
                    'backgroundColor' => 'rgb(255, 99, 132, .4)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data,
                    'tension' => 0.4,
                ],
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
        ]);

        return $this->render('admin/stat.html.twig', [
            'chart' => $chart,
        ]);
    }
}
