<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('shuffle', [$this, 'shuffle']),
        ];
    }

    public function shuffle(array $array): array
    {
        shuffle($array);

        return $array;
    }
}