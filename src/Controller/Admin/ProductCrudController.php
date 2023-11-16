<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\ProductSize;
use App\Controller\Admin\Field\TagsField;
use App\Controller\Admin\Field\ProductSizeField;
use App\Controller\Admin\Field\TranslationField;
use App\Repository\ProductRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public function __construct(private ProductRepository $productRepository){}
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setFormThemes(
                [
                    '@A2lixTranslationForm/bootstrap_5_layout.html.twig',
                    '@EasyAdmin/crud/form_theme.html.twig',
                ]
            );
    }
    public function configureFields(string $pageName): iterable
    {
        $fieldsConfig = [
            'name' => [
                'field_type' => TextType::class,
                'required' => true,
                'label' => 'Name'
            ]
        ];
        return [
            TranslationField::new('translations', 'Traduction du nom et de la description', $fieldsConfig)
            ->setRequired(true)
            ->hideOnIndex(),

            AssociationField::new('productSizes')
            ->setFormTypeOptions(['choice_label' => function(ProductSize $productSize){
                return $productSize->getName();
            }, 'by_reference'=>false])
            ->setRequired(true)
            ->formatValue( function ($value,$entity){
                $result = implode(',', $entity->getProductSizes()->toArray());
                if($result ===''){
                    return 'Aucune taille';
                }
                return  $result;
            }),

            MoneyField::new('price')->setCurrency('EUR'),
            IntegerField::new('quantity'),
            ImageField::new('photo_front')
            ->setBasePath('uploads/')
            ->setUploadDir('public/uploads/')
            ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
            ->setRequired(false),

            ImageField::new('photo_back')
            ->setBasePath('uploads/')
            ->setUploadDir('public/uploads/')
            ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
            ->setRequired(false),
            TagsField::new('Tags', 'Intégrer le produit dans une Collection',$fieldsConfig)
            ->setRequired(true)
            ->hideOnIndex(),
        ];
    }
}
