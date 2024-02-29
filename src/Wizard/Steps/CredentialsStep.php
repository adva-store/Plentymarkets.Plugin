<?php namespace Advastore\Wizard\Steps;

class CredentialsStep
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.credentials.title",
            "description" => "Wizard.credentials.description",
            "sections" => [
                [
                    "title" => "Wizard.credentials.title",
                    "form" => [
                        "isTesting" => [
                            "type" => "toggle",
                            "options" => [
                                "name" => "Wizard.credentials.testing",
                                "defaultValue" => false
                            ]
                        ],
                        "sandBoxToken" => [
                            "isVisible" => "isTesting",
                            "type" => "text",
                            "minValue" => 2,
                            "options" => [
                                "required"  => "isTesting",
                                "name" => "Wizard.credentials.sandboxtoken",
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}



