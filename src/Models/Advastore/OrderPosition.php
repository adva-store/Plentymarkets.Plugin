<?php //todo: chatGPT

namespace Advastore\Models\Advastore;

class OrderPosition
{
    public ?int    $quantity;
    public ?string $sellerSku;
    public ?float  $netSalesPrice;
    public ?float  $grossSalesPrice;
    public ?array $shippingProviderServices = [];
    /**
     * @param int|null $quantity
     * @return OrderPosition
     */
    public function setQuantity(?int $quantity): OrderPosition
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param string|null $sellerSku
     * @return OrderPosition
     */
    public function setSellerSku(?string $sellerSku): OrderPosition
    {
        $this->sellerSku = $sellerSku;
        return $this;
    }

    /**
     * @param float|null $netSalesPrice
     * @return OrderPosition
     */
    public function setNetSalesPrice(?float $netSalesPrice): OrderPosition
    {
        $this->netSalesPrice = $netSalesPrice;
        return $this;
    }

    /**
     * @param float|null $grossSalesPrice
     * @return OrderPosition
     */
    public function setGrossSalesPrice(?float $grossSalesPrice): OrderPosition
    {
        $this->grossSalesPrice = $grossSalesPrice;
        return $this;
    }

    /**
     * Method to set a service in the shippingProviderServices dictionary
     * Currently used only for age verification
     *
     * @param string $key   The key for the service (e.g., provider name)
     * @param string $value The value for the service (e.g., service description)
     * @return self
     */
    public function setShippingProviderService(string $key, string $value): self
    {
        $this->shippingProviderServices[$key] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $addressArray = [];

        foreach ($this as $key => $value) {
            $addressArray[$key] = $value;
        }

        return $addressArray;
    }
}
