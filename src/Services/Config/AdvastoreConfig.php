<?php

namespace Advastore\Services\Config;

use Advastore\Config\Settings;
use Advastore\Services\Rest\WebserviceMethods;
use Plenty\Modules\System\Contracts\WebstoreConfigurationRepositoryContract;
use Plenty\Plugin\Application;

class AdvastoreConfig
{
    public function __construct(
        private WebserviceMethods $webserviceMethods,
        private Application $application,
        private WebstoreConfigurationRepositoryContract $webstoreConfiguration
    ){}

    /**
     * @throws \Exception
     */
    public function sendConfig($merchantId): string
    {
        $webhookUrl  = $this->buildWebhookUrl();
        $webhookUrl .= '/rest/';
        $webhookUrl .= strtolower(Settings::PLUGIN_NAME);

        $response = $this->webserviceMethods->sendConfig($merchantId,$webhookUrl);

        return $response->apiKey;
    }

    private function buildWebhookUrl()
    {
        return $this->webstoreConfiguration->findByPlentyId($this->application->getPlentyId())->domainSsl;
    }
}
