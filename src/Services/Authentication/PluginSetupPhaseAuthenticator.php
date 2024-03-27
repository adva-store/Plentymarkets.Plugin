<?php
namespace Advastore\Services\Authentication;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;

class PluginSetupPhaseAuthenticator
{
    protected static $phaseProcessMapping = [
        1 => [
            Settings::WEBHOOK_HEALTHCHECK,
            Settings::WEBHOOK_INVOKE_UPDATE_CONFIG
        ],
        2 => [
            Settings::WEBHOOK_UPDATEPRODUCTS_GENERATE,
            Settings::WEBHOOK_UPDATEPRODUCTS_EXPORT,
            Settings::WEBHOOK_INVOKE_GET_STOCKS,
        ]
    ];

    protected static $minPhase = 1;
    protected static $maxPhase = 3;

    public static function getPluginSetupPhase()
    {
        $wizardData = pluginApp(WizardData::class);
        $pluginSetupPhase = $wizardData->getPluginSetupPhase();

        if (empty($pluginSetupPhase)) {
            $pluginSetupPhase = self::$minPhase;
        }

        return (int) $pluginSetupPhase;
    }

    /**
     * @param $process - if called empty, process will be marked as max phase process
     * @return bool
     */
    public static function isCurrentProcessAllowed($process = "")
    {
        $processPhase = self::getProcessPhaseFromMapping($process);
        $pluginSetupPhase = self::getPluginSetupPhase();

        // all webhooks not in mapping, will be marked as max phase webhooks
        if (empty($processPhase)) {
            $processPhase = self::$maxPhase;
        }

        return $pluginSetupPhase >= $processPhase;
    }

    protected static function getProcessPhaseFromMapping($currentProcess)
    {
        foreach (self::$phaseProcessMapping as $phase => $process) {
            if (in_array($currentProcess, $process)) {
                return $phase;
            }
        }

        return false;
    }
}
