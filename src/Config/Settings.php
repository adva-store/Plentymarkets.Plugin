<?php

namespace Advastore\Config;

use Plenty\Modules\Order\Property\Contracts\OrderPropertyRepositoryContract;
use Plenty\Modules\Order\Referrer\Contracts\OrderReferrerRepositoryContract;

/**
 * Class Settings
 *
 * Contains constant values and a method for handling Advastore settings.
 */
class Settings
{
    const PLUGIN_NAME   = 'Advastore';
    const WIZARD_KEY    = 'AdvastoreWizard';
    const URL_PREFIX    = 'advahook';
    CONST URL_PARAMETER = 'process';

    const URL_PROD    =  'https://1313-88-133-166-32.ngrok-free.app/x';
    const URL_DEV     = 'https://1313-88-133-166-32.ngrok-free.app/';

    const PRODUCT_EXPORT_FILENAME = 'products.csv';

    const ENDPOINT_ORDER    = 'v1/orders/validate-and-confirm';
    const ENDPOINT_ORDER_STATUS    = 'v1/orders';
    const ENDPOINT_STOCK    = 'v1/stocks';
    const ENDPOINT_CONFIG   = 'v1/config';

    const ENDPOINT_DOCUMENT_INVOICE        = '/documents/invoice';
    const ENDPOINT_DOCUMENT_DELIVERY_NOTE  = '/documents/delivery-note';
    const ENDPOINT_DOCUMENT_RETURN_LABEL   = '/documents/customer-return-label';
    const ENDPOINT_DOCUMENT_RETURN_RECEIPT = '/documents/customer-return-receipt';

    const WEBHOOK_HEALTHCHECK             = 'healthCheck';
    const WEBHOOK_UPDATEPRODUCTS_GENERATE = 'generateProductCSV';
    const WEBHOOK_UPDATEPRODUCTS_EXPORT   = 'getProductCSV';
    const WEBHOOK_INVOKE_UPDATE_CONFIG    = 'getConfig';
    const WEBHOOK_INVOKE_GET_STOCKS       = 'getStocks';
    const WEBHOOK_INVOKE_GET_DELIVERYDATE = 'getDeliveryDates';

    const STATUS_BOOK_OUTGOING_STOCK      = 7;

    /**
     * Get the Referrer ID for the Advastore plugin.
     *
     * @return int The Referrer ID if found, otherwise 0.
     */
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

    public static function getOrderPropertyTypeId(): int
    {
        $repo = pluginApp(OrderPropertyRepositoryContract::class);

        $entries = $repo->getTypes(['de']);

        foreach ($entries->toArray() as $entry)
        {
            if($entry['names'][0]['name'] === Settings::PLUGIN_NAME)
            {
                return $entry['id'];
            }
        }

        return 0;
    }
}
