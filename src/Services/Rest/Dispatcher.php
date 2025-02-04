<?php /** @noinspection PhpUnused */

namespace Advastore\Services\Rest;

use Exception;
use Plenty\Plugin\Http\Response;
use Plenty\Plugin\Log\Loggable;
use Advastore\Models\Request\RequestModel;
use Advastore\Config\Settings;
use Advastore\Config\WizardData;

/**
 * Class Dispatcher
 *
 * A class for handling HTTP requests to the Advastore API.
 */
class Dispatcher
{
	use Loggable;

	protected string $baseURL;
    protected string $apiToken     = '';
    protected string $sandboxToken = '';
    protected bool   $isTesting    = true;

    /**
     * Dispatcher constructor.
     *
     * @param WizardData $wizardData
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
     * Send an HTTP request to the Advastore API.
     *
     * @param string $method The HTTP method (GET, POST, PUT, DELETE) for the request.
     * @param RequestModel $request The RequestModel object containing the request details.
     * @return mixed Returns the decoded JSON response from the API.
     * @throws Exception
     */
	private function sendRequest(string $method, RequestModel $request): mixed
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
        $curlError = curl_error($curl);

		$this
			->getLogger(Settings::PLUGIN_NAME." httpRequest")
			->debug(Settings::PLUGIN_NAME.'::Logger.done', [
                'header'    => $header,
				'method'    => $method,
				'url'       => $this->baseURL.$request->requestURL,
				'status'    => $httpcode,
				'request'   => $request,
				'response'  => $response,
                'curlError' => $curlError
			]);

		curl_close($curl);

        if($httpcode >= Response::HTTP_BAD_REQUEST && $httpcode != Response::HTTP_NOT_ACCEPTABLE) {
            throw new Exception($curlError,$httpcode);
        }

        return json_decode($response);
	}

    /**
     * Send a GET request to the Advastore API.
     *
     * @param RequestModel $requestModel The RequestModel object containing the request
     * @return mixed Returns the decoded JSON response from the API.
     * @throws Exception
     */
	protected function get(RequestModel $requestModel): mixed
    {
		return $this->sendRequest('GET', $requestModel);
	}

    /**
     * Send a POST request to the Advastore API.
     *
     * @param RequestModel $requestModel The RequestModel object containing the request
     * @return mixed Returns the decoded JSON response from the API.
     * @throws Exception
     */
    protected function post(RequestModel $requestModel): mixed {
		return $this->sendRequest('POST', $requestModel);
	}

    /**
     * Send a PUT request to the Advastore API.
     *
     * @param RequestModel $requestModel The RequestModel object containing the request
     * @return mixed Returns the decoded JSON response from the API.
     * @throws Exception
     */
    protected function put(RequestModel $requestModel): mixed {
		return $this->sendRequest('PUT', $requestModel);
	}

    /**
     * Send a DELETE request to the Advastore API.
     *
     * @param RequestModel $requestModel The RequestModel object containing the request
     * @return mixed Returns the decoded JSON response from the API.
     * @throws Exception
     */
    protected function delete(RequestModel $requestModel): mixed {
		return $this->sendRequest('DELETE', $requestModel);
	}
}
