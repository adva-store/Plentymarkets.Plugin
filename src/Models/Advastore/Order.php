<?php

namespace Advastore\Models\Advastore;

class Order
{
    public ?string $email;
    public ?string $externalCustomerNo;
    public ?string $externalOrderNo;
    public bool $allowPartiallyFulfillment = false;
    public ?string $addressAliasId;
    public ?string $orderTime;

    /** @var CustomerAddress */
    public CustomerAddress $shippingAddress;

    /** @var OrderPosition[] */
    public array $orderPositions;
}
