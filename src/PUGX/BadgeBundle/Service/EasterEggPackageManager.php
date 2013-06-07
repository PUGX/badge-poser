<?php

namespace PUGX\BadgeBundle\Service;

use PUGX\BadgeBundle\Package\Package;

class EasterEggPackageManager extends PackageManager
{
    /**
     * Get the Type of the Downloads (total, monthly or daily), but if badge-poser it returns randomic numbers.
     *
     * @param Package $package
     * @param string  $type
     *
     * @return string
     */
    public function getPackageDownloads(Package $package, $type)
    {
        $statsType = 'get' . ucfirst($type);

        if ($package && ($download = $package->getDownloads()) && $download instanceof \Packagist\Api\Result\Package\Downloads) {

            if ($package->getName() == 'pugx/badge-poser') {
                return rand(10, 19) * pow(10, rand(1, 9));
            }

            return $download->{$statsType}();
        }
    }
}