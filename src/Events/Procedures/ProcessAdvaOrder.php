<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Order\OrderExport;
use Exception;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Plugin\Log\Loggable;

/**
 * Class ProcessAdvaOrder
 *
 * Event procedure to process and export an order to Advastore.
 */
class ProcessAdvaOrder
{
    use Loggable;

    /**
     * ProcessAdvaOrder constructor.
     *
     * @param OrderExport $orderExport
     */
    public function __construct(
        private OrderExport $orderExport
    ){}

    /**
     * Handle the event procedure to process and export an order to Advastore.
     *
     * @param EventProceduresTriggered $eventTriggered The event triggered data.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function handle(EventProceduresTriggered $eventTriggered): void
    {
        try {
            $order = $eventTriggered->getOrder();
            if (!$order->hasDeliveryOrders) {
                $advastoreOrder = $this->orderExport->export($order);
            }
        }
        catch (Exception $e) {
            $this->getLogger('process:order')->error('Exception',$e);
            exit();
        }

        $this->getLogger('ProcessAdvaOrder')->debug(Settings::PLUGIN_NAME.'::Logger.done',$advastoreOrder);
    }
}
