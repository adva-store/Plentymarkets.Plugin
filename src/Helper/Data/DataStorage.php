<?php

namespace Advastore\Helper\Data;

use Advastore\Config\Settings;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;

class DataStorage
{
    public function __construct(
        private StorageRepositoryContract $storageRepository
    ){}

    public function saveData(string $filename, string $data): void
    {
        $this->storageRepository
            ->uploadObject(Settings::PLUGIN_NAME,$filename,$data);
    }

    public function loadData($filename): string
    {
        if($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME,$filename))
        {
            return $this->storageRepository->getObject(Settings::PLUGIN_NAME,$filename)->body;
        }

        return '';
    }

    public function deleteData($filename): void
    {
        $this->storageRepository->deleteObject(Settings::PLUGIN_NAME,$filename);
    }
}
