<?php

namespace Advastore\Events\Procedures;

use Advastore\Config\Settings;
use Advastore\Services\Order\OrderBuilder;
use Advastore\Services\Order\OrderExport;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Plugin\Log\Loggable;

class ProcessAdvaOrder
{
    use Loggable;

    public function __construct(
        private OrderExport $orderExport
    ){}

    public function handle(EventProceduresTriggered $eventTriggered): void
    {
        try {
            $advastoreOrder = $this->orderExport->export($eventTriggered->getOrder());
        }
        catch (Exception $e) {
            $this->getLogger('process:order')->error('Exception',$e);
            exit();
        }

        $this->getLogger('ProcessAdvaOrder')->debug(Settings::PLUGIN_NAME.'::Logger.done',$advastoreOrder);
    }
}
