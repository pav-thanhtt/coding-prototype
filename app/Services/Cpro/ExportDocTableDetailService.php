<?php

namespace App\Services\Cpro;

use Illuminate\Support\Facades\Schema;

class ExportDocTableDetailService extends ExportDocTableBaseService
{
    public $data;
    protected $filename;
    protected $sheetTitle;
    
    private $prefix = 'lbl_';

    public function __construct(string $connection, string $table)
    {
        $this->getData($connection, $table);
        $this->filename = $table . '-detail';
        $this->sheetTitle = ucfirst($table) . ' Definition Detail';
    }

    private function getData(string $connection, string $table): void
    {
        $columns = Schema::connection($connection)->getColumnListing($table);
        $data = [];
        
        foreach($columns as $key => $column) {
            $type = Schema::connection($connection)->getColumnType($table, $column);
            // label
            $data[] = [
                'no' => $key+1,
                'section' => 'main', // TODO
                'name' => $this->prefix . $column,
                'type' => 'text', // fixed
                'io' => 'O',
                'static_value' => null,
                'dynamic_value' => null,
                'show_hide' => 'Show', // TODO
                'place_holder' => null,
                'valid_input' => null,
                'action' => null,
                'logic_id' => null,
                'comments' => null
            ];

            // input
            $data[] = [
                'no' => $key+1,
                'section' => 'main', // TODO
                'name' => $column,
                'type' => $this->convertColumnTypeToInputType($type),
                'io' => 'I',
                'static_value' => null,
                'dynamic_value' => $table . '.' . $column,
                'show_hide' => 'Show', // TODO
                'place_holder' => null,
                'valid_input' => null,
                'action' => null,
                'logic_id' => null,
                'comments' => null
            ];
        }

        $this->data = $this->appendData($data);
    }

    public function appendData(array $listData): array
    {
        $max = count($listData);
        $listData[] = [
            'no' => $max+1,
            'section' => 'main', // TODO
            'name' => 'btn_save', // fixed
            'type' => 'button', // fixed
            'io' => 'I',
            'static_value' => 'Save', // fixed
            'dynamic_value' => null,
            'show_hide' => 'Show', // TODO
            'place_holder' => null,
            'valid_input' => null,
            'action' => null,
            'logic_id' => null,
            'comments' => null
        ];

        $listData[] = [
            'no' => $max+2,
            'section' => 'main', // TODO
            'name' => 'btn_back', // fixed
            'type' => 'button', // fixed
            'io' => 'I',
            'static_value' => 'Back', // fixed
            'dynamic_value' => null,
            'show_hide' => 'Show', // TODO
            'place_holder' => null,
            'valid_input' => null,
            'action' => null,
            'logic_id' => null,
            'comments' => null
        ];

        return $listData;
    }

    private function convertColumnTypeToInputType(string $columnType)
    {
        switch ($columnType) {
            case 'datetime':
                return 'datepicker';
            default:
                return 'textbox';
        }
    }
}