<?php

namespace App\Entity;

class Country
{
    public string  $iso2;
    public string  $nameFr;
    public string  $nameEN;
    public string  $capital;
    public ?string $encryptedName = null;
    public bool   $independant;
}
