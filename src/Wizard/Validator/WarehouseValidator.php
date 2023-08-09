<?php

namespace Advastore\Wizard\Validator;

use Exception;
use Plenty\Modules\StockManagement\Warehouse\Management\Contracts\StorageLocationManagementRepositoryContract;
use Plenty\Plugin\Log\Loggable;
use Plenty\Validation\Validator;

class WarehouseValidator extends Validator
{
    use Loggable;

    /**
     * Validates the provided data or fails if validation doesn't pass.
     *
     * @param array $data The data to be validated.
     * @return void
     */
    public static function validateOrFail(array $data): void
    {
        $storageRepo = pluginApp(StorageLocationManagementRepositoryContract::class);

        try {
            $storageLocation = $storageRepo->getStorageLocationById($data['storageLocationId']);
        }
        /** @noinspection PhpUnusedLocalVariableInspection */
        catch (Exception $e) {}

        if(!$data['useDefaultStorageLocation'] && $storageLocation->warehouseId===$data['warehouse']) {
            $data['validWarehouse'] = true;
        }

        parent::validateOrFail($data);
    }

    /**
     * Defines the attributes for the model.
     *
     * @return void
     */
    protected function defineAttributes(): void
    {
        $this->addBool('validWarehouse',true);
    }

    /**
     * Builds and returns custom validation messages.
     *
     * @return array The custom validation messages.
     */
    public function buildCustomMessages(): array
    {
        $messages = parent::buildCustomMessages();
        $messages["validWarehouse.required"] = "Der gewählte Lagerort ist nicht gültig!";

        return $messages;
    }
}
