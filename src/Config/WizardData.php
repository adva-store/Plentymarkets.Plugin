<?php

namespace Advastore\Config;

use Advastore\Services\Authentication\TokenAuthenticator;
use Exception;
use Plenty\Plugin\Log\Loggable;

/**
 * Class WizardData
 *
 * Represents a data handler for the WizardData.
 */
class WizardData extends WizardDataHandler
{
    use Loggable;

    /**
     * @var array Holds the settings data.
     */
    private array $settings;

    /**
     * WizardData constructor.
     * Initializes the settings data by calling the getWizardData method.
     */
    public function __construct()
    {
        $this->settings = $this->getWizardData();
    }

    /**
     * Get the settings data.
     *
     * @return array Returns an array containing the settings data.
     */
    public function getSettings(): array
    {
        return $this->settings ?? [];
    }

    /**
     * Check if the application is in testing mode.
     *
     * @return bool Returns true if testing mode is enabled, false otherwise.
     */
    public function isTesting(): bool
    {
        return $this->settings['isTesting'] ?? false;
    }

    /**
     * Get the API token for authentication.
     *
     * @return string Returns the API token.
     * @throws Exception Throws an exception if unable to get the API token.
     */
    public function getApiToken(): string
    {
        return pluginApp(TokenAuthenticator::class)->getApiKey();
    }

    /**
     * Get the sandbox token.
     *
     * @return string Returns the sandbox token.
     */
    public function getSandboxToken(): string
    {
        return $this->settings['sandBoxToken'] ?? '';
    }

    /**
     * Get the merchant ID.
     *
     * @return string Returns the merchant ID.
     */
    public function getMerchantId(): string
    {
        return $this->settings['merchantId'] ?? '';
    }

    /**
     * Get the warehouse ID.
     *
     * @return int Returns the warehouse ID.
     */
    public function getWarehouseId(): int
    {
        return $this->settings['warehouse'] ?? 0;
    }

    /**
     * Get the status ID.
     *
     * @return float Returns the status ID.
     */
    public function getStatusId(): float
    {
        return $this->settings['statusId'] ?? 0;
    }

    /**
     * Get the error status ID.
     *
     * @return float Returns the error status ID.
     */
    public function getErrorStatusId(): float
    {
        return $this->settings['errorStatusId'] ?? 0;
    }

    /**
     * Get the user ID for notice.
     *
     * @return int Returns the user ID for notice.
     */
    public function getUserIdForNotice(): int
    {
        return $this->settings['noticeUserId'] ?? 2;
    }
}

