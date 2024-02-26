<?php

namespace Advastore\Controllers;

use Advastore\Config\Settings;
use Advastore\Helper\OrderHelper;
use Advastore\Migrations\CreateOrderProperties;
use Advastore\Migrations\CreateReferrer;
use Advastore\Services\Products\ProductExport;
use Plenty\Modules\Account\Address\Models\AddressRelationType;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class AdvastoreController
 *
 * Represents the Advastore Controller, extending the base Controller class.
 */
class AdvastoreController extends Controller
{
    /**
     * AdvastoreController constructor.
     *
     * @param Response $response The response instance to be used in the controller.
     */
    public function __construct(
        private Response $response
    ){}

    /**
     * Debug function, used while development
     *
     * @return string
     */
    public function debug(): string
    {
        $repo = pluginApp(OrderRepositoryContract::class);
        $order = $repo->findById(139, ['addresses']);
        $plentyAddress = OrderHelper::getAddress($order,AddressRelationType::DELIVERY_ADDRESS);

        $mergedAdditionalAddressFields = $plentyAddress->additional;

        if ($plentyAddress->address4) {
            $mergedAdditionalAddressFields = $mergedAdditionalAddressFields . PHP_EOL . $plentyAddress->address4;
        }

        return json_encode($mergedAdditionalAddressFields);
    }

    /**
     * Debug function
     * Prepare product data for export.
     *
     * @return Response Returns a Response instance with the 'done!' message.
     * @noinspection PhpUnused
     */
    public function prepareProductData(): Response
    {
        $service = pluginApp(ProductExport::class);
        $service->prepareProductExport();

        return $this->response->make('done!');
    }

    /**
     * Debug function
     * Download product data as CSV file.
     *
     * @return Response with the product data in CSV format as a downloadable file.
     * @noinspection PhpUnused
     */
    public function downloadProductData(): Response
    {
        $service = pluginApp(ProductExport::class);
        $data = $service->getProductExport();

        return $this->response->make($data, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . Settings::PRODUCT_EXPORT_FILENAME . '"',
        ]);
    }

    /** @noinspection PhpUnused */
    public function runMigrations(): SymfonyResponse
    {
        return $this->response->json([
            'orderProperty' => pluginApp(CreateOrderProperties::class)->run(),
            'orderReferer'  => pluginApp(CreateReferrer::class)->run()
        ]);
    }
}

