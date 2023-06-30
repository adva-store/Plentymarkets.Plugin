<?php /** @noinspection PhpUnused */

namespace Advastore\Providers;

use Advastore\Config\Settings;
use Advastore\Events\Procedures\ProcessAdvaOrder;
use Advastore\Wizard\AdvastoreWizard;
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

    public function boot(
        WizardContainerContract $wizardContainerContract,
        EventProceduresService $eventProceduresService,
    ): void
    {
        $wizardContainerContract->register(Settings::WIZARD_KEY, AdvastoreWizard::class);

        $eventProceduresService->registerProcedure(
            Settings::PLUGIN_NAME.'SendOrder' , ProcedureEntry::PROCEDURE_GROUP_ORDER,
            [
                'de' => Settings::PLUGIN_NAME.' - Sende Auftrag',
                'en' => Settings::PLUGIN_NAME.' - Send order',
            ],
            ProcessAdvaOrder::class.'@handle');
    }
}
