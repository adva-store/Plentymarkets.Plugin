<?php /** @noinspection PhpUnused */

namespace Advastore\Services\Rest;

use Exception;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Log\Loggable;
use Advastore\Models\Request\RequestModel;
use Advastore\Config\Settings;
use Advastore\Config\WizardData;

class Dispatcher
{
	use Loggable;

	protected string $baseURL;
    protected string $apiToken     = '';
    protected string $sandboxToken = '';
    protected bool   $isTesting    = true;

    /**
     * @throws Exception
     */
    public function __construct(
        private WizardData $wizardData
    ){
        $this->isTesting = $this->wizardData->isTesting();
        $this->apiToken  = $this->wizardData->getApiToken();
        $this->baseURL   = Settings::URL_PROD;

        if($this->isTesting) {
            $this->baseURL  = Settings::URL_DEV;
            $this->sandboxToken = $this->wizardData->getSandboxToken();
        }
	}

	/**
	 * @throws Exception
	 */
	private function sendRequest($method, RequestModel $request): mixed
	{
		$header[] = "Content-Type: $request->contentType";
        $header[] = "ApiKey: $this->apiToken";
        if($this->isTesting) $header[] = "Sandboxapikey: $this->sandboxToken";

        $curl = curl_init();

        curl_setopt_array($curl, [
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_URL => $this->baseURL.$request->requestURL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $header
		]);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, !$this->isTesting);

		if($request->postfields)
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request->postfields));

		if($request->requestBody)
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request->requestBody);

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		$this
			->getLogger(Settings::PLUGIN_NAME." httpRequest")
			->debug(Settings::PLUGIN_NAME.'::Logger.done', [
                'header'    => $header,
				'method'    => $method,
				'url'       => $this->baseURL.$request->requestURL,
				'status'    => $httpcode,
				'request'   => $request,
				'response'  => $response,
                'curlError' => curl_error($curl)
			]);

		curl_close($curl);

        if($httpcode >= Response::HTTP_BAD_REQUEST) {
            throw new Exception("Dispatcher::sendRequest".$httpcode);
        }

        return json_decode($response);
	}

	/**
	 * @throws Exception
	 */
	protected function get(RequestModel $requestModel): mixed
    {
		return $this->sendRequest('GET', $requestModel);
	}

	/**
	 * @throws Exception
	 */
    protected function post(RequestModel $requestModel): mixed {
		return $this->sendRequest('POST', $requestModel);
	}

	/**
	 * @throws Exception
	 */
    protected function put(RequestModel $requestModel): mixed {
		return $this->sendRequest('PUT', $requestModel);
	}

	/**
	 * @throws Exception
	 */
    protected function delete(RequestModel $requestModel): mixed {
		return $this->sendRequest('DELETE', $requestModel);
	}
}
