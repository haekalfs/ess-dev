<?php

// Helpers.php

if (!function_exists('Parse_Data')) {
    function Parse_Data($data, $startTag, $endTag)
    {
        $startPos = strpos($data, $startTag);
        if ($startPos === false) {
            return "";
        }
        $startPos += strlen($startTag);
        $endPos = strpos($data, $endTag, $startPos);
        if ($endPos === false) {
            return "";
        }
        return substr($data, $startPos, $endPos - $startPos);
    }
}
