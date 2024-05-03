<?php

namespace Advastore\Services\Products;

use Advastore\Config\WizardData;
use Advastore\Services\Rest\WebserviceMethods;
use DateInterval;
use DateTime;
use Exception;
use Plenty\Modules\Item\VariationStock\Contracts\VariationStockRepositoryContract;
use Plenty\Plugin\Log\Loggable;

/**
 * Class StockImport
 *
 * Service to import and correct stock quantities.
 */
class StockImport
{
    use Loggable;

    /**
     * StockImport constructor.
     *
     * @param WizardData $wizardData The WizardData instance for data access.
     * @param WebserviceMethods $webserviceMethods The WebserviceMethods instance for accessing web services.
     * @param VariationStockRepositoryContract $variationStockRepository The VariationStockRepositoryContract instance for variation stock management.
     */
    public function __construct(
        private WizardData $wizardData,
        private WebserviceMethods $webserviceMethods,
        private VariationStockRepositoryContract $variationStockRepository
    ){}

    /**
     * Import stock quantities from the web service and correct stock in the system.
     *
     * @throws Exception If an error occurs during the import or stock correction process.
     *
     * @return void
     */
    public function importStock(): void
    {
        foreach ($this->webserviceMethods->getStocks() as $stocks) {
            foreach ($stocks as $stock) {
                $this->correctStock($stock->sellerSku, (int)$stock->availableStock);
            }
        }

    }

    /**
     * Correct the stock quantity for a specific variation.
     *
     * @param mixed $variationId The ID of the variation.
     * @param int $stockQuantity The stock quantity to be corrected.
     *
     * @return void
     */
    public function correctStock(mixed $variationId, int $stockQuantity): void
    {
        try {
            $this->variationStockRepository->correctStock($variationId,[
                'quantity' => (float) $stockQuantity,
                'warehouseId' => $this->wizardData->getWarehouseId(),
                'storageLocationId' => $this->wizardData->getStorageLocationId(),
                'reasonId' => 301,
                'batch' => 'LOT#'.$variationId,
                'bestBeforeDate' => '2028-06-02'
            ]);
        }
        catch (Exception $e) {
            $this
                ->getLogger('Stock-Import')
                ->addReference('variationId',$variationId)
                ->error('Exception',$e);
        }
    }
}
