<?php


namespace App\Integrations;


use App\City;

class AbstractIntegration
{

    /**
     * Returns true if the integration is active and properly configured to work for a specific city
     * @param City $city
     * @return bool
     */
    public static function isActive(City $city): bool
    {
        return false;
    }

}
