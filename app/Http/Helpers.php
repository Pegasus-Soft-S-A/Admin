<?php

use Illuminate\Support\Facades\Route;


if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "menu-item-active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

if (!function_exists('areActiveRoutesMenu')) {
    function areActiveRoutesMenu(array $routes, $output = "menu-item-open menu-item-here")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

if (!function_exists('encrypt_openssl')) {
    function encrypt_openssl($msg, $key)
    {
        $key = hash('MD5', $key, TRUE);
        $encryptedMessage = openssl_encrypt($msg, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return base64_encode($encryptedMessage);
    }
}

if (!function_exists('removeDuplicate')) {

    function removeDuplicate($array1, $array2, $array3, $key)
    {
        $tmpArray = $array1;
        foreach ($array1 as $data1k => $data1) {
            foreach ($array2 as $data2) {
                if ($data1[$key] === $data2[$key]) {
                    unset($tmpArray[$data1k]);
                    continue;
                }
            }
            foreach ($array3 as $data3) {
                if ($data1[$key] === $data3[$key]) {
                    unset($tmpArray[$data1k]);
                    continue;
                }
            }
        }
        return $tmpArray;
    }
}
