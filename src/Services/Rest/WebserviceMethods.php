<?php namespace Advastore\Services\Rest;

use Advastore\Config\Settings;
use Advastore\Models\Advastore\Order;
use Advastore\Models\Request\RequestModel;
use Exception;
use Generator;
use Plenty\Plugin\Log\Loggable;

class WebserviceMethods extends Dispatcher
{
    use Loggable;

    /**
     * @throws Exception
     */
    public function sendOrder(Order $advastoreOrder)
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_ORDER;
        $request->postfields = $advastoreOrder;

        return $this->post($request);
    }

    /**
     * @throws Exception
     * @return Generator
     */
    public function getStocks(): Generator
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_STOCK . "?start=1&limit=50";

        while ($request->requestURL)
        {
            $response = $this->get($request);
            $request->requestURL = $response->links->next??null;

            yield $response->results;
        }
    }

    /**
     * @throws Exception
     */
    public function sendConfig($merchantId, $webHookUrl)
    {
        $request = pluginApp(RequestModel::class);
        $request->requestURL = Settings::ENDPOINT_CONFIG;
        $request->postfields = [
          'merchantId' => $merchantId,
          'webHookUrl' => $webHookUrl
        ];

        return $this->put($request);
    }
}
