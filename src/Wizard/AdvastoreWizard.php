<?php /** @noinspection ALL */

namespace Advastore\Wizard;

use Advastore\Config\Settings;
use Advastore\Config\WizardDataHandler;
use Advastore\Wizard\SettingsHandler\SettingsHandler;
use Advastore\Wizard\Steps\CredentialsStep;
use Advastore\Wizard\Steps\OrderSteps;
use Advastore\Wizard\Steps\PluginSetupPhaseStep;
use Advastore\Wizard\Steps\WarehouseStep;
use Plenty\Modules\Wizard\Services\WizardProvider;
use Plenty\Plugin\Application;

/**
 * Class AdvastoreWizard
 *
 * Represents a custom implementation of the wizard for setting up the Advastore plugin in the Plenty platform.
 * Extends the WizardProvider class to provide the wizard structure and steps.
 */
class AdvastoreWizard extends WizardProvider
{
    /**
     * AdvastoreWizard constructor.
     *
     * @param PluginSetupPhaseStep $pluginSetupPhaseStep
     * @param CredentialsStep $credentialsStep
     * @param WarehouseStep $warehouseStep
     * @param OrderSteps $orderSteps
     */
    public function __construct(
        private PluginSetupPhaseStep $pluginSetupPhaseStep,
        private CredentialsStep $credentialsStep,
        private WarehouseStep $warehouseStep,
        private OrderSteps $orderSteps
    ){}

    /**
     * Generate the structure of the Advastore wizard.
     *
     * @return array Returns an array representing the structure of the wizard.
     */
    protected function structure():array
    {
        $wizard = [
          'title' => 'Wizard.main.title',
          'key'   => Settings::WIZARD_KEY,
          'shortDescription' => 'Wizard.main.shortDescription',
          'iconPath' => $this->getIcon(),
          'reloadStructure' => true,
          'settingsHandlerClass' => WizardDataHandler::class,
          'translationNamespace' => Settings::PLUGIN_NAME,
          'steps' => [
              $this->pluginSetupPhaseStep->generateStep(),
              $this->credentialsStep->generateStep(),
              $this->warehouseStep->generateStep(),
              $this->orderSteps->generateStep()
          ]
        ];

        return $wizard;
    }

    /**
     * Get the URL path of the plugin's icon for the wizard.
     *
     * @return string Returns the URL path of the plugin's icon for the wizard.
     */
    private function getIcon(): string
    {
        $app = pluginApp(Application::class);
        return $app->getUrlPath(Settings::PLUGIN_NAME).'/images/logo.png';
    }
}
