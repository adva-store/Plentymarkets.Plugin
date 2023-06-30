<?php

namespace Advastore\Wizard\Steps;

use Plenty\Modules\Order\Status\Contracts\OrderStatusRepositoryContract;

class OrderSteps
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.order.title",
            "description" => 'Wizard.order.description',
            "sections" => [
                [
                    "title" => 'Wizard.order.sections.title',
                    "form" => [
                        'statusId' => [
                            'type' => 'select',
                            'options' => [
                                "name" => "Status",
                                "listBoxValues" => $this->buildOrderStatusList()
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function buildOrderStatusList(): array
    {
        $repo = pluginApp(OrderStatusRepositoryContract::class);

        foreach ($repo->all() as $status)
        {
            $result[] = [
                "value"   => $status["statusId"],
                "caption" => $status->names["de"]
            ];
        }

        return $result ?? [];
    }
}
