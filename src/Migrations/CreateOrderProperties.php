<?php /** @noinspection PhpUnused */

namespace Advastore\Migrations;

use Advastore\Config\Settings;
use Plenty\Modules\Order\Property\Contracts\OrderPropertyRepositoryContract;

class CreateOrderProperties
{
    public function __construct(
       private OrderPropertyRepositoryContract $orderPropertyRepository
    ){}

    public function run(): string
    {
        if(!Settings::getOrderPropertyTypeId()) {
            $this->orderPropertyRepository->createType([
                "isErasable"=> true,
                "position"=> "0",
                "cast"=> "string",
                "names"=> [
                    [
                        "lang"=> "de",
                        "name"=> Settings::PLUGIN_NAME
                    ],
                    [
                        "lang"=> "en",
                        "name"=> Settings::PLUGIN_NAME
                    ]
                ]
            ]);
            return 'Property created!';
        }
        return 'Property already exits!';
    }
}
