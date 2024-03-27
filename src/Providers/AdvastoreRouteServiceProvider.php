<?php /** @noinspection PhpUnused */

namespace Advastore\Providers;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;
use Advastore\Controllers\AdvastoreController;
use Advastore\Controllers\AdvastoreDispatcher;
use Advastore\Middlewares\PluginSetupPhaseAuth;
use Advastore\Middlewares\RemoteAddressAuth;
use Advastore\Middlewares\TokenAuth;
use Advastore\Services\Authentication\RemoteAddressAuthenticator;
use Advastore\Services\Authentication\TokenAuthenticator;
use Advastore\Services\Order\OrderConfirmation;
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
        $prefix = Settings::URL_PREFIX;

        $apiRouter->version(['v1'], ['middleware' => [TokenAuth::class, RemoteAddressAuth::class, PluginSetupPhaseAuth::class]],function ($router) use ($prefix)
        {
            /** WebHooks
             *
             * The advastore.Api expects the external application to provide certain WebHooks.
             * The WebHooks are all sent to the same base URL. The functions are supplied via
             * the URL parameter process. In addition, the ApiKey is passed via the token parameter.
             * The ApiKey corresponds to the key with which the application logs on to advastore.Api.
            **/

            $router->get($prefix,AdvastoreDispatcher::class.'@dispatch');
        });

        $apiRouter->version(['v1'], ['middleware' => ['oauth']], function ($router) use ($prefix)
        {
            $router->get($prefix.'/debug/products/prepare',AdvastoreController::class.'@prepareProductData');
            $router->get($prefix.'/debug/products/export',AdvastoreController::class.'@downloadProductData');
            $router->get($prefix.'/debug/migrations/run',AdvastoreController::class.'@runMigrations');
            $router->get($prefix.'/debug/settings/get',WizardData::class.'@getSettings');
            $router->get($prefix.'/debug/order/confirmation',OrderConfirmation::class.'@handle');
            $router->get($prefix.'/debug/whitelist',RemoteAddressAuthenticator::class.'@getWhitelist');

            $router->delete($prefix.'/debug/settings/delete',WizardData::class.'@resetWizardData');
            $router->delete($prefix.'/debug/authtoken/delete',TokenAuthenticator::class.'@resetAuthToken');
            $router->delete($prefix.'/debug/whitelist/delete',RemoteAddressAuthenticator::class.'@resetAuth');

            $router->get($prefix.'/debug',AdvastoreController::class.'@debug');
        });
    }
}
