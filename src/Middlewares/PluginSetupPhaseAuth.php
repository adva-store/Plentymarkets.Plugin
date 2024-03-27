<?php

namespace Advastore\Middlewares;

use Advastore\Services\Authentication\PluginSetupPhaseAuthenticator;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Middleware;

/**
 * Class PluginSetupPhaseAuth
 *
 * Middleware for authentication using process
 */
class PluginSetupPhaseAuth extends Middleware
{
    use Loggable;

    /**
     * PluginSetupPhaseAuth constructor.
     *
     * @param PluginSetupPhaseAuthenticator  $pluginSetupPhaseAuthenticator
     */
    public function __construct(
        private PluginSetupPhaseAuthenticator  $pluginSetupPhaseAuthenticator
    ){
        /** @noinspection PhpDynamicFieldDeclarationInspection */
        $this->includeRestRoutes = true;
    }

    /**
     * Method executed before the request is handled
     *
     * @param  Request $request
     * @return Request
     */
    public function before(Request $request): Request
    {
        $process = $request->get('process');

        if(!$process || !$this->pluginSetupPhaseAuthenticator->isCurrentProcessAllowed($process)) {
            header("HTTP/1.1 401 Unauthorized");
            die("401 Unauthorized - Webhook $process not allowed in this plugin setup phase!");
        }

        return $request;
    }

    /**
     * Method executed after the request is handled
     *
     * @param  Request $request
     * @param  Response $response
     * @return Response
     */
    public function after(Request $request, Response $response): Response
    {
        return $response;
    }
}
