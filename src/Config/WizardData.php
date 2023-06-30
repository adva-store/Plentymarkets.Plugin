<?php namespace Advastore\Config;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Wizard\Contracts\WizardDataRepositoryContract;
use Plenty\Plugin\Log\Loggable;

class WizardData extends WizardDataHandler
{
    use Loggable;

    private mixed $settings;

    public function __construct()
    {
        $this->settings = $this->getWizardData();
    }

    public function getSettings()
    {
        return $this->settings ?? [];
    }

    public function isTesting(): bool
    {
        return $this->settings['isTesting'] ?? false;
    }

    public function getApiToken(): string
    {
        return $this->settings['apiKey'] ?? '';
    }

    public function getSandboxToken(): string
    {
        return $this->settings['sandBoxToken'] ?? '';
    }

    public function getMerchantId(): string
    {
        return $this->settings['merchantId'] ?? '';
    }

    public function getWarehouseId(): int
    {
        return $this->settings['warehouse'] ?? 0;
    }

    public function getStatusId()
    {
        return $this->settings['statusId'] ?? 0;
    }
}
