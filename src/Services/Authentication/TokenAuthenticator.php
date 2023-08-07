<?php

namespace Advastore\Services\Authentication;

use Advastore\Config\Settings;
use Exception;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;
use Plenty\Plugin\Http\Request;

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
     * @param Request $request
     */
    public function __construct(
        private StorageRepositoryContract $storageRepository,
        private Request $request
    ){}

    /**
     * Checks if the given auth token exists or creates a new one if it doesn't.
     *
     * @param string $token
     * @return bool
     */
    public function checkTokenAuth(string $token): bool
    {
        $isGetConfig = $this->request->get(Settings::URL_PARAMETER)===Settings::WEBHOOK_INVOKE_UPDATE_CONFIG;

        if(!$this->authExists() && $isGetConfig)
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
        if($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME,self::AUTH_FILENAME))
        {
            $savedToken = $this->storageRepository->getObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME)->body;

            return $token === $savedToken;
        }

        return false;
    }

	/**
	 * Deletes the current AuthToken
	 *
	 * @return string
     * @noinspection PhpUnused
     */
	public function resetAuthToken(): string
	{
		$this->storageRepository->deleteObject(Settings::PLUGIN_NAME,self::AUTH_FILENAME);
		return 'DELETED';
	}

	/**
	 * @throws Exception
	 */
	public function getApiKey()
	{
		if($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME,self::AUTH_FILENAME)) {
			return $this->storageRepository->getObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME)->body;
		}

		throw new Exception('No saved Apikey!');
	}
}
