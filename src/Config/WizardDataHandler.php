<?php

namespace Advastore\Config;

use Advastore\Helper\Data\DataStorage;
use Advastore\Services\Config\AdvastoreConfig;
use Exception;
use Plenty\Modules\Wizard\Contracts\WizardDataRepositoryContract;
use Plenty\Modules\Wizard\Contracts\WizardSettingsHandler;

class WizardDataHandler implements WizardSettingsHandler
{
    protected const FILE_NAME = 'settings.json';

    /**
     * Used by the AdvastoreWizard to save the wizard settings
     *
     * @param array $parameters
     * @return bool
     * @throws Exception
     */
    public function handle(array $parameters): bool
    {
        $dataStorage = pluginApp(DataStorage::class);
        $dataStorage->saveData(self::FILE_NAME,json_encode($parameters['data']));

        return true;
    }

    /**
     * Deletes the settings from DataStorage and the wizard itself
     *
     * @return string
     * @noinspection PhpUnused
     */
    public function resetWizardData(): string
    {
        $dataStorage = pluginApp(DataStorage::class);
        $WizardRepo  = pluginApp(WizardDataRepositoryContract::class);

        $WizardRepo->delete(Settings::WIZARD_KEY);
        $dataStorage->deleteData(self::FILE_NAME);

        return 'OK';
    }

    public function modifiesWizardData($key, $value): void
    {
        $data = $this->getWizardData();

        if(!empty($data))
        {
            $data[$key] = $value;

            $dataStorage = pluginApp(DataStorage::class);
            $dataStorage->saveData(self::FILE_NAME,json_encode($data));
        }
    }

    protected function getWizardData(): array
    {
        $dataStorage = pluginApp(DataStorage::class);
        $data = $dataStorage->loadData(self::FILE_NAME);

        return json_decode($data,true)??[];
    }


}
