<?php

namespace Advastore\Controllers;

use Advastore\Config\Settings;
use Advastore\Services\Products\ProductExport;
use Plenty\Modules\Document\Contracts\DocumentRepositoryContract;
use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Response;

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
        $repo = pluginApp(DocumentRepositoryContract::class);
        $repo->setFilters(['orderId' => 1650]);
        $result = $repo->find();
        return json_encode($result->getResult());
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
}

