<?php

namespace App\Controller\Admin;

use App\Entity\Siniestro;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class SiniestroCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Siniestro::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('servicio')->setChoices([
                'Energía' => 'energia',
                'Saneamiento' => 'saneamiento',
            ]),
            TextField::new('causa'),
            TextField::new('createdUser'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }
}

