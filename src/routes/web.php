<?php

    use Illuminate\Support\Facades\Route;

    Route::middleware(['web'])->group(function () {
        // login
        Route::get('sfw/auth/login', 'Softinline\SfwComponent\Controllers\AuthController@login');
        Route::post('sfw/auth/login', 'Softinline\SfwComponent\Controllers\AuthController@login');
        Route::get('sfw/auth/logoff', 'Softinline\SfwComponent\Controllers\AuthController@logoff');
        // datatable configs
        Route::post('sfw/datatable/config-columns-save', 'Softinline\SfwComponent\Controllers\DatatableController@configColumnsSave');
    });
    
    Route::middleware(['web', 'SfwProtected'])->group(function () {
    
        // home
        Route::get('sfw', 'Softinline\SfwComponent\Controllers\HomeController@index');
    
        // smtps
        Route::get('sfw/smtps', 'Softinline\SfwComponent\Controllers\SmtpsController@index');
        Route::get('sfw/smtps/data', 'Softinline\SfwComponent\Controllers\SmtpsController@data');
        Route::get('sfw/smtps/add', 'Softinline\SfwComponent\Controllers\SmtpsController@add');
        Route::post('sfw/smtps/add', 'Softinline\SfwComponent\Controllers\SmtpsController@create');
        Route::get('sfw/smtps/dependend1', 'Softinline\SfwComponent\Controllers\SmtpsController@selectDependend1');
        Route::get('sfw/smtps/dependend2', 'Softinline\SfwComponent\Controllers\SmtpsController@selectDependend2');
        Route::get('sfw/smtps/{id}', 'Softinline\SfwComponent\Controllers\SmtpsController@edit');
        Route::post('sfw/smtps/{id}', 'Softinline\SfwComponent\Controllers\SmtpsController@update');
        Route::delete('sfw/smtps/{id}', 'Softinline\SfwComponent\Controllers\SmtpsController@delete');
        Route::post('sfw/smtps/{id}/test-email', 'Softinline\SfwComponent\Controllers\SmtpsController@testEmail');

        // roles
        Route::get('sfw/roles', 'Softinline\SfwComponent\Controllers\RolesController@index');
        Route::get('sfw/roles/data', 'Softinline\SfwComponent\Controllers\RolesController@data');
        Route::get('sfw/roles/add', 'Softinline\SfwComponent\Controllers\RolesController@add');
        Route::post('sfw/roles/add', 'Softinline\SfwComponent\Controllers\RolesController@create');
        Route::get('sfw/roles/{id}', 'Softinline\SfwComponent\Controllers\RolesController@edit');
        Route::post('sfw/roles/{id}', 'Softinline\SfwComponent\Controllers\RolesController@update');
        Route::delete('sfw/roles/{id}', 'Softinline\SfwComponent\Controllers\RolesController@delete');

        // files
        Route::get('sfw/files', 'Softinline\SfwComponent\Controllers\FilesController@index');
        Route::get('sfw/files/data', 'Softinline\SfwComponent\Controllers\FilesController@data');
        Route::get('sfw/files/add', 'Softinline\SfwComponent\Controllers\FilesController@add');
        Route::post('sfw/files/add', 'Softinline\SfwComponent\Controllers\FilesController@create');
        Route::get('sfw/files/{id}', 'Softinline\SfwComponent\Controllers\FilesController@edit');
        Route::post('sfw/files/{id}', 'Softinline\SfwComponent\Controllers\FilesController@update');
        Route::delete('sfw/files/{id}', 'Softinline\SfwComponent\Controllers\FilesController@delete');

        // languages
        Route::get('sfw/languages', 'Softinline\SfwComponent\Controllers\LanguagesController@index');
        Route::get('sfw/languages/data', 'Softinline\SfwComponent\Controllers\LanguagesController@data');
        Route::get('sfw/languages/add', 'Softinline\SfwComponent\Controllers\LanguagesController@add');
        Route::post('sfw/languages/add', 'Softinline\SfwComponent\Controllers\LanguagesController@create');
        Route::get('sfw/languages/{id}', 'Softinline\SfwComponent\Controllers\LanguagesController@edit');
        Route::post('sfw/languages/{id}', 'Softinline\SfwComponent\Controllers\LanguagesController@update');
        Route::delete('sfw/languages/{id}', 'Softinline\SfwComponent\Controllers\LanguagesController@delete');
        
        // tasks
        Route::get('sfw/tasks', 'Softinline\SfwComponent\Controllers\TasksController@index');
        Route::get('sfw/tasks/data', 'Softinline\SfwComponent\Controllers\TasksController@data');
        Route::get('sfw/tasks/add', 'Softinline\SfwComponent\Controllers\TasksController@add');
        Route::post('sfw/tasks/add', 'Softinline\SfwComponent\Controllers\TasksController@create');
        Route::get('sfw/tasks/{id}', 'Softinline\SfwComponent\Controllers\TasksController@edit');
        Route::post('sfw/tasks/{id}', 'Softinline\SfwComponent\Controllers\TasksController@update');
        Route::delete('sfw/tasks/{id}', 'Softinline\SfwComponent\Controllers\TasksController@delete');

        // translations
        Route::get('sfw/translations', 'Softinline\SfwComponent\Controllers\TranslationsController@index');
        Route::get('sfw/translations/data', 'Softinline\SfwComponent\Controllers\TranslationsController@data');
        Route::get('sfw/translations/{id}', 'Softinline\SfwComponent\Controllers\TranslationsController@edit');
        Route::post('sfw/translations/{id}', 'Softinline\SfwComponent\Controllers\TranslationsController@update');
        Route::delete('sfw/translations/{id}', 'Softinline\SfwComponent\Controllers\TranslationsController@delete');

        // audits
        Route::get('sfw/audits', 'Softinline\SfwComponent\Controllers\AuditsController@index');
        Route::get('sfw/audits/data', 'Softinline\SfwComponent\Controllers\AuditsController@data');    
        Route::get('sfw/audits/{id}', 'Softinline\SfwComponent\Controllers\AuditsController@edit');

        // email templates
        Route::get('sfw/email-templates', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@index');
        Route::get('sfw/email-templates/data', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@data');
        Route::get('sfw/email-templates/add', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@add');
        Route::post('sfw/email-templates/add', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@create');
        Route::get('sfw/email-templates/{id}', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@edit');
        Route::post('sfw/email-templates/{id}', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@update');
        Route::delete('sfw/email-templates/{id}', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@delete');

        // email logs
        Route::get('sfw/email-logs', 'Softinline\SfwComponent\Controllers\EmailLogsController@index');
        Route::get('sfw/email-logs/data', 'Softinline\SfwComponent\Controllers\EmailLogsController@data');
        
    });