<?php

declare(strict_types=1);

namespace JTG\Mark\Util;

abstract class ArrayHelper
{
    public static function recursiveAssocMerge(array $first, array $second): array
    {
        $merged = $first;

        foreach ($second as $key => $value) {
            $merged[$key] = match (true) {
                is_array($value) && isset($merged[$key]) && is_array($merged[$key]) => self::recursiveAssocMerge($merged[$key], $value),
                default => $value
            };
        }

        return $merged;
    }
}