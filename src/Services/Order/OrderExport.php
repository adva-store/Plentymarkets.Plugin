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
    ) {}

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
        $response = null;

        try {
            $advastoreOrder = $this->orderBuilder->buildOrder($plentyOrder);
            $response = $this->webservice->sendOrder($advastoreOrder);

            // Log the response
            $this->getLogger('OrderExport')->debug(Settings::PLUGIN_NAME . '::Logger.debug', $response);

            // Check response for success or error
            if (!empty($response->orderId)) {
                OrderHelper::setExternalOrderId($plentyOrder->id, $response->orderId);
                OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getStatusId());
                OrderHelper::setOrderComment($plentyOrder->id, "Auftrag exportiert an Advastore ($response->orderId)");
            } else {
                $errorType = $response->type ?? 'Unknown';
                $errorTitle = $response->title ?? 'No title provided';
                $detail = $response->detail ?? 'No detail provided';

                OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getErrorStatusId());
                OrderHelper::setOrderComment($plentyOrder->id,
                    "Fehler bei Auftragsexport an Advastore ($errorType)<br>$errorTitle<br>$detail");
            }
        } catch (Exception $e) {
            // Handle unexpected errors
            $errorType = $response->type ?? 'Unknown';
            OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getErrorStatusId());
            OrderHelper::setOrderComment($plentyOrder->id,
                "Fehler bei Auftragsexport an Advastore ($errorType)<br>" . $e->getMessage());

            // Log the exception
            $this->getLogger('OrderExport')->error(Settings::PLUGIN_NAME . '::Logger.error', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
        }

        return $advastoreOrder;
    }
}
