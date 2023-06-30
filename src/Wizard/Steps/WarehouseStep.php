<?php

namespace Advastore\Wizard\Steps;

use Plenty\Modules\Warehouse\Contracts\WarehouseRepositoryContract;
use Plenty\Modules\Warehouse\Models\Warehouse;

class WarehouseStep
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.warehouse.title",
            "description" => 'Wizard.warehouse.description',
            "sections" => [
                [
                    "title" => 'Wizard.warehouse.title',
                    "form" => [
                        'warehouse' => [
                            'type' => 'select',
                            'options' => [
                                "name" => "Lager",
                                "listBoxValues" => $this->buildWarehouseCheckBoxValues()
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
