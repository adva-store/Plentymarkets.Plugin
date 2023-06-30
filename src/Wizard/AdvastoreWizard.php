<?php /** @noinspection ALL */

namespace Advastore\Wizard;

use Advastore\Config\Settings;
use Advastore\Config\WizardDataHandler;
use Advastore\Wizard\SettingsHandler\SettingsHandler;
use Advastore\Wizard\Steps\CredentialsStep;
use Advastore\Wizard\Steps\OrderSteps;
use Advastore\Wizard\Steps\WarehouseStep;
use Plenty\Modules\Wizard\Services\WizardProvider;
use Plenty\Plugin\Application;

class AdvastoreWizard extends WizardProvider
{
    public function __construct(
        private CredentialsStep $credentialsStep,
        private WarehouseStep $warehouseStep,
        private OrderSteps $orderSteps
    ){}

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
              $this->credentialsStep->generateStep(),
              $this->warehouseStep->generateStep(),
              $this->orderSteps->generateStep()
          ]
        ];

        return $wizard;
    }

    /**
     * @return string
     */
    private function getIcon(): string
    {
        $app = pluginApp(Application::class);
        return $app->getUrlPath(Settings::PLUGIN_NAME).'/images/logo.png';
    }
}
