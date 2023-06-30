<?php

namespace Advastore\Helper;

use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Account\Contact\Models\Contact;
use Plenty\Modules\Order\Address\Contracts\OrderAddressRepositoryContract;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use \Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use Plenty\Modules\Order\RelationReference\Models\OrderRelationReference;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Models\Country;
use Plenty\Modules\Order\Shipping\Package\Contracts\OrderShippingPackageRepositoryContract;
use Plenty\Modules\Order\Shipping\PackageType\Contracts\ShippingPackageTypeRepositoryContract;
use Plenty\Modules\Order\Shipping\PackageType\Models\ShippingPackageType;

class OrderHelper
{
    /**
     * Get the address of the specified address type for an order.
     *
     * @param  Order $order The order object.
     * @param  int $addressType The address type (default: 1).
     * @return Address The address object or null if not found.
     */
    public static function getAddress(Order $order, int $addressType=1):Address
    {
        $addressRepo = pluginApp(OrderAddressRepositoryContract::class);

        return $addressRepo->findAddressByType($order->id,$addressType);
    }

    /**
     * Get the customer ID associated with an order.
     *
     * @param Order $order The order object.
     * @return int The customer ID or 0 if not found.
     */
    public static function getCustomerId(Order $order): int
    {
        /** @var OrderRelationReference $relation */
        foreach ($order->relations as $relation)
        {
            if($relation->referenceType === OrderRelationReference::REFERENCE_TYPE_CONTACT)
            {
                return $relation->referenceId;
            }
        }

        return 0;
    }

    /**
     * Retrieve the ISO code for a given country ID.
     *
     * @param int $countryId The ID of the country.
     * @return string|null The ISO code of the country, or null if not found.
     */
    public static function getISOCode(int $countryId): ?string
    {
        return pluginApp(CountryRepositoryContract::class)->findIsoCode($countryId,'isoCode2');
    }

    /**
     * Sets the status of a specific order.
     *
     * @param int $orderId The ID of the order to update.
     * @param int|float $status The new status for the order.
     * @return void
     */
    public static function setOrderStatus(int $orderId, int|float $status): void
    {
        pluginApp(OrderRepositoryContract::class)->updateOrder(
            ['statusId' => $status],$orderId
        );
    }

    /**
     * Sets an external order ID for a specific order.
     *
     * @param int $orderId The ID of the order to update.
     * @param mixed $externalOrderId The external order ID to set.
     * @return void
     */
    public static function setExternalOrdered(int $orderId, mixed $externalOrderId): void
    {
        pluginApp(OrderRepositoryContract::class)->update($orderId,[
            'properties' => [[
                "typeId" => OrderPropertyType::EXTERNAL_ORDER_ID,
                "value" => $externalOrderId
            ]]
        ]);
    }

    /**
     * Sets the shipping package information for a specific order.
     *
     * @param int $orderId The ID of the order to update.
     * @param mixed $packageNumber The package number to set.
     * @param int $weight The weight of the package (default is 500).
     * @return void
     */
    public static function setShippingPackage(int $orderId, mixed $packageNumber, int $weight=500): void
    {
        $shippingPackageTypeRepo = pluginApp(ShippingPackageTypeRepositoryContract::class);
        $shippingPackage = $shippingPackageTypeRepo->listShippingPackageTypes()[0];

        pluginApp(OrderShippingPackageRepositoryContract::class)
            ->createOrderShippingPackage($orderId,[
                'packageNumber' => $packageNumber,
                'packageId'     => $shippingPackage->id,
                'weight'        => $weight,
                'packageType'   => 24 // Karton
            ]);
    }
}
