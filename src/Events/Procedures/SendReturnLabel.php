<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Order\OrderDocumentsService;
use Exception;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Plugin\Log\Loggable;

/**
 * Class SendShippingLabel
 *
 * Event procedure to send the latest shipping label for an order.
 */
class SendReturnLabel
{
    use Loggable;

    /**
     * Handle the event procedure to send the latest shipping label.
     *
     * @param EventProceduresTriggered $eventTriggered The event triggered data.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function handle(EventProceduresTriggered $eventTriggered): void
    {
        try {
            $order  = $eventTriggered->getOrder();
            $result = pluginApp(OrderDocumentsService::class)->sendReturnLabel($order);

            $this
                ->getLogger('event:send-return-label')
                ->addReference('orderId',$order->id)
                ->report(Settings::PLUGIN_NAME.'::Logger.report',$result);
        }
        catch (Exception $e) {
            $this
                ->getLogger('event:send-return-label')
                ->addReference('orderId',$order->id)
                ->error('Exception',$e);
            exit();
        }
    }
}
