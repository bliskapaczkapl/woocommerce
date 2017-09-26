<?php

function is_json($str)
{
    $json = json_decode($str);
    return $json && $str != $json;
}
