<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1aac2bdddc8ff61c8ef51919962f3779
{
    public static $classMap = array (
        'Pest' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'PestJSON' => __DIR__ . '/..' . '/educoder/pest/PestJSON.php',
        'PestXML' => __DIR__ . '/..' . '/educoder/pest/PestXML.php',
        'PestXML_Exception' => __DIR__ . '/..' . '/educoder/pest/PestXML.php',
        'Pest_BadRequest' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_ClientError' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Conflict' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Curl_Exec' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Curl_Init' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Curl_Meta' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Exception' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Forbidden' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Gone' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_InvalidRecord' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Json_Decode' => __DIR__ . '/..' . '/educoder/pest/PestJSON.php',
        'Pest_Json_Encode' => __DIR__ . '/..' . '/educoder/pest/PestJSON.php',
        'Pest_MethodNotAllowed' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_NotFound' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_ServerError' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_Unauthorized' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
        'Pest_UnknownResponse' => __DIR__ . '/..' . '/educoder/pest/Pest.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit1aac2bdddc8ff61c8ef51919962f3779::$classMap;

        }, null, ClassLoader::class);
    }
}
