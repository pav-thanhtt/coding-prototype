<?php

namespace App\Common;


class ExcelStyleCommon
{
    const STYLE_API_SPEC = [
        'title' => [

            'font' => [
                'bold' => true,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],

            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => [
                    'argb' => 'FFC9DAF8'
                ]
            ]

        ],

        'default' => [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ],

        'text_red' => [
            'font' => [
                'italic' => true,
                'color' =>[
                    'argb' => 'FFff0000'
                ]
            ]

        ]


    ];

}