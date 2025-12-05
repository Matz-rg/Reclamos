<?php

namespace App\Controller\Admin;

use App\Entity\Mantenimiento;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MantenimientoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mantenimiento::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('servicio')->setChoices([
                'Energía' => 'energia',
                'Saneamiento' => 'saneamiento',
            ]),

            TextEditorField::new('detalle'),

            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),

        ];
    }
}
