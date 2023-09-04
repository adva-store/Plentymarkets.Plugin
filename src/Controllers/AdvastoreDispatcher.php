<?php

namespace Advastore\Controllers;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;
use Advastore\Services\Authentication\RemoteAddressAuthenticator;
use Advastore\Services\Products\ProductExport;
use Advastore\Services\Products\StockImport;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Log\Loggable;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdvastoreDispatcher
{
    use Loggable;

    /**
     * AdvastoreDispatcher constructor.
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(
        private Request $request,
        private Response $response
    )
    {
        $this
            ->getLogger('AdvastoreDispatcher')
            ->addReference('endpoint',$this->request->get(Settings::URL_PARAMETER))
            ->report(Settings::PLUGIN_NAME.'::Logger.report');
    }

    /**
     * Dispatches the request to the appropriate handler based on the 'process' request parameter.
     *
     * @return Response|SymfonyResponse
     * @noinspection PhpUnused
     */
    public function dispatch(): Response|SymfonyResponse
    {
        $advahook = $this->request->get(Settings::URL_PARAMETER);

        try {
            switch ($advahook) {
                case Settings::WEBHOOK_HEALTHCHECK:
                    return $this->handleHealthCheck();

                case Settings::WEBHOOK_UPDATEPRODUCTS_GENERATE:
                    return $this->handleProductCSVGeneration();

                case Settings::WEBHOOK_UPDATEPRODUCTS_EXPORT:
                    return $this->handleProductCSVExport();

                case Settings::WEBHOOK_INVOKE_UPDATE_CONFIG:
                    return $this->handleConfigUpdate();

                case Settings::WEBHOOK_INVOKE_GET_STOCKS:
                    return $this->handleGetStocks();

                case Settings::WEBHOOK_INVOKE_GET_DELIVERYDATE:
                    return $this->handleGetDeliveryDates();

                default:
                    return $this->response->make('Unknown Webhook', Response::HTTP_METHOD_NOT_ALLOWED);
            }
        }
        catch (Exception $e) {
            $this->getLogger('AdvastoreDispatcher')->error('Exception',$e);
            return $this->response->make($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handles a health check request.
     *
     * Response HTTP 200: OK if plugin is installed and configured
     *
     * Response HTTP 202: ACCEPTED if the plugin is not yet configured
     *
     * @return Response indicating the status of the health check.
     */
    private function handleHealthCheck(): Response
    {
        $wizardData = pluginApp(WizardData::class);

        if(!$wizardData->getSettings())
        {
            return $this->response->make('ACCEPTED', Response::HTTP_ACCEPTED);
        }

        return $this->response->make('OK');
    }

    /**
     * Handles a request to generate product CSV.
     *
     * @return Response indicating the status of the operation.
     */
    private function handleProductCSVGeneration(): Response
    {
        pluginApp(ProductExport::class)->prepareProductExport();

        return $this->response->make('OK');
    }

    /**
     * Handles a request to export product CSV.
     *
     * @return Response containing the product CSV.
     */
    private function handleProductCSVExport(): Response
    {
        $data = pluginApp(ProductExport::class)->getProductExport();

        return $this->response->make($data,200,[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.Settings::PRODUCT_EXPORT_FILENAME.'"'
        ]);
    }

    /**
     * Handles a request to update configuration.
     * Responsible for retrieving ip whitelist
     *
     * @return Response indicating the status of the operation.
     * @throws Exception
     */
    private function handleConfigUpdate(): Response
    {
        $webService = pluginApp(WebserviceMethods::class);
        $wizardData = pluginApp(WizardData::class);
        $remoteAuth = pluginApp(RemoteAddressAuthenticator::class);

        if($wizardData->getSettings())
        {
            if($config = $webService->getConfig()) {
                $this->getLogger('Config update')->report(Settings::PLUGIN_NAME.'::Logger.report',$config);
                if($config->ipWhitelist) {
                    $remoteAuth->createNewAuth($config->ipWhitelist);
                    return $this->response->make('OK');
                }
            }

            return $this->response->make('Whitelist was not found!',Response::HTTP_NOT_ACCEPTABLE);
        }

        return $this->response->make('Plugin wizard was not completed!',Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * Handles a request to get stocks.
     *
     * @return Response containing the stocks.
     * @throws Exception
     */
    private function handleGetStocks(): Response
    {
        // todo: Temporarily disabled for Coffeefair. Must be reinstalled later.
        //pluginApp(StockImport::class)->importStock();

        return $this->response->make('OK');
    }

    /**
     * Handles a request to get delivery dates.
     *
     * @return Response containing the delivery dates.
     */
    private function handleGetDeliveryDates(): Response
    {
        // Not implemented now!
        return $this->response->make('OK');
    }
}
