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
    ){}

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
        try {
            $advastoreOrder = $this->orderBuilder->buildOrder($plentyOrder);
            $response = $this->webservice->sendOrder($advastoreOrder);

            $this->getLogger('OrderExport')->debug(Settings::PLUGIN_NAME.'::Logger.debug', ['response' => $response]);

            if (!empty($response->orderId)) {
                OrderHelper::setExternalOrderId($plentyOrder->id, $response->orderId);
                OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getStatusId());
                OrderHelper::setOrderComment($plentyOrder->id, "Auftrag exportiert an Advastore ($response->orderId)");
            } else {
                OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getErrorStatusId());
                OrderHelper::setOrderComment($plentyOrder->id,
                    "Fehler bei Auftragsexport an Advastore xxx ({$response->type})<br>{$response->title}<br>{$response->detail}");
            }
        } catch (Exception $e) {
            OrderHelper::setOrderStatus($plentyOrder->id, $this->wizardData->getErrorStatusId());
            OrderHelper::setOrderComment($plentyOrder->id,
                "Fehler bei Auftragsexport an Advastore ($response->type)<br>" . $e->getMessage());
            return $advastoreOrder;
        }

        return $advastoreOrder;
    }
}
