<?php

declare(strict_types=1);

namespace JTG\Mark\Util;

class ArrayHelper
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

    public static function stringToMultidimensionalArray(string $str, string $separator, mixed $lastValue = null): array
    {
        if (false === ($position = strpos(haystack: $str, needle: $separator))) {
            if (null === $lastValue) {
                return [$str];
            }

            return [$str => $lastValue];
        }

        $key = substr(string: $str, offset: 0, length: $position);
        $str = substr(string: $str, offset: $position + 1);

        return [
            $key => self::stringToMultidimensionalArray(str: $str, separator: $separator, lastValue: $lastValue)
        ];
    }
}