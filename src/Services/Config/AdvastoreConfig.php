<?php

namespace Advastore\Services\Config;

use Advastore\Config\Settings;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Plenty\Modules\System\Contracts\WebstoreConfigurationRepositoryContract;
use Plenty\Plugin\Application;

/**
 * Class AdvastoreConfig
 *
 * A class for handling Advastore configuration and sending it via a webservice.
 */
class AdvastoreConfig
{
    /**
     * AdvastoreConfig constructor.
     *
     * @param WebserviceMethods $webserviceMethods
     * @param Application $application
     * @param WebstoreConfigurationRepositoryContract $webstoreConfiguration
     */
    public function __construct(
        private WebserviceMethods $webserviceMethods,
        private Application $application,
        private WebstoreConfigurationRepositoryContract $webstoreConfiguration
    ){}

    /**
     * Send the Advastore configuration to the webservice.
     *
     * @param mixed $merchantId The merchant ID for which the configuration is being sent.
     * @return string Returns the API key
     * @throws Exception
     */
    public function sendConfig(mixed $merchantId): string
    {
        $response = $this->webserviceMethods->sendConfig($merchantId, $this->buildWebhookUrl());

        return $response->apiKey;
    }

    /**
     * Build the webhook URL for sending the Advastore configuration.
     *
     * @return string Returns the webhook URL
     */
    private function buildWebhookUrl(): string
    {
        $sslDomain = $this->webstoreConfiguration->findByPlentyId($this->application->getPlentyId())->domainSsl;
        $sslDomain .= '/rest/';
        $sslDomain .= strtolower(Settings::URL_PREFIX);

        return $sslDomain;
    }
}

