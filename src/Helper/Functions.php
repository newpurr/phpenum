<?php

if (!function_exists('convert_to_camel')) {
    /**
     * @param string $value
     * @return string
     */
    function convert_to_camel(string $value) : string
    {
        $value = strtolower($value);
        
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        
        $value = str_replace(' ', '', $value);
        
        return lcfirst($value);
    }
}
