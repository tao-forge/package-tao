<?php

namespace oat\tao\helpers;

/**
 * Class Base64
 *
 * @package oat\tao\helpers
 */
class Base64
{
    /**
     * @param $data
     *
     * @return bool
     */
    public static function isEncodedImage($data): bool
    {
        return is_string($data) && preg_match('/^(data:.*;base64)/', $data) && getimagesize($data) !== false;
    }
}
