<?php

namespace Advastore\Wizard\Steps;

use Plenty\Modules\Order\Status\Contracts\OrderStatusRepositoryContract;
use Plenty\Modules\User\Contracts\UserRepositoryContract;

class OrderSteps
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.order.title",
            "description" => 'Wizard.order.description',
            "sections" => [
                [
                    "title" => 'Wizard.order.title',
                    "form" => [
                        'statusId' => [
                            'type' => 'select',
                            'options' => [
                                "name" => "Wizard.order.success.status",
                                "listBoxValues" => $this->buildOrderStatusList()
                            ]
                        ],
                        'errorStatusId' => [
                            'type' => 'select',
                            'options' => [
                                "name" => "Wizard.order.error.status",
                                "listBoxValues" => $this->buildOrderStatusList()
                            ]
                        ],
                        "noticeUserId" => [
                            'type' => 'select',
                            'defaultValue' => false,
                            'options' => [
                                'name' => 'Wizard.order.notice.user',
                                'listBoxValues' => $this->generateUserListBoxValues()
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

    private function generateUserListBoxValues(): array
    {
        /** @var UserRepositoryContract $repo */
        $repo = pluginApp(UserRepositoryContract::class);

        $listBoxValues = [];

        foreach ($repo->getAll() as $user)
        {
            $listBoxValues[] = [
                'value'   => $user->id,
                'caption' => $user->realName. ' ('.$user->user.') '
            ];
        }

        return $listBoxValues;
    }
}
