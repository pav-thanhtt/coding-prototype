<?php

return [
//    output path
    'output_path' => env('CPRO_OUTPUT_PATH', 'storage'),
    'factory_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_FACTORY_OUTPUT_PATH', '/allu/factories'),
    'seeder_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_SEEDER_OUTPUT_PATH', '/allu/seeders'),
    'model_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_MODEL_OUTPUT_PATH', '/allu/Models'),
    'repository_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_REPOSITORY_OUTPUT_PATH', '/allu/Repositories'),
    'service_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_SERVICE_OUTPUT_PATH', '/allu/Services'),
    'controller_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_CONTROLLER_OUTPUT_PATH', '/allu/Controllers'),
    'request_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_REQUEST_OUTPUT_PATH', '/allu/Requests'),
    'resource_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_RESOURCE_OUTPUT_PATH', '/allu/Resources'),
    'filter_output_path' => env('CPRO_OUTPUT_PATH', 'storage') . env('CPRO_FILTER_OUTPUT_PATH', '/allu/ModelFilters'),

//    table except
    'table_excepts' => env('CPRO_TABLE_EXCEPTS', 'migrations,failed_jobs,password_resets,personal_access_tokens'),
    'stub_path' => env('CPRO_STUB_DIR', 'stubs/api-resource-generator/'),

    'factory_limit_default' => 10,

    'resource_file_map' => [
        'controller' => 'controller',
        'factory' => 'factory',
        'model' => 'model',
        'repository' => 'repository',
        'resource' => 'resource',
        'seeder' => 'seeder',
        'service' => 'service',
        'search_request' => 'request',
        'store_request' => 'request',
        'update_request' => 'request',
        'filter' => 'filter',
    ]
];
