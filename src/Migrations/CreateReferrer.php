<?php /** @noinspection ALL */

namespace Advastore\Migrations;

use Plenty\Modules\Order\Referrer\Contracts\OrderReferrerRepositoryContract;
use Plenty\Plugin\Log\Loggable;
use Advastore\Config\Settings;

class CreateReferrer
{
    use Loggable;

    public function run(): string
    {
        if(!Settings::getReferrerId())
        {
            pluginApp(OrderReferrerRepositoryContract::class)
                ->create([
                    'name'         => Settings::PLUGIN_NAME,
                    'backendName'  => Settings::PLUGIN_NAME,
                    'isEditable'   => false,
                    'isFilterable' => true
                ]);
        }

        return 'Done!';
    }
}
