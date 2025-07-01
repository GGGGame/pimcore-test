<?php

namespace App\DataObjects\Helper;


class Normalizer
{
    public static function normalize(array $data): array
    {
        return array_map(function($col) {
            $col = trim($col);
            $col = ucwords($col);
            $col = preg_replace('/^\xEF\xBB\xBF/', '', $col); // Remove BOM char
            $col = str_replace([' ', '-'], '', $col);
            return $col;
        }, $data);
    }

    public static function normalizeType(array $data): array
    {
        $numericExcludes = NumericFields::get();
        foreach ($data as $key => $value) {
            if (!in_array($key, $numericExcludes)) {
                $data[$key] = intval($value);
            }
        }

        return $data;
    }
}