<?php /** @noinspection PhpUnused */

namespace Advastore\Providers;

use Advastore\Config\Settings;
use Advastore\Events\Procedures\ProcessAdvaOrder;
use Advastore\Events\Procedures\SendDeliveryNote;
use Advastore\Events\Procedures\SendInvoice;
use Advastore\Events\Procedures\SendReturnLabel;
use Advastore\Events\Procedures\SendReturnNote;
use Advastore\Services\Order\OrderConfirmation;
use Advastore\Wizard\AdvastoreWizard;
use Plenty\Modules\Cron\Services\CronContainer;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Wizard\Contracts\WizardContainerContract;
use Plenty\Plugin\ServiceProvider;

/**
 * Class AdvastoreServiceProvider
 * @package Advastore\Providers
 */
class AdvastoreServiceProvider extends ServiceProvider
{
    /**
    * Register the route service provider
    */
    public function register(): void
    {
        $this->getApplication()->register(AdvastoreRouteServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called when the service provider is registered and is used to perform any
     * bootstrapping tasks required by the application. This is where you can register event listeners,
     * bind implementations to interfaces, or perform other initialization tasks.
     *
     * @param WizardContainerContract $wizardContainerContract
     * @param EventProceduresService $eventProceduresService
     * @param CronContainer $cronContainer
     * @return void
     */
    public function boot(
        WizardContainerContract $wizardContainerContract,
        EventProceduresService $eventProceduresService,
        CronContainer $cronContainer
    ): void
    {
        /** Register the AdvastoreWizard class under the Settings::WIZARD_KEY in the WizardContainerContract. **/
        $wizardContainerContract->register(Settings::WIZARD_KEY, AdvastoreWizard::class);

        /** Add the OrderConfirmation::class as a cron job to the CronContainer, executed hourly. **/
        $cronContainer->add(CronContainer::EVERY_FIFTEEN_MINUTES, OrderConfirmation::class);

        /** Register the "SendOrder" event procedure with the "ProcessAdvaOrder::handle" method in the EventProceduresService. **/
        $eventProceduresService->registerProcedure(
            Settings::PLUGIN_NAME . 'SendOrder',
            ProcedureEntry::PROCEDURE_GROUP_ORDER,
            [
                'de' => Settings::PLUGIN_NAME . ' - Sende Auftrag',
                'en' => Settings::PLUGIN_NAME . ' - Send order',
            ],
            ProcessAdvaOrder::class . '@handle'
        );

        /** Register the "SendInvoice" event procedure with the "SendInvoice::handle" method in the EventProceduresService.**/
        $eventProceduresService->registerProcedure(
            Settings::PLUGIN_NAME . 'SendInvoice',
            ProcedureEntry::PROCEDURE_GROUP_ORDER,
            [
                'de' => Settings::PLUGIN_NAME . ' - Sende Rechnung',
                'en' => Settings::PLUGIN_NAME . ' - Send invoice',
            ],
            SendInvoice::class . '@handle'
        );

        /** Register the "SendDeliveryNote" event procedure with the "SendDeliveryNote::handle" method in the EventProceduresService. **/
        $eventProceduresService->registerProcedure(
            Settings::PLUGIN_NAME . 'SendDeliveryNote',
            ProcedureEntry::PROCEDURE_GROUP_ORDER,
            [
                'de' => Settings::PLUGIN_NAME . ' - Sende Lieferschein',
                'en' => Settings::PLUGIN_NAME . ' - Send delivery note',
            ],
            SendDeliveryNote::class . '@handle'
        );

        /** Register the "SendReturnLabel" event procedure with the "SendReturnNote::handle" method in the EventProceduresService. **/
        $eventProceduresService->registerProcedure(
            Settings::PLUGIN_NAME . 'SendReturnLabel',
            ProcedureEntry::PROCEDURE_GROUP_ORDER,
            [
                'de' => Settings::PLUGIN_NAME . ' - Sende Rücksendelabel',
                'en' => Settings::PLUGIN_NAME . ' - Send return label',
            ],
            SendReturnLabel::class . '@handle'
        );

        /** Register the "SendReturnNote" event procedure with the "SendReturnNote::handle" method in the EventProceduresService. **/
        $eventProceduresService->registerProcedure(
            Settings::PLUGIN_NAME . 'SendReturnNote',
            ProcedureEntry::PROCEDURE_GROUP_ORDER,
            [
                'de' => Settings::PLUGIN_NAME . ' - Sende Rücksendeschein',
                'en' => Settings::PLUGIN_NAME . ' - Send return note',
            ],
            SendReturnNote::class . '@handle'
        );
    }
}
