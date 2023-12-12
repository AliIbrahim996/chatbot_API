<?php

class Validation
{
    static function checkUserEmptyData($data): bool
    {
        if (
            !empty($data->email) &&
            !empty($data->password) &&
            !empty($data->fullName) &&
            !empty($data->userRole)
        ) {
            return true;
        } else {
            return false;
        }
    }
}
