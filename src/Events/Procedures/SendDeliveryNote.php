<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Order\OrderDocumentsService;
use Exception;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Plugin\Log\Loggable;

/**
 * Class SendDeliveryNote
 *
 * Event procedure to send the latest delivery note for an order.
 */
class SendDeliveryNote
{
    use Loggable;

    /**
     * Handle the event procedure to send the latest delivery note.
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
            $result = pluginApp(OrderDocumentsService::class)->sendLatestDeliveryNote($order);

            $this
                ->getLogger('event:send-delivery-note')
                ->addReference('orderId',$order->id)
                ->report(Settings::PLUGIN_NAME.'::Logger.report',$result);
        }
        catch (Exception $e) {
            $this
                ->getLogger('event:send-delivery-note')
                ->addReference('orderId',$order->id)
                ->error('Exception',$e);
            exit();
        }
    }
}
