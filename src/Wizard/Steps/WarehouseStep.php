<?php

namespace Advastore\Wizard\Steps;

use Advastore\Wizard\Validator\WarehouseValidator;
use Plenty\Modules\Warehouse\Contracts\WarehouseRepositoryContract;
use Plenty\Modules\Warehouse\Models\Warehouse;

class WarehouseStep
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.warehouse.title",
            "description" => 'Wizard.warehouse.description',
            "validationClass" => WarehouseValidator::class,
            "sections" => [
                [
                    "title" => 'Wizard.warehouse.title',
                    "form" => [
                        'warehouse' => [
                            'type' => 'select',
                            'options' => [
                                "name" => "Wizard.warehouse.title",
                                "listBoxValues" => $this->buildWarehouseCheckBoxValues()
                            ]
                        ],
                        "useDefaultStorageLocation" => [
                            "type" => "toggle",
                            "options" => [
                                "name" => "Wizard.credentials.useDefaultStorageLocation",
                                "defaultValue" => true
                            ]
                        ],
                        'storageLocationId' => [
                            'isVisible' => '!useDefaultStorageLocation',
                            'type' => 'text',
                            'options' => [
                                "name" => "Wizard.warehouse.storageLocationId"
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function buildWarehouseCheckBoxValues(): array
    {
        $repo = pluginApp(WarehouseRepositoryContract::class);

        $query = $repo->search();

        /** @var Warehouse $warehouse */
        foreach ($query->getResult() as $warehouse)
        {
            $values[] = [
                "value"   => $warehouse->id,
                "caption" => $warehouse->name
            ];
        }

        return $values ?? [];
    }
}
