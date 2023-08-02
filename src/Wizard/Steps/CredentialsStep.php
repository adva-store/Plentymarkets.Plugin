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
                                'name' => 'Wizard.credentials.merchantId',
                            ]
                        ],
                        "isTesting" => [
                            "type" => "toggle",
                            "options" => [
                                "name" => "Wizard.credentials.testing",
                                "defaultValue" => false
                            ]
                        ],
                        "sandBoxToken" => [
                            'isVisible' => 'isTesting',
                            'type' => 'text',
                            'minValue' => 2,
                            "required"  => true,
                            'options' => [
                                'name' => 'Wizard.credentials.sandboxtoken',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}



