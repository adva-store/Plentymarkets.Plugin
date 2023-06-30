<?php

namespace Advastore\Services\Products;

use Advastore\Config\WizardData;
use Advastore\Services\Rest\WebserviceMethods;
use Exception;
use Plenty\Modules\Item\VariationStock\Contracts\VariationStockRepositoryContract;
use Plenty\Plugin\Log\Loggable;

class StockImport
{
    use Loggable;

    public function __construct(
        private WizardData $wizardData,
        private WebserviceMethods $webserviceMethods,
        private VariationStockRepositoryContract $variationStockRepository
    ){}

    /**
     * @throws Exception
     */
    public function importStock(): void
    {
        foreach ($this->webserviceMethods->getStocks() as $stocks) {
            foreach ($stocks as $stock) {
                $this->correctStock($stock->sellerSku, (int)$stock->availableStock);
            }
        }

    }

    public function correctStock(mixed $variationId, int $stockQuantity): void
    {
        try {
            $this->variationStockRepository->correctStock($variationId,[
                'quantity' => (float) $stockQuantity,
                'warehouseId' => $this->wizardData->getWarehouseId(),
                'storageLocationId' => 0, // Standard-Lagerort
                'reasonId' => 301
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
