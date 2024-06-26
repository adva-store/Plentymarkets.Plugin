<?php

namespace Advastore\Services\Order;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;
use Advastore\Helper\OrderHelper;
use Advastore\Services\Authentication\PluginSetupPhaseAuthenticator;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Generator;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order as PlentyOrder;
use Plenty\Plugin\Log\Loggable;

/**
 * Class OrderConfirmation
 *
 * A class for handling order confirmation and updating order status and shipping package information.
 */
class OrderConfirmation
{
    use Loggable;

    /**
     * OrderConfirmation constructor.
     *
     * @param WizardData $wizardData
     * @param OrderRepositoryContract $orderRepository
     * @param WebserviceMethods $webserviceMethods
     * @param PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
     */
    public function __construct(
        private WizardData $wizardData,
        private OrderRepositoryContract $orderRepository,
        private WebserviceMethods $webserviceMethods,
        private PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
    ){}

    /**
     * Handle order confirmation, update order status, and shipping package information.
     *
     * @return string Returns 'OK' upon successful completion
     * @throws Exception
     */
    public function handle(): string
    {
        if(!$this->pluginSetupPhaseAuthenticator->isCurrentProcessAllowed()) {
            $this->getLogger('OrderConfirmation')->error(Settings::PLUGIN_NAME.'::Logger.error | Event handle OrderConfirmation not allowed in this plugin setup phase!');
            return "ERROR";
        }

        foreach ($this->getPlentyOrders() as $plentyOrders) {
            /** @var PlentyOrder $plentyOrder */
            foreach ($plentyOrders as $plentyOrder)
            {
                // TODO: quick fix, should be analysed
                if ($plentyOrder['id'] === null) {
                    continue;
                }

                if($externalOrderId = OrderHelper::getExternalOrderId($plentyOrder))
                {
                    $response = $this->webserviceMethods->getShipmentInformation($externalOrderId);

                    if(isset($response->status) &&
                        strtolower($response->status) == 'fulfilled' &&
                        is_array($response->parcels) &&
                        count($response->parcels) > 0)
                    {
                        foreach ($response->parcels as $parcel)
                        {
                            OrderHelper::setShippingPackage($plentyOrder['id'],$parcel->trackingNumber);
                        }
                        OrderHelper::setOrderStatus($plentyOrder['id'],Settings::STATUS_BOOK_OUTGOING_STOCK);
                    }
                    else if (strtolower($response->status) == 'cancelled')
                    {
                        OrderHelper::setOrderStatus($plentyOrder['id'], $this->wizardData->getErrorStatusId());
                        OrderHelper::setOrderComment($plentyOrder['id'],
                            "Der Auftrag wurde von Advastore storniert<br>");
                    }
                }
            }
        }

        return 'OK';
    }

    private function getPlentyOrders(): Generator
    {
		$this->orderRepository->setFilters([
            'warehouseId' => $this->wizardData->getWarehouseId(),
            'statusFrom'  => $this->wizardData->getStatusId(),
            'statusTo'    => $this->wizardData->getStatusId()
        ]);

        $page=1;
        do {
            $result = $this->orderRepository->searchOrders($page,50,['id']);
            yield $result->getResult();
            $page++;
        }
        while(!$result->isLastPage());
    }
}
