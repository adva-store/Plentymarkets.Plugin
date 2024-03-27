<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Authentication\PluginSetupPhaseAuthenticator;
use Advastore\Services\Order\OrderDocumentsService;
use Exception;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Plugin\Log\Loggable;

/**
 * Class SendInvoice
 *
 * Event procedure to send the latest invoice for an order.
 */
class SendInvoice
{
    use Loggable;

    /**
     * SendInvoice constructor.
     *
     * @param PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
     */
    public function __construct(
        private PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
    ){}

    /**
     * Handle the event procedure to send the latest invoice.
     *
     * @param EventProceduresTriggered $eventTriggered The event triggered data.
     *
     * @return void
     * @noinspection PhpUnused
     */
    public function handle(EventProceduresTriggered $eventTriggered): void
    {
        if(!$this->pluginSetupPhaseAuthenticator->isCurrentProcessAllowed()) {
            $this->getLogger('event:send-invoice')->error(Settings::PLUGIN_NAME.'::Logger.error | Event handle event:send-invoice not allowed in this plugin setup phase!');
            return;
        }

        try {
            $order  = $eventTriggered->getOrder();
            $result = pluginApp(OrderDocumentsService::class)->sendLatestInvoice($order);

            $this
                ->getLogger('event:send-invoice')
                ->addReference('orderId',$order->id)
                ->report(Settings::PLUGIN_NAME.'::Logger.report',$result);

        }
        catch (Exception $e) {
            $this
                ->getLogger('event:send-invoice')
                ->addReference('orderId',$order->id)
                ->error('Exception',$e);
            exit();
        }
    }
}
