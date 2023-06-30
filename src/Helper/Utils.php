<?php

namespace Advastore\Helper;

class Utils
{
    /**
     * Finds the first element in an array or object based on a key-value pair.
     *
     * @param object|array $arrayOrObject The array or object to search in.
     * @param string $key The key to search for.
     * @param mixed $value The value to search for.
     * @return mixed The first found element or false if no element was found.
     */
    public static function findFirst(object|array $arrayOrObject, string $key, mixed $value): mixed
    {
        if (is_array($arrayOrObject)) {
            $filtered = array_filter(
                $arrayOrObject,
                fn($x) => $x[$key] === $value
            );
        } elseif (is_object($arrayOrObject)) {
            $arrayOrObject = json_decode(json_encode($arrayOrObject),true);
            $filtered = array_filter(
                $arrayOrObject,
                fn($x) => $x[$key] === $value
            );
        } else {
            return false;
        }

        if ($filtered) {
            reset($filtered);
            return $filtered[0];
        }

        return false;
    }
}
