<?php namespace Advastore\Wizard\Steps;

class PluginSetupPhaseStep
{
    public function generateStep(): array
    {
        return [
            "title" => "Wizard.pluginSetupPhase.title",
            "description" => "Wizard.pluginSetupPhase.description",
            "sections" => [
                [
                    "title" => "Wizard.pluginSetupPhase.title",
                    "form" => [
                        "pluginSetupPhase" => [
                            "type" => "select",
                            "options" => [
                                "name" => "Wizard.pluginSetupPhase.title",
                                "listBoxValues" => [
                                    [
                                        "caption" => "Wizard.pluginSetupPhase.Phase1",
                                        "value" => 1
                                    ],
                                    [
                                        "caption" => "Wizard.pluginSetupPhase.Phase2",
                                        "value" => 2
                                    ],
                                    [
                                        "caption" => "Wizard.pluginSetupPhase.Phase3",
                                        "value" => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}



