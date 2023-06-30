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

class OrderExport
{
    use Loggable;

    public function __construct(
        private WebserviceMethods $webservice,
        private OrderBuilder $orderBuilder,
        private OrderRepositoryContract $orderRepository,
        private WizardData $wizardData
    ){}

    /**
     * Exports an order to the external system and updates the status and external order ID in Plenty.
     *
     * @param  plentyOrder $plentyOrder The order to export.
     * @return advastoreOrder The exported order.
     * @throws Exception If an error occurs during order export.
     */
    public function export(plentyOrder $plentyOrder): advastoreOrder
    {
        $advastoreOrder = $this->orderBuilder->buildOrder($plentyOrder);
        $response = $this->webservice->sendOrder($advastoreOrder);

        OrderHelper::setExternalOrdered($plentyOrder->id,$response->requestId);
        OrderHelper::setOrderStatus($plentyOrder->id,$this->wizardData->getStatusId());

        return $advastoreOrder;
    }
}
