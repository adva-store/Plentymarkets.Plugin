<?php namespace Advastore\Wizard\Steps;

class CredentialsStep
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.credentials.title",
            "description" => 'Wizard.credentials.description',
            "sections" => [
                [
                    "title" => 'Wizard.credentials.title',
                    "form" => [
                        "merchantId" => [
                            'type' => 'text',
                            'minValue' => 2,
                            "required"  => true,
                            'options' => [
                                'name' => 'merchantId',
                            ]
                        ],
                        "isTesting" => [
                            "type" => "toggle",
                            "options" => [
                                "name" => "Testing",
                                "defaultValue" => false
                            ]
                        ],
                        "sandBoxToken" => [
                            'isVisible' => 'isTesting',
                            'type' => 'text',
                            'minValue' => 2,
                            "required"  => true,
                            'options' => [
                                'name' => 'Sandbox-API Token',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}



