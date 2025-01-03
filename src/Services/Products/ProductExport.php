<?php

namespace Advastore\Services\Products;

use Advastore\Config\Settings;
use Advastore\Helper\Data\CSVGenerator;
use Advastore\Helper\Data\DataStorage;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Item\Manufacturer\Contracts\ManufacturerRepositoryContract;
use Plenty\Modules\Item\Variation\Contracts\VariationSearchRepositoryContract;
use Plenty\Modules\Item\Variation\Models\Variation;
use Plenty\Plugin\Log\Loggable;

/**
 * Class ProductExport
 *
 * A class for exporting products to Advastore
 */
class ProductExport
{
    use Loggable;

    /**
     * @var array Holds the cached data.
     */
    private array $CACHE = [];

    /**
     * ProductExport constructor.
     *
     * @param CSVGenerator $CSVGenerator
     * @param DataStorage $dataStorage
     * @param VariationSearchRepositoryContract $variationSearchRepository
     * @param ItemRepositoryContract $itemRepository
     * @param ManufacturerRepositoryContract $manufacturerRepository
     */
    public function __construct(
        private CSVGenerator $CSVGenerator,
        private DataStorage $dataStorage,
        private VariationSearchRepositoryContract $variationSearchRepository,
        private ItemRepositoryContract $itemRepository,
        private ManufacturerRepositoryContract $manufacturerRepository
    ) {
    }

    /**
     * Prepare product data for export and save to CSV file.
     *
     * @return void
     */
    public function prepareProductExport(): void
    {
        $variationsData = $this->getVariationsdata();

        $CSVString = $this->CSVGenerator->createFromArrays($variationsData);

        $this->dataStorage->saveData(Settings::PRODUCT_EXPORT_FILENAME, $CSVString);
    }

    /**
     * Get the CSV content of the product export.
     *
     * @return string The content of the CSV file.
     */
    public function getProductExport(): string
    {
        return $this->dataStorage->loadData(Settings::PRODUCT_EXPORT_FILENAME);
    }

    /**
     * Retrieve product variation data.
     *
     * @return array An array of product variation data.
     */
    private function getVariationsdata(): array
    {
        $this->variationSearchRepository->clearFilters();
        $this->variationSearchRepository->setFilters([
            'referrerId' => Settings::getReferrerId(),
            'isActive' => true
        ]);

        $variationsData[] = $this->getHeader();
        $page = 1;
        do {
            $this->variationSearchRepository->setSearchParams([
                'page' => $page,
                'itemsPerPage' => 10,
                'with' => [
                    'variationBarcodes' => true,
                    'images' => true,
                    'variationAttributeValues' => true,
                    'variationSalesPrices' => true
                ]
            ]);

            $result = $this->variationSearchRepository->search();

            /** @var Variation $variation */
            foreach ($result->getResult() as $variation) {
                $item = $this->getItemData($variation['itemId'])->toArray();

                $variationsData[] = [
                    'sellerSku' => $variation['id'],
                    'gtins' => $variation['variationBarcodes'][0]['code'] ?? ' ',
                    'manufacturerSKU' => ' ',
                    'manufacturer' => $this->getManufacturer($item['manufacturerId']),
                    'minimumSizeBundle' => ' ',
                    'price' => $variation['variationSalesPrices'][0]['price'],
                    'containsBattery' => ' ',
                    'advaHandling' => 1,
                    'imageUrl' => $variation['images'][0]['urlPreview'] ?? ' ',
                    'sellerSkuName' => $item['texts'][0]['name1'] ?? ' ',
                    'sellerSkuDescription' => $item['texts'][0]['shortDescription'] ?? ' ',
                    'customsTariffNumber' => $this->getCustomTariffNumber($item['customsTariffNumber'], $variation['customsTariffNumber'])
                ];
            }

            $page++;
        }
        while (!$result->isLastPage());

        return $variationsData;
    }

    /**
     * Retrieve item data.
     *
     * @param int $itemId The ID of the item.
     * @return Item The Item object.
     */
    private function getItemData(int $itemId): Item
    {
        if (isset($this->CACHE['items'][$itemId])) {
            return $this->CACHE['items'][$itemId];
        }

        $item = $this->itemRepository->show($itemId);

        return $this->CACHE['items'][$itemId] = $item;
    }

    /**
     * Retrieve manufacturer data.
     *
     * @param int $manufacturerId The ID of the manufacturer.
     * @return string The Manufacturer object.
     */
    private function getManufacturer(int $manufacturerId): string
    {
        if (isset($this->CACHE['manufacturer'][$manufacturerId])) {
            return $this->CACHE['manufacturer'][$manufacturerId];
        }

        $manufacturer = ($manufacturerId)
            ? $this->manufacturerRepository->findById($manufacturerId)->name
            : 'Unknown';

        return $this->CACHE['manufacturer'][$manufacturerId] = $manufacturer;
    }

    /**
     * Retrieve custom tariff number.
     *
     * @param string $itemCustomsTariffNumber The custom tariff number of the item.
     * @param string $variantCustomsTariffNumber The custom tariff number of the variant.
     * @return string The custom tariff number as a string.
     */
    private function getCustomTariffNumber(string $itemCustomsTariffNumber, string $variantCustomsTariffNumber): string
    {
        // If itemCustomsTariffNumber is empty, check variantCustomsTariffNumber
        if (!empty($variantCustomsTariffNumber)) {
            return $variantCustomsTariffNumber;
        }

        // Check if itemCustomsTariffNumber is not empty
        if (!empty($itemCustomsTariffNumber)) {
            return $itemCustomsTariffNumber;
        }

        // Return an empty string if both are empty or null
        return '';
    }

    /**
     * Get the header of the CSV file.
     *
     * @return array The array containing the header data.
     */
    private function getHeader(): array
    {
        return [
            'sellerSku',            // Seller product number *
            'gtins',                // GTIN's of the product *
            'manufacturerSKU',      // SKU of the manufacturer
            'manufacturer',         // Manufacturer name
            'minimumSizeBundle',    // The smallest bundle for sale
            'price',                // The product price
            'containsBattery',      // Indicates whether the product has a battery

            'advaHandling',         // Indicates whether this is a physical item that is in the warehouse and will be
            // handled by advastore, or whether it is a non-physical item, such as a voucher or license,
            // that the store must handle itself.

            'imageUrl',             // Url to a picture of the article
            'sellerSkuName',        // Product title
            'sellerSkuDescription', // Article description,
            'customsTariffNumber'   // Customs tariff number
        ];
    }
}
