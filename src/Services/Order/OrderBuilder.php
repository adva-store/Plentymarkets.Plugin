<?php

namespace Advastore\Services\Order;

use Advastore\Helper\OrderHelper;
use Advastore\Models\Advastore\CustomerAddress;
use Advastore\Models\Advastore\Order as AdvaStoreOrder;
use Advastore\Models\Advastore\OrderPosition;
use Plenty\Modules\Account\Address\Models\AddressOption;
use Plenty\Modules\Account\Address\Models\AddressRelationType;
use Plenty\Modules\Order\Models\Order as PlentyOrder;
use Plenty\Modules\Order\Models\OrderItemType;
use Plenty\Plugin\Log\Loggable;

/**
 * Class OrderBuilder
 *
 * A class for building AdvaStore orders from PlentyOrders.
 */
class OrderBuilder
{
    use Loggable;

    /**
     * The allowed order item types for building AdvaStore orders.
     */
    const allowedOrderItems = [
      OrderItemType::TYPE_VARIATION,
      OrderItemType::TYPE_BUNDLE_COMPONENT,
      OrderItemType::TYPE_SET_COMPONENT
    ];

    /**
     * @var AdvaStoreOrder The AdvaStore order instance being built.
     */
    private AdvaStoreOrder $advastoreOrder;

    /**
     * OrderBuilder constructor.
     */
    public function __construct() {
        $this->advastoreOrder = pluginApp(AdvaStoreOrder::class);
    }

    /**
     * Build an AdvaStore order from the given PlentyOrder.
     *
     * @param PlentyOrder $plentyOrder The PlentyOrder to build the AdvaStore order from.
     * @return AdvaStoreOrder Returns the built AdvaStore order.
     */
    public function buildOrder(PlentyOrder $plentyOrder): AdvaStoreOrder
    {
        $this->advastoreOrder->externalOrderNo = $plentyOrder->id;
        $this->advastoreOrder->allowPartiallyFulfillment = false;
        $this->advastoreOrder->orderTime = date(DATE_W3C);
        $this->advastoreOrder->externalCustomerNo = OrderHelper::getCustomerId($plentyOrder);
        $this->advastoreOrder->email = OrderHelper::getAddress($plentyOrder,AddressRelationType::DELIVERY_ADDRESS)->email;

        $this->advastoreOrder->shippingAddress = $this->buildAddress($plentyOrder);
        $this->advastoreOrder->orderPositions  = $this->buildOrderPositions($plentyOrder);

        return $this->advastoreOrder;
    }

    /**
     * Build a customer address from the given PlentyOrder.
     *
     * @param PlentyOrder $order The PlentyOrder to build the customer address from.
     * @return CustomerAddress Returns the built customer address.
     */
    protected function buildAddress(PlentyOrder $order): CustomerAddress
    {
        $plentyAddress   = OrderHelper::getAddress($order,AddressRelationType::DELIVERY_ADDRESS);
        $customerAddress = pluginApp(CustomerAddress::class);

        // Find in options
        $postNumber = array_filter($plentyAddress->toArray()['options'],fn($x)=>$x['typeId']===AddressOption::TYPE_POST_NUMBER);
        $this->getLogger('debug shizzel')->error('debug',[
            'toArray' => $plentyAddress->toArray(),
            '$postNumber' => array_filter($postNumber)
        ]);
        $postNumber = ($postNumber) ? array_filter($postNumber)[0]['value'] :'';

        $customerAddress
            ->setCompanyName($plentyAddress->companyName)
            ->setFirstName($plentyAddress->firstName)
            ->setLastName($plentyAddress->lastName)
            ->setStreet($plentyAddress->street)
            ->setHouseNo($plentyAddress->houseNumber)
            ->setCity($plentyAddress->town)
            ->setPostalCode($plentyAddress->postalCode)
            ->setCountryIsoCode(OrderHelper::getISOCode($plentyAddress->countryId))
            ->setAdditionToAddress($postNumber??$plentyAddress->additional)
            ->setPhoneNumber($plentyAddress->phone);

        return $customerAddress;
    }

    /**
     * @param PlentyOrder $plentyOrder
     * @return array
     */
    protected function buildOrderPositions(PlentyOrder $plentyOrder): array
    {
        $orderPositions=[];
        foreach ($plentyOrder->orderItems as $orderItem)
        {
            if(in_array($orderItem->typeId,self::allowedOrderItems))
            {
                $orderPosition = pluginApp(OrderPosition::class);

                $orderPosition
                    ->setSellerSku($orderItem->itemVariationId)
                    ->setQuantity($orderItem->quantity)
                    ->setGrossSalesPrice($orderItem->amounts[0]->priceGross)
                    ->setNetSalesPrice($orderItem->amounts[0]->priceNet);

                $orderPositions[] = $orderPosition;
            }
        }

        return $orderPositions;
    }
}
