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


    //    fe output path
    'fe_api_output_path' => $outputPath . env('CPRO_FE_API_OUTPUT_PATH', '/frontend/src/apis'),
    'fe_module_output_path' => $outputPath . env('CPRO_FE_MODULE_OUTPUT_PATH', '/frontend/src/store/modules'),
    'fe_view_list_output_path' => $outputPath . env('CPRO_FE_VIEW_LIST_OUTPUT_PATH', '/frontend/src/views'),
    'fe_view_form_output_path' => $outputPath . env('CPRO_FE_VIEW_FORM_OUTPUT_PATH', '/frontend/src/views'),
    'fe_type_entity_output_path' => $outputPath . env('CPRO_FE_TYPE_ENTITY_OUTPUT_PATH', '/frontend/src/types/entities'),
    'fe_type_store_output_path' => $outputPath . env('CPRO_FE_TYPE_STORE_OUTPUT_PATH', '/frontend/src/types/stores'),
    'fe_type_body_output_path' => $outputPath . env('CPRO_FE_TYPE_BODY_OUTPUT_PATH', '/frontend/src/types/bodies'),
    'fe_type_filter_body_output_path' => $outputPath . env('CPRO_FE_TYPE_BODY_OUTPUT_PATH', '/frontend/src/types/bodies'),

//    table except
    'table_excepts' => env('CPRO_TABLE_EXCEPTS', 'migrations,failed_jobs,password_resets,personal_access_tokens'),
    'be_stub_path' => "{$stubPath}/be/",
    'fe_stub_path' => "{$stubPath}/fe/",

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
    ],

    'fe_resource_file_map' => [
        'api' => 'api',
        'module' => 'module',
        'type_store' => 'type_store',
        'type_body' => 'type_body',
        'type_filter_body' => 'type_filter_body',
        'type_entity' => 'type_entity',
        'view_list' => 'view_list',
        'view_form' => 'view_form',
    ]
];
