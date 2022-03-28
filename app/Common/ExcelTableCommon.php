<?php

namespace App\Common;


class ExcelTableCommon
{
    const EXCEL_API_SPEC = [
        "Base" => [
            ["EndPoint", NULL],
            ["Method", NULL],
            ["Header", "Content-Type: application/json\nAuthorization: Beaer {accessToken}\nAccept-Charset: utf-8\nAccept: application/json"]
        ],

        "Request" => [
            ["Request", "Query String", NULL, NULL, NULL, NULL],
            [NULL, "Field", "Type", "M/O", "Default", "Description"]
        ],

        "Request_body" => [
            ["Request body", "Object JSON", NULL, NULL, NULL, NULL],
            [NULL, "Field", "Type", "M/O", "Default", "Description"]
        ],

        "Response" => [
            ["Response", "Object", NULL, NULL, NULL, NULL],
            [NULL, "Field", "Type", "M/O", "Default", "Description"]
        ],

        "Response_code" => [
            ["Response Code", "int", NULL, NULL, NULL],
            [NULL, "Code", "Description",  NULL, NULL, NULL],
            [NULL,"200 Success", NULL,  NULL, NULL, NULL],
            [NULL,"400 Bad Request", "Required fields is invalid or not specify",  NULL, NULL, NULL],
            [NULL,"401 Unauthorized", "Access Token is invalid or not specify",  NULL, NULL, NULL],
            [NULL,"403 Forbidden", "User dont have permission to access this api",  NULL, NULL, NULL],
            [NULL,"404 Not Found", "Page not found | Data not found",  NULL, NULL, NULL],
            [NULL,"422 Unprocessable Entity", "Request entity is correct, but it was unable to process the contained instructions.",  NULL, NULL, NULL],
            [NULL,"500  Internal Server Error", NULL,  NULL, NULL, NULL],
        ],

        "Sample" => [
            [NULL, "Success", "Fail"],
            ["Request Sample", NULL, NULL],
            ["Data", NULL, NULL],
            ["Response Sample", NULL, NULL],

        ]


    ];

}