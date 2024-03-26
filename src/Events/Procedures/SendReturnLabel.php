<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Authentication\PluginSetupPhaseAuthenticator;
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
     * SendReturnLabel constructor.
     *
     * @param PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
     */
    public function __construct(
        private PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
    ){}

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
        if(!$this->pluginSetupPhaseAuthenticator->isCurrentProcessAllowed()) {
            $this->getLogger('event:send-return-label')->error(Settings::PLUGIN_NAME.'::Logger.error | Event handle event:send-return-label not allowed in this plugin setup phase!');
            return;
        }

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
