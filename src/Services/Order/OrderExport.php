<?php

namespace Advastore\Services\Order;

use Advastore\Config\WizardData;
use Advastore\Helper\OrderHelper;
use Advastore\Models\Advastore\Order as advastoreOrder;
use Advastore\Services\Rest\WebserviceMethods;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order as plentyOrder;
use Plenty\Plugin\Log\Loggable;
use Exception;

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
        $advastoreOrder = $this->orderBuilder->buildOrder($plentyOrder);
        $response = $this->webservice->sendOrder($advastoreOrder);

        if($response->requestId)
        {
            OrderHelper::setExternalOrderId($plentyOrder->id,$response->requestId);
            OrderHelper::setOrderStatus($plentyOrder->id,$this->wizardData->getStatusId());
        }
        else
        {
            OrderHelper::setOrderStatus($plentyOrder->id,$this->wizardData->getErrorStatusId());
            OrderHelper::setOrderComment($plentyOrder->id,
                "Fehler bei Auftragsexport an Advastore ($response->type)<br>" . implode('<br>',$response->problems));
        }

        return $advastoreOrder;
    }
}
