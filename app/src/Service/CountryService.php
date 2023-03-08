<?php

namespace App\Service;

use App\Entity\Country;

class CountryService
{
    public static function getAll() : array
    {
        $countriesArray = file_get_contents('./../data/countries.json');
        $countriesArray = json_decode($countriesArray,1);
        $countries      = [];

        foreach ($countriesArray as $country) {
            $countries[] = self::setCountry($country);
        }

        return $countries;
    }

    public static function get(string $countryName) : ?Country
    {
        $countries = file_get_contents('./../data/countries.json');
        $countries = json_decode($countries,1);

        foreach ($countries as $country) {
            if ($countryName === $country["name-fr"]){
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
}