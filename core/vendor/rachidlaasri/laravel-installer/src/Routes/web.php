<?php

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'namespace' => 'RachidLaasri\LaravelInstaller\Controllers', 'middleware' => ['web', 'install']], function () {
    Route::get('/', [
        'as' => 'welcome',
        'uses' => 'WelcomeController@welcome',
    ]);

    Route::get('environment', [
        'as' => 'environment',
        'uses' => 'EnvironmentController@environmentMenu',
    ]);

    Route::get('environment/wizard', [
        'as' => 'environmentWizard',
        'uses' => 'EnvironmentController@environmentWizard',
    ]);

    Route::post('environment/saveWizard', [
        'as' => 'environmentSaveWizard',
        'uses' => 'EnvironmentController@saveWizard',
    ]);


    Route::get('requirements', [
        'as' => 'requirements',
        'uses' => 'RequirementsController@requirements',
    ]);

    Route::get('permissions', [
        'as' => 'permissions',
        'uses' => 'PermissionsController@permissions',
    ]);

    Route::get('license', [
        'as' => 'license',
        'uses' => 'LicenseController@license',
    ]);

    Route::post('environment/licenseCheck', [
        'as' => 'licenseCheck',
        'uses' => 'LicenseController@licenseCheck',
    ]);


    Route::get('administrator', [
        'as' => 'administrator',
        'uses' => 'AdministratorController@create',
    ]);

    Route::post('administrator', [
        'as' => 'administratorSave',
        'uses' => 'AdministratorController@store',
    ]);

    Route::get('final', [
        'as' => 'final',
        'uses' => 'FinalController@review',
    ]);

    Route::post('final/complete', [
        'as' => 'finalComplete',
        'uses' => 'FinalController@finish',
    ]);
});

