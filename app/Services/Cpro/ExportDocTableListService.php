<?php

namespace App\Services\Cpro;

use Illuminate\Support\Facades\Schema;

class ExportDocTableListService extends ExportDocTableBaseService
{
    public $data;
    protected $filename;
    protected $sheetTitle;

    public function __construct(string $connection, string $table)
    {
        $this->getData($connection, $table);
        $this->filename = $table . '-list';
        $this->sheetTitle = ucfirst($table) . ' Definition List';
    }

    private function getData(string $connection, string $table): void
    {
        $columns = Schema::connection($connection)->getColumnListing($table);
        $data = [];
        
        foreach($columns as $key => $column) {
            $type = Schema::connection($connection)->getColumnType($table, $column);
            $data[] = [
                'no' => $key+1,
                'section' => 'main', // TODO
                'name' => $column,
                'type' => 'text/label', // fixed // $type,
                'io' => 'O',
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

    private function appendData(array $listData): array
    {
        $max = count($listData);
        $listData[] = [
            'no' => $max+1,
            'section' => 'main', // TODO
            'name' => 'btn_edit', // fixed
            'type' => 'button', // fixed
            'io' => 'I',
            'static_value' => 'Edit', // fixed
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
            'name' => 'btn_delete', // fixed
            'type' => 'button', // fixed
            'io' => 'I',
            'static_value' => 'Delete', // fixed
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
}