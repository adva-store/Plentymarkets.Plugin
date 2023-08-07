<?php

namespace Advastore\Middlewares;

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
class TokenAuth extends Middleware
{
    use Loggable;

    /**
     * TokenAuth constructor.
     *
     * @param TokenAuthenticator $tokenAuthenticator
     */
    public function __construct(
        private TokenAuthenticator $tokenAuthenticator
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
        $token = $request->get('token');

        if(!$token || !$this->tokenAuthenticator->checkTokenAuth($token)) {
            header("HTTP/1.1 401 Unauthorized");
            die('401 Unauthorized - Please go away!');
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
