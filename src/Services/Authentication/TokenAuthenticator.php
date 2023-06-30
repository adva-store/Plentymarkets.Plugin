<?php

namespace Advastore\Services\Authentication;

use Advastore\Config\Settings;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;

/**
 * Class TokenAuthenticator
 * This class handles the authentication token operations.
 */
class TokenAuthenticator
{
    /**
     * Constant for auth token filename
     */
    const AUTH_FILENAME ='authtoken.json';

    /**
     * TokenAuthenticator constructor.
     *
     * @param StorageRepositoryContract $storageRepository
     */
    public function __construct(
        private StorageRepositoryContract $storageRepository
    ){}

    /**
     * Checks if the given auth token exists or creates a new one if it doesn't.
     *
     * @param string $token
     * @return bool
     */
    public function doAuth(string $token): bool
    {
        if(!$this->authExists())
        {
            $this->createNewAuth($token);
            return true;
        }

        if($this->checkAuthToken($token))
        {
            return true;
        }

        return false;
    }

    /**
     * Checks if the auth token exists.
     *
     * @return bool
     */
    public function authExists(): bool
    {
        return $this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME, self::AUTH_FILENAME);
    }

    /**
     * Creates a new auth token.
     *
     * @param string $token
     */
    private function createNewAuth(string $token):void
    {
        $this->storageRepository->uploadObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME, $token);
    }

    /**
     * Checks if the given token matches the saved token.
     *
     * @param string $token
     * @return bool
     */
    private function checkAuthToken(string $token): bool
    {
        $savedToken = $this->storageRepository->getObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME)->body;

        return $token === $savedToken;
    }
}
