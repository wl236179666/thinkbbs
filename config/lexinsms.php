<?php

return [
    "UserName" => env('lexinsms.username'),
    "Password" => env('lexinsms.password'),
    "Signature" => "【中联惠众】",
    "Addresses" => array("http://sdk.lx198.com/sdk", "http://101.200.141.210/sdk"),
    "HttpTimeout" => 5,
    "CodeTimeout" => 15,
    "DetectMobile" => 30,
    "DetectAddress" => 30
];
