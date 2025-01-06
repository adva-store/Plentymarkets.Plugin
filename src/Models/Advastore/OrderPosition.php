<?php //todo: chatGPT

namespace Advastore\Models\Advastore;

class OrderPosition
{
    public ?int    $quantity;
    public ?string $sellerSku;
    public ?float  $netSalesPrice;
    public ?float  $grossSalesPrice;

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
     * @return array
     */
    public function toArray(): array
    {
        $addressArray = [];

        foreach ($this as $key => $value){
            $addressArray[$key] = $value;
        }

        return $addressArray;
    }
}
