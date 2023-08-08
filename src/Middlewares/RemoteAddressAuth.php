<?php

namespace Advastore\Middlewares;

use Advastore\Services\Authentication\RemoteAddressAuthenticator;
use Advastore\Services\Authentication\TokenAuthenticator;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Middleware;

/**
 * Class TokenAuth
 *
 * Middleware for authentication using tokens
 */
class RemoteAddressAuth extends Middleware
{
    use Loggable;

    /**
     * TokenAuth constructor.
     *
     * @param TokenAuthenticator $tokenAuthenticator
     */
    public function __construct(
        private RemoteAddressAuthenticator $remoteAddressAuthenticator
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
        if(!$this->remoteAddressAuthenticator->checkAuth($_SERVER['REMOTE_ADDR'])) {
            header("HTTP/1.1 401 Unauthorized");
            die('401 Unauthorized - IP is not whitelisted!');
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
