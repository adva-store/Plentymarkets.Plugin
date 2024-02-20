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
                        "pluginSetupPhase" => [
                            "type" => "select",
                            "options" => [
                                "name" => "Wizard.credentials.pluginSetupPhase",
                                "listBoxValues" => [
                                    [
                                        "caption" => "Wizard.credentials.pluginSetupPhase.Phase1",
                                        "value" => 1
                                    ],
                                    [
                                        "caption" => "Wizard.credentials.pluginSetupPhase.Phase2",
                                        "value" => 2
                                    ],
                                    [
                                        "caption" => "Wizard.credentials.pluginSetupPhase.Phase3",
                                        "value" => 3
                                    ]
                                ]
                            ]
                        ],
                        "merchantId" => [
                            "type" => "text",
                            "minValue" => 2,
                            "options" => [
                                "name" => "Wizard.credentials.merchantId",
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



