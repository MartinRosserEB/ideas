<?php

namespace App\Service;

use App\Entity\Collection;
use App\Entity\User;

/**
 * @author mrosser
 */
class Access {
    public function checkAccess(User $user, Collection $collection, $checkAdmin = false)
    {
        $accessGranted = false;

        $userCollections = $user->getUserCollections()->filter(
            function ($entry) use ($collection)
            {
                return $entry->getCollection() === $collection;
            }
        );

        if (count($userCollections) == 1) {
             if ($checkAdmin === false) {
                $accessGranted = true;
             } elseif ($userCollections->first()->getRole() === 'Admin') {
                $accessGranted = true;
             }
        }

        return $accessGranted;
    }
}
