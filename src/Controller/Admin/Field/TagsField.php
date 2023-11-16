<?php
namespace App\Controller\Admin\Field;


use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;

final class TagsField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null, $fieldsConfig = []): self
    {
        return (new self())
        ->setProperty($propertyName)
        ->setLabel($label)
        ->setFormType(EntityType::class)
        ->setFormTypeOptions(
            [
                "class"=>Tags::class,
                "choice_label"=>"name",
                "multiple"=>false, //optionnel pour ManyToMany
                "expanded"=>true //optionnel pour ManyToMany
            ]);
    }
}