<?php

namespace Advastore\Config;

use Advastore\Helper\Data\DataStorage;
use Plenty\Modules\Wizard\Contracts\WizardDataRepositoryContract;
use Plenty\Modules\Wizard\Contracts\WizardSettingsHandler;

/**
 * Class WizardDataHandler
 *
 * A class that implements the WizardSettingsHandler interface and provides methods for handling wizard data.
 */
class WizardDataHandler implements WizardSettingsHandler
{
    /**
     * @var string The filename for storing the wizard settings data.
     */
    protected const FILE_NAME = 'settings.json';

    /**
     * Handle the wizard data and save it to the storage.
     *
     * @param array $parameters An array containing the data to be handled.
     * @return bool Returns true if the data handling was successful, false otherwise.
     */
    public function handle(array $parameters): bool
    {
        $dataStorage = pluginApp(DataStorage::class);
        $dataStorage->saveData(self::FILE_NAME, json_encode($parameters['data']));

        return true;
    }

    /**
     * Reset the wizard data by deleting it from storage.
     *
     * @return string Returns 'OK' if the wizard data was reset successfully.
     * @noinspection PhpUnused
     */
    public function resetWizardData(): string
    {
        $dataStorage = pluginApp(DataStorage::class);
        $WizardRepo = pluginApp(WizardDataRepositoryContract::class);

        $WizardRepo->delete(Settings::WIZARD_KEY);
        $dataStorage->deleteData(self::FILE_NAME);

        return 'OK';
    }

    /**
     * Modify specific wizard data key-value pair and save it to the storage.
     *
     * @param mixed $key The key for the data to be modified.
     * @param mixed $value The new value for the specified key.
     */
    public function modifiesWizardData(mixed $key, mixed $value): void
    {
        $data = $this->getWizardData();

        if (!empty($data)) {
            $data[$key] = $value;

            $dataStorage = pluginApp(DataStorage::class);
            $dataStorage->saveData(self::FILE_NAME, json_encode($data));
        }
    }

    /**
     * Get the stored wizard data.
     *
     * @return array Returns an array containing the wizard data, or an empty array if no data is found.
     */
    protected function getWizardData(): array
    {
        $dataStorage = pluginApp(DataStorage::class);
        $data = $dataStorage->loadData(self::FILE_NAME);

        return json_decode($data, true) ?? [];
    }
}

