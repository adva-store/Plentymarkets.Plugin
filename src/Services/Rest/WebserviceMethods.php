<?php

namespace Advastore\Services\Rest;

use Advastore\Config\Settings;
use Advastore\Models\Advastore\Order;
use Advastore\Models\Request\RequestModel;
use Exception;
use Generator;
use Plenty\Plugin\Log\Loggable;

/**
 * Class WebserviceMethods
 *
 * A class for handling various web service methods to interact with the Advastore API.
 * Extends the Dispatcher class to handle HTTP requests to the API.
 */
class WebserviceMethods extends Dispatcher
{
    use Loggable;

    /**
     * Send an order to the Advastore API.
     *
     * @param Order $advastoreOrder The Order object representing the Advastore order to be sent.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function sendOrder(Order $advastoreOrder): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER;
        $request->postfields = $advastoreOrder;

        return $this->post($request);
    }

    /**
     * Get stocks from the Advastore API using pagination.
     *
     * @return Generator Returns a generator of stock data retrieved from the API.
     * @throws Exception
     */
    public function getStocks(): Generator
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_STOCK . "?start=1&limit=50";

        while ($request->requestURL)
        {
            $response = $this->get($request);
            $request->requestURL = $response->links->next??null;

            yield $response->results;
        }
    }

    /**
     * Send configuration data to the Advastore API.
     *
     * @param mixed $merchantId The merchant ID for the configuration.
     * @param string $webHookUrl The web hook URL for the configuration.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function sendConfig(mixed $merchantId, string $webHookUrl): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_CONFIG;
        $request->postfields = [
          'merchantId' => $merchantId,
          'webHookUrl' => $webHookUrl
        ];

        return $this->put($request);
    }

    /**
     * Get shipment information for a specific Advastore order.
     *
     * @param string $advastoreOrderId The Advastore orderID to retrieve shipment information for.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function getShipmentInformation(string $advastoreOrderId): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER.'/'. $advastoreOrderId;

        return $this->get($request);
    }

    /**
     * Send an invoice document to the Advastore API for a specific Advastore order.
     *
     * @param string $advastoreOrderId The Advastore order ID for which the invoice document is being sent.
     * @param string $documentBase64 The base64 encoded document data to be sent.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function sendInvoiceDocument(string $advastoreOrderId, string $documentBase64): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER.Settings::ENDPOINT_DOCUMENT_INVOICE;
        $request->postfields = [
            "orderId"  => $advastoreOrderId,
            "document" => $documentBase64
        ];

        return $this->post($request);
    }

    /**
     * Send a delivery-note document to the Advastore API for a specific Advastore order.
     *
     * @param string $advastoreOrderId The Advastore order ID for which the document is being sent.
     * @param string $documentBase64 The base64 encoded document data to be sent.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function sendDeliveryNoteDocument(string $advastoreOrderId, string $documentBase64): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER.Settings::ENDPOINT_DOCUMENT_DELIVERY_NOTE;
        $request->postfields = [
            "orderId"  => $advastoreOrderId,
            "document" => $documentBase64
        ];

        return $this->post($request);
    }

    /**
     * Send a return-label document to the Advastore API for a specific Advastore order.
     *
     * @param string $advastoreOrderId The Advastore order ID for which the document is being sent.
     * @param string $documentBase64 The base64 encoded document data to be sent.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function sendReturnLabelDocument(string $advastoreOrderId, string $documentBase64): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER.Settings::ENDPOINT_DOCUMENT_RETURN_LABEL;
        $request->postfields = [
            "orderId"  => $advastoreOrderId,
            "document" => $documentBase64
        ];

        return $this->post($request);
    }

    /**
     * Send a return-note document to the Advastore API for a specific Advastore order.
     *
     * @param string $advastoreOrderId The Advastore order ID for which the document is being sent.
     * @param string $documentBase64 The base64 encoded document data to be sent.
     * @return mixed Returns the response from the API.
     * @throws Exception
     */
    public function sendReturnReceipt(string $advastoreOrderId, string $documentBase64): mixed
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER.Settings::ENDPOINT_DOCUMENT_RETURN_RECEIPT;
        $request->postfields = [
            "orderId"  => $advastoreOrderId,
            "document" => $documentBase64
        ];

        return $this->post($request);
    }
}
