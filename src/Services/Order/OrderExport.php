<?php

namespace Advastore\Services\Order;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;
use Advastore\Helper\OrderHelper;
use Advastore\Models\Advastore\Order as advastoreOrder;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order as plentyOrder;
use Plenty\Plugin\Log\Loggable;

/**
 * Class OrderExport
 *
 * A class for exporting order to Advastore
 */
class OrderExport
{
    use Loggable;
    /**
     * OrderExport constructor.
     */
    public function __construct(
        private WebserviceMethods $webservice,
        private OrderBuilder $orderBuilder,
        private OrderRepositoryContract $orderRepository,
        private WizardData $wizardData
    ) {
    }

    /**
     * Export the PlentyOrder to Advastore.
     *
     * @param plentyOrder $plentyOrder The PlentyOrder to be exported to Advastore.
     *
     * @return advastoreOrder The AdvastoreOrder created from the exported data.
     * @throws Exception
     */
    public function export(plentyOrder $plentyOrder): advastoreOrder
    {
        $advastoreOrder = null;

        try {
            $advastoreOrder = $this->orderBuilder->buildOrder($plentyOrder);
            $response = $this->webservice->sendOrder($advastoreOrder);

            // Log the response
            $this->getLogger('OrderExport')->debug(Settings::PLUGIN_NAME . 'ECOMM ORDER RESPONSE', $response);

            // Check response for success or error
            if (!empty($response->orderId)) {
                OrderHelper::setExternalOrderId($plentyOrder->id, $response->orderId);
                OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getStatusId());
                OrderHelper::setOrderComment($plentyOrder->id, "Auftrag exportiert an Advastore ($response->orderId)");
            } else {
                // If orderId is not present, treat it as an error response
                $this->handleErrorResponse($plentyOrder, $response);
            }
        } catch (Exception $e) {
            $this->getLogger('OrderExport')->error("Error received from Advastore API", $e);
            $code = $e->getCode();
            $exceptionAsString = $e->__tostring();
            $this->getLogger('OrderExport')->error("Error status received $code", );
            $this->getLogger('OrderExport')->error("Error as string", $exceptionAsString);

            // Handle unexpected errors (e.g., API exceptions or network issues)
            $response = json_decode($e->getMessage()); // Attempt to parse error response if included
            $this->getLogger('OrderExport')->error("Error response get message method", $response);

            $this->handleErrorResponse($plentyOrder, $response, $e);
        }

        return $advastoreOrder;
    }

    /**
     * Handle error response from the API or exceptions.
     *
     * @param plentyOrder $plentyOrder
     * @param object|null $response
     * @param Exception|null $exception
     */
    private function handleErrorResponse(plentyOrder $plentyOrder, ?object $response, ?Exception $exception = null): void
    {
        if ($exception) {
            $errorType = 'Bad request';
        } else {
            $errorType = $response->type ?? 'Unknown';
        }
        $errorComments = [];

        // Check if the response has a problems array
        if (!empty($response->problems) && is_array($response->problems)) {
            foreach ($response->problems as $problem) {
                $errorTitle = $problem->title ?? 'Kein Titel angegeben!';
                $detail = $problem->detail ?? 'Keine Details angegeben!';
                $errorComments[] = "Titel: $errorTitle<br>Details: $detail";
            }
        } elseif (!empty($response->detail)) {
            // Fallback to the top-level detail if problems array is not available
            $errorComments[] = $response->detail;
        } elseif ($exception) {
            $errorComments[] = $exception->getMessage();
        } else {
            // Default message if no errors are provided
            $errorComments[] = 'Keine Details angegeben';
        }

        // Combine all error messages into one comment
        $errorComment = implode('<br><br>', $errorComments);

        // Set order status and comment with error details
        OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getErrorStatusId());
        OrderHelper::setOrderComment(
            $plentyOrder->id,
            "Fehler bei Auftragsexport an Advastore ($errorType)<br>$errorComment"
        );

        // Log additional exception details if provided
        if ($exception) {
            $this->getLogger('OrderExport')->error(Settings::PLUGIN_NAME . '::Logger.error', [
                'message' => $exception->getMessage(),
                'stack' => $exception->getTraceAsString()
            ]);
        }
    }
}
