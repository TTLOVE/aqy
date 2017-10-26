<?php

namespace Utils;

/**
 * Xml
 */
class Xml
{
    /**
        * xml字符串转数组
        *
        * @param $xmlString string xml的字符串
        *
        * @return array
     */
    public static function xmlToArray($xmlString)
    {
        $xmlClass = new \SimpleXMLElement($xmlString);
        return json_decode(json_encode($xmlClass), true);
    }
}

