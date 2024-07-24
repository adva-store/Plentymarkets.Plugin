<?php

namespace Advastore\Services\Authentication;

use Advastore\Config\Settings;
use Advastore\Config\WizardData;
use Exception;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class TokenAuthenticator
 * This class handles the authentication token operations.
 */
class RemoteAddressAuthenticator
{
    use Loggable;

    /**
     * Constant for auth token filename
     */
    const AUTH_FILENAME ='remote_address_auth.json';

    /**
     * TokenAuthenticator constructor.
     *
     * @param StorageRepositoryContract $storageRepository
     * @param WizardData $wizardData
     */
    public function __construct(
        private StorageRepositoryContract $storageRepository,
        private WizardData $wizardData
    ){}

    /**
     * Checks if the given auth token exists or creates a new one if it doesn't.
     *
     * @param mixed $remoteAddress
     * @return bool
     */
    public function checkAuth(mixed $remoteAddress): bool
    {
        if($this->wizardData->isTesting()) {
            return true;
        }

        if(!$this->authExists()) {
            return true;
        }

        if($this->checkRemoteAddress($remoteAddress)) {
            return true;
        }

        $this->getLogger('IP Whitelist error')->error('Unauthorized',['IP'=>$remoteAddress]);
        return false;
    }

    public function test(): string {
        return "This is Test 1";
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
     * @param array $whiteList
     */
    public function createNewAuth(array $whiteList):void
    {
        $this->storageRepository->uploadObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME, json_encode($whiteList));
    }

    /**
     * Checks if the given token matches the saved token.
     *
     * @param mixed $remoteAddress
     * @return bool
     */
    private function checkRemoteAddress(mixed $remoteAddress): bool
    {
        if($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME,self::AUTH_FILENAME))
        {
            $storageObject = $this->storageRepository->getObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME);
            $whiteList = json_decode($storageObject->body,true);

            return in_array($remoteAddress,$whiteList);
        }

        return false;
    }

	/**
	 * Deletes the current AuthToken
	 *
	 * @return string
     * @noinspection PhpUnused
     */
	public function resetAuth(): string
	{
		$this->storageRepository->deleteObject(Settings::PLUGIN_NAME,self::AUTH_FILENAME);
		return 'DELETED';
	}

	/**
	 * @throws Exception
     * @noinspection PhpUnused
     */
	public function getWhitelist()
	{
		if($this->storageRepository->doesObjectExist(Settings::PLUGIN_NAME,self::AUTH_FILENAME)) {
            $storageObject = $this->storageRepository->getObject(Settings::PLUGIN_NAME, self::AUTH_FILENAME);
            return $storageObject->body;
		}

		throw new Exception('No saved whitelist!');
	}
}
