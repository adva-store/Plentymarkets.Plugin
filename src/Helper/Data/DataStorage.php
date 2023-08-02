<?php

namespace Advastore\Helper\Data;

use Advastore\Config\Settings;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;

/**
 * Class DataStorage
 *
 * A class for handling data storage operations using a StorageRepositoryContract.
 */
class DataStorage
{
    /**
     * DataStorage constructor.
     *
     * @param StorageRepositoryContract $storageRepository Used for data storage operations.
     */
    public function __construct(
        private StorageRepositoryContract $storageRepository
    ){}

    /**
     * Save data to the storage.
     *
     * @param string $filename The filename under which the data will be stored.
     * @param string $data The data to be saved.
     */
    public function saveData(string $filename, string $data): void
    {
        $this->storageRepository->uploadObject(Settings::PLUGIN_NAME, $filename, $data);
    }

    /**
     * Load data from the storage.
     *
     * @param string $filename The filename from which to load the data.
     * @return string Returns the loaded data as a string, or an empty string if the data is not found.
     */
    public function loadData(string $filename): string
    {
        if ($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME, $filename)) {
            return $this->storageRepository->getObject(Settings::PLUGIN_NAME, $filename)->body;
        }

        return '';
    }

    /**
     * Delete data from the storage.
     *
     * @param string $filename The filename of the data to be deleted.
     */
    public function deleteData(string $filename): void
    {
        if ($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME, $filename)) {
            $this->storageRepository->deleteObject(Settings::PLUGIN_NAME, $filename);
        }
    }
}
