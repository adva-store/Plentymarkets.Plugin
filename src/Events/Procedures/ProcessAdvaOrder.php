<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
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
     */
    public function __construct(
        private OrderExport $orderExport,
        private AuthHelper $authHelper
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
            $advastoreOrder = $this->authHelper->processUnguarded(function () use ($eventTriggered){
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
