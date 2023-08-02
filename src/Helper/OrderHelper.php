<?php

namespace Advastore\Helper;

use Advastore\Config\WizardData;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Comment\Contracts\CommentRepositoryContract;
use Plenty\Modules\Comment\Models\Comment;
use Plenty\Modules\Order\Address\Contracts\OrderAddressRepositoryContract;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use Plenty\Modules\Order\RelationReference\Models\OrderRelationReference;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Order\Shipping\Package\Contracts\OrderShippingPackageRepositoryContract;
use Plenty\Modules\Order\Shipping\PackageType\Contracts\ShippingPackageTypeRepositoryContract;

/**
 * Class OrderHelper
 *
 * A helper class for handling various operations related to orders.
 */
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
     * Write a comment for an order.
     *
     * @param int $orderId The ID of the order for which the comment is being written.
     * @param string $comment The content of the comment to be written.
     *
     * @return void
     */
    public static function setOrderComment(int $orderId, string $comment): void
    {
        $userId = pluginApp(WizardData::class)->getUserIdForNotice();
        if($orderId && $comment)
        {
            pluginApp(CommentRepositoryContract::class)->createComment([
                'referenceType' => Comment::REFERENCE_TYPE_ORDER,
                'userId' => $userId,
                'referenceValue' => $orderId,
                'text' => $comment,
                'isVisibleForContact' => false
            ]);
        }
    }

    /**
     * Sets an external order ID for a specific order.
     *
     * @param int $orderId The ID of the order to update.
     * @param mixed $externalOrderId The external order ID to set.
     * @return void
     */
    public static function setExternalOrderId(int $orderId, mixed $externalOrderId): void
    {
        pluginApp(OrderRepositoryContract::class)->update($orderId,[
            'properties' => [[
                "typeId" => OrderPropertyType::EXTERNAL_ORDER_ID,
                "value" => $externalOrderId
            ]]
        ]);
    }

	/**
	 * Get the external order ID associated with the given order, if available.
	 *
	 * This method searches for the external order ID in the properties of the provided order.
	 * If found, it returns the external order ID as a string. If not found, it returns false.
	 *
	 * @param array $orderArray
	 * @return string|bool The external order ID as a string if available, otherwise false.
	 */
    public static function getExternalOrderId(array $orderArray):string|bool
    {
        foreach ($orderArray['properties'] as $property)
        {
            if($property['typeId'] === OrderPropertyType::EXTERNAL_ORDER_ID) {
                return $property['value'];
            }
        }

        return false;
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
