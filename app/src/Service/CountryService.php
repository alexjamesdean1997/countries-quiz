<?php

namespace App\Service;

use App\Entity\Country;

class CountryService
{
    public static function getAll(): array
    {
        $countriesArray = file_get_contents('./../data/countries.json');
        $countriesArray = json_decode($countriesArray,1);
        $countries      = [];

        foreach ($countriesArray as $countryItem) {
            $country = self::setCountry($countryItem);

            if (true === $country->independant) {
                $countries[] = $country;
            }
        }

        return $countries;
    }

    public static function getByNameFr(string $countryName): ?Country
    {
        $countries = file_get_contents('./../data/countries.json');
        $countries = json_decode($countries,1);

        foreach ($countries as $country) {
            if ($countryName === $country["nameFr"]){
                return self::setCountry($country);
            }
        }

        return null;
    }

    public static function setCountry(array $countryData): Country
    {
        $country                = new Country();
        $country->iso2          = $countryData['iso2'];
        $country->nameFr        = $countryData['nameFr'];
        $country->nameEN        = $countryData['nameEn'];
        $country->capital       = $countryData['capital'];
        $country->independant   = $countryData['independent'];
        return $country;
    }

    public static function getByIso2(string $countryIso2): ?Country
    {
        $countries = file_get_contents('./../data/countries.json');
        $countries = json_decode($countries,1);

        foreach ($countries as $country) {
            if ($countryIso2 === $country["iso2"]){
                return self::setCountry($country);
            }
        }

        return null;
    }
}