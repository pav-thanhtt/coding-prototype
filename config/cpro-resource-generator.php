<?php

$outputPath = env('CPRO_OUTPUT_PATH', 'storage/allu');
$stubPath = env('CPRO_STUB_DIR', 'stubs/api-resource-generator');

return [
//    be output path
    'be_factory_output_path' => $outputPath . env('CPRO_BE_FACTORY_OUTPUT_PATH', '/backend/database/factories'),
    'be_seeder_output_path' => $outputPath . env('CPRO_BE_SEEDER_OUTPUT_PATH', '/backend/database/seeders'),
    'be_model_output_path' => $outputPath . env('CPRO_BE_MODEL_OUTPUT_PATH', '/backend/app/Models'),
    'be_repository_output_path' => $outputPath . env('CPRO_BE_REPOSITORY_OUTPUT_PATH', '/backend/app/Repositories'),
    'be_service_output_path' => $outputPath . env('CPRO_BE_SERVICE_OUTPUT_PATH', '/backend/app/Services/Api'),
    'be_controller_output_path' => $outputPath . env('CPRO_BE_CONTROLLER_OUTPUT_PATH', '/backend/app/Http/Controllers/Api/V1'),
    'be_request_output_path' => $outputPath . env('CPRO_BE_REQUEST_OUTPUT_PATH', '/backend/app/Http/Requests/Api'),
    'be_resource_output_path' => $outputPath . env('CPRO_BE_RESOURCE_OUTPUT_PATH', '/backend/app/Http/Resources/Api'),
    'be_filter_output_path' => $outputPath . env('CPRO_BE_FILTER_OUTPUT_PATH', '/backend/app/ModelFilters'),

//    table except
    'table_excepts' => env('CPRO_TABLE_EXCEPTS', 'migrations,failed_jobs,password_resets,personal_access_tokens'),
    'be_stub_path' => "{$stubPath}/be/",

    'factory_limit_default' => 10,

    'be_resource_file_map' => [
        'controller' => 'controller',
        'resource' => 'resource',
        'service' => 'service',
        'search_request' => 'request',
        'store_request' => 'request',
        'update_request' => 'request',
        'filter' => 'filter',
    ],

    'rmsf_resource_file_map' => [
        'repository' => 'repository',
        'model' => 'model',
        'seeder' => 'seeder',
        'factory' => 'factory',
    ]
];
