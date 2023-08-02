<?php

namespace Advastore\Migrations;

use Plenty\Modules\Order\Referrer\Contracts\OrderReferrerRepositoryContract;
use Plenty\Plugin\Log\Loggable;
use Advastore\Config\Settings;

/**
 * Class CreateReferrer
 *
 * A class for creating a custom order referrer if it does not exist.
 */
class CreateReferrer
{
    use Loggable;

    /**
     * Run the process to create the custom order referrer if it doesn't exist.
     *
     * @return string Returns a message indicating the result of the process.
     */
    public function run(): string
    {
        if (!Settings::getReferrerId()) {
            pluginApp(OrderReferrerRepositoryContract::class)
                ->create([
                    'name' => Settings::PLUGIN_NAME,
                    'backendName' => Settings::PLUGIN_NAME,
                    'isEditable' => false,
                    'isFilterable' => true
                ]);
        }

        return 'Done!';
    }
}

