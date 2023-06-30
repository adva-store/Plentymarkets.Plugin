<?php /** @noinspection PhpUnused */

namespace Advastore\Providers;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;
use Advastore\Controllers\AdvastoreController;
use Advastore\Controllers\AdvastoreDispatcher;
use Advastore\Middlewares\TokenAuth;
use Advastore\Migrations\CreateReferrer;
use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\ApiRouter;

/**
 * Class AdvastoreRouteServiceProvider
 * @package Advastore\Providers
 */
class AdvastoreRouteServiceProvider extends RouteServiceProvider
{
    public function map(ApiRouter $apiRouter): void
    {
        $prefix = strtolower(Settings::PLUGIN_NAME);

        $apiRouter->version(['v1'], ['middleware' => [TokenAuth::class]], function ($router) use ($prefix)
        {
            // WebHooks
            // The advastore.Api expects the external application to provide certain WebHooks.
            // The WebHooks are all sent to the same base URL. The functions are supplied via
            // the URL parameter hookah. In addition, the ApiKey is passed via the token parameter.
            // The ApiKey corresponds to the key with which the application logs on to advastore.Api.

            $router->get($prefix,AdvastoreDispatcher::class.'@dispatch');
        });

        $apiRouter->version(['v1'], ['middleware' => ['oauth']], function ($router) use ($prefix)
        {
            $router->get($prefix.'/debug/products/prepare',AdvastoreController::class.'@prepareProductData');
            $router->get($prefix.'/debug/products/export',AdvastoreController::class.'@downloadProductData');
            $router->get($prefix.'/debug/migrations/run',CreateReferrer::class.'@run');
            $router->get($prefix.'/debug/settings/get',WizardData::class.'@getSettings');
            $router->delete($prefix.'/debug/settings/delete',WizardData::class.'@resetWizardData');

            $router->get($prefix.'/debug',AdvastoreController::class.'@debug');
        });
    }
}
