<?php

namespace Advastore\Models\Advastore;

class CustomerAddress
{
    public ?string $firstName;
    public ?string $lastName;
    public ?string $phoneNumber;
    public ?string $companyName;
    public ?string $street;
    public ?string $houseNo;
    public ?string $postalCode;
    public ?string $city;
    public ?string $countryIsoCode;
    public ?string $additionToAddress;
    public ?string $addressNickname;
    public ?string $deliveryInfo;
    public ?int    $floor;

    /**
     * @param string|null $firstName
     * @return CustomerAddress
     */
    public function setFirstName(?string $firstName): CustomerAddress
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param string|null $lastName
     * @return CustomerAddress
     */
    public function setLastName(?string $lastName): CustomerAddress
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string|null $phoneNumber
     * @return CustomerAddress
     */
    public function setPhoneNumber(?string $phoneNumber): CustomerAddress
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @param string|null $companyName
     * @return CustomerAddress
     */
    public function setCompanyName(?string $companyName): CustomerAddress
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @param string|null $street
     * @return CustomerAddress
     */
    public function setStreet(?string $street): CustomerAddress
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @param string|null $houseNo
     * @return CustomerAddress
     */
    public function setHouseNo(?string $houseNo): CustomerAddress
    {
        $this->houseNo = $houseNo;
        return $this;
    }

    /**
     * @param string|null $postalCode
     * @return CustomerAddress
     */
    public function setPostalCode(?string $postalCode): CustomerAddress
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @param string|null $city
     * @return CustomerAddress
     */
    public function setCity(?string $city): CustomerAddress
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @param string|null $countryIsoCode
     * @return CustomerAddress
     */
    public function setCountryIsoCode(?string $countryIsoCode): CustomerAddress
    {
        $this->countryIsoCode = $countryIsoCode;
        return $this;
    }

    /**
     * @param string|null $additionToAddress
     * @return CustomerAddress
     */
    public function setAdditionToAddress(?string $additionToAddress): CustomerAddress
    {
        $this->additionToAddress = $additionToAddress;
        return $this;
    }

    /**
     * @param string|null $addressNickname
     * @return CustomerAddress
     */
    public function setAddressNickname(?string $addressNickname): CustomerAddress
    {
        $this->addressNickname = $addressNickname;
        return $this;
    }

    /**
     * @param string|null $deliveryInfo
     * @return CustomerAddress
     */
    public function setDeliveryInfo(?string $deliveryInfo): CustomerAddress
    {
        $this->deliveryInfo = $deliveryInfo;
        return $this;
    }

    /**
     * @param int|null $floor
     * @return CustomerAddress
     */
    public function setFloor(?int $floor): CustomerAddress
    {
        $this->floor = $floor;
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
