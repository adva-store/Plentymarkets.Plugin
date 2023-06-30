<?php

namespace Advastore\Controllers;

use Advastore\Config\Settings;
use Advastore\Helper\OrderHelper;
use Advastore\Services\Products\ProductExport;
use Plenty\Modules\Order\Shipping\PackageType\Contracts\ShippingPackageTypeRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdvastoreController extends Controller
{
    public function __construct(
        private Response $response
    ){}

    public function debug(): string
    {
        OrderHelper::setShippingPackage(1650,'12346ABCDEFG');

        return 'OK';
    }

    public function prepareProductData(): Response
    {
        $service = pluginApp(ProductExport::class);
        $service->prepareProductExport();

        return $this->response->make('done!');
    }

    public function downloadProductData(): Response
    {
        $service = pluginApp(ProductExport::class);
        $data = $service->getProductExport();

        return $this->response->make($data,200,[
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.Settings::PRODUCT_EXPORT_FILENAME.'"',
        ]);
    }
}
