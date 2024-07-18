<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Authentication\PluginSetupPhaseAuthenticator;
use Advastore\Services\Order\OrderExport;
use Exception;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Authorization\Services\AuthHelper;

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
     * @param AuthHelper $authHelper
     * @param PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
     */
    public function __construct(
        private OrderExport $orderExport,
        private AuthHelper $authHelper,
        private PluginSetupPhaseAuthenticator $pluginSetupPhaseAuthenticator
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
        if(!$this->pluginSetupPhaseAuthenticator->isCurrentProcessAllowed()) {
            $this->getLogger('ProcessAdvaOrder')->error(Settings::PLUGIN_NAME.'::Logger.error | Event handle ProcessAdvaOrder not allowed in this plugin setup phase!');
            return;
        }
        
        $this->getLogger('ProcessAdvaOrder')->error(Settings::PLUGIN_NAME.'::Logger.error | Start process order...');
        try {
            $advastoreOrder = $this->authHelper->processUnguarded(function () use ($eventTriggered){
                $this->getLogger('ProcessAdvaOrder')->error(Settings::PLUGIN_NAME.'::Logger.error | Export order...');
                return $this->orderExport->export($eventTriggered->getOrder());
            });
        }
        catch (Exception $e) {
            $this->getLogger('process:order')->error('Exception',$e);
            exit();
        }

        $this->getLogger('ProcessAdvaOrder')->debug(Settings::PLUGIN_NAME.'::Logger.done',$advastoreOrder);
    }
}
