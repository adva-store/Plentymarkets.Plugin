<?php

namespace Advastore\Config;

use Plenty\Modules\Order\Referrer\Contracts\OrderReferrerRepositoryContract;

class Settings
{
    const PLUGIN_NAME = 'Advastore';
    const WIZARD_KEY  = 'AdvastoreWizard';

    const URL_PROD    =  '';
    const URL_DEV     = 'https://sandbox.advaapi.com/';

    const PRODUCT_EXPORT_FILENAME = 'products.csv';

    const ENDPOINT_ORDER  = 'v1/orders';
    const ENDPOINT_STOCK  = 'v1/stocks';
    const ENDPOINT_CONFIG = 'v1/config';

    const WEBHOOK_HEALTHCHECK             = 'healthCheck';
    const WEBHOOK_UPDATEPRODUCTS_GENERATE = 'generateProductCSV';
    const WEBHOOK_UPDATEPRODUCTS_EXPORT   = 'getProductCSV';
    const WEBHOOK_INVOKE_UPDATE_CONFIG    = 'getConfig';
    const WEBHOOK_INVOKE_GET_STOCKS       = 'getStocks';
    const WEBHOOK_INVOKE_GET_DELIVERYDATE = 'getDeliveryDates';

    public static function getReferrerId(): int
    {
        $repo = pluginApp(OrderReferrerRepositoryContract::class);

        $repo->setFilters(['name' => self::PLUGIN_NAME]);

        $result = $repo->search();

        if($result->getTotalCount() > 0)
        {
            return $result->getResult()[0]->id;
        }

        return  0;
    }
}
