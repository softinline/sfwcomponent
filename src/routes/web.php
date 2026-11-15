<?php

use Illuminate\Support\Facades\Route;

Route::prefix('sfw')->middleware(['web'])->group(function () {
    Route::get('auth/login', 'Softinline\SfwComponent\Controllers\AuthController@loginGet');
    Route::post('auth/login', 'Softinline\SfwComponent\Controllers\AuthController@loginPost');
    Route::get('auth/logoff', 'Softinline\SfwComponent\Controllers\AuthController@logoff');
});

Route::prefix('sfw')->middleware(['web', 'SfwProtected'])->group(function () {
    Route::get('setup', 'Softinline\SfwComponent\Controllers\SetupController@index');
    Route::post('setup', 'Softinline\SfwComponent\Controllers\SetupController@store');

    Route::post('datatable/config-columns-save', 'Softinline\SfwComponent\Controllers\DatatableController@configColumnsSave');

    Route::get('/', 'Softinline\SfwComponent\Controllers\HomeController@index');

    Route::get('smtps', 'Softinline\SfwComponent\Controllers\SmtpsController@index');
    Route::get('smtps/data', 'Softinline\SfwComponent\Controllers\SmtpsController@data');
    Route::get('smtps/add', 'Softinline\SfwComponent\Controllers\SmtpsController@add');
    Route::post('smtps/add', 'Softinline\SfwComponent\Controllers\SmtpsController@create');
    Route::get('smtps/dependend1', 'Softinline\SfwComponent\Controllers\SmtpsController@selectDependend1');
    Route::get('smtps/dependend2', 'Softinline\SfwComponent\Controllers\SmtpsController@selectDependend2');
    Route::get('smtps/{id}', 'Softinline\SfwComponent\Controllers\SmtpsController@edit');
    Route::post('smtps/{id}', 'Softinline\SfwComponent\Controllers\SmtpsController@update');
    Route::delete('smtps/{id}', 'Softinline\SfwComponent\Controllers\SmtpsController@delete');
    Route::post('smtps/{id}/test-email', 'Softinline\SfwComponent\Controllers\SmtpsController@testEmail');

    Route::get('screens', 'Softinline\SfwComponent\Controllers\ScreensController@index');
    Route::get('screens/data', 'Softinline\SfwComponent\Controllers\ScreensController@data');
    Route::get('screens/add', 'Softinline\SfwComponent\Controllers\ScreensController@add');
    Route::post('screens/add', 'Softinline\SfwComponent\Controllers\ScreensController@create');
    Route::get('screens/import', 'Softinline\SfwComponent\Controllers\ScreensController@import');
    Route::post('screens/import', 'Softinline\SfwComponent\Controllers\ScreensController@importExec');
    Route::get('screens/{id}', 'Softinline\SfwComponent\Controllers\ScreensController@edit');
    Route::post('screens/{id}', 'Softinline\SfwComponent\Controllers\ScreensController@update');
    Route::delete('screens/{id}', 'Softinline\SfwComponent\Controllers\ScreensController@delete');
    Route::get('screens/{id}/detail', 'Softinline\SfwComponent\Controllers\ScreensController@detail');
    Route::post('screens/{id}/detail', 'Softinline\SfwComponent\Controllers\ScreensController@detail');
    Route::post('screens/{id}/order', 'Softinline\SfwComponent\Controllers\ScreensController@order');
    Route::get('screens/{id}/components/add', 'Softinline\SfwComponent\Controllers\ScreensController@componentsAdd');
    Route::post('screens/{id}/components/add', 'Softinline\SfwComponent\Controllers\ScreensController@componentsCreate');
    Route::get('screens/{id}/components/{componentId}/edit', 'Softinline\SfwComponent\Controllers\ScreensController@componentsEdit');
    Route::post('screens/{id}/components/{componentId}/edit', 'Softinline\SfwComponent\Controllers\ScreensController@componentsUpdate');

    Route::get('objects', 'Softinline\SfwComponent\Controllers\ObjectsController@index');
    Route::get('objects/data', 'Softinline\SfwComponent\Controllers\ObjectsController@data');
    Route::get('objects/add', 'Softinline\SfwComponent\Controllers\ObjectsController@add');
    Route::post('objects/add', 'Softinline\SfwComponent\Controllers\ObjectsController@create');
    Route::get('objects/{id}', 'Softinline\SfwComponent\Controllers\ObjectsController@edit');
    Route::post('objects/{id}', 'Softinline\SfwComponent\Controllers\ObjectsController@update');
    Route::delete('objects/{id}', 'Softinline\SfwComponent\Controllers\ObjectsController@delete');

    Route::get('roles', 'Softinline\SfwComponent\Controllers\RolesController@index');
    Route::get('roles/data', 'Softinline\SfwComponent\Controllers\RolesController@data');
    Route::get('roles/add', 'Softinline\SfwComponent\Controllers\RolesController@add');
    Route::post('roles/add', 'Softinline\SfwComponent\Controllers\RolesController@create');
    Route::get('roles/{id}', 'Softinline\SfwComponent\Controllers\RolesController@edit');
    Route::post('roles/{id}', 'Softinline\SfwComponent\Controllers\RolesController@update');
    Route::delete('roles/{id}', 'Softinline\SfwComponent\Controllers\RolesController@delete');

    Route::get('files', 'Softinline\SfwComponent\Controllers\FilesController@index');
    Route::get('files/data', 'Softinline\SfwComponent\Controllers\FilesController@data');
    Route::get('files/{id}', 'Softinline\SfwComponent\Controllers\FilesController@detail');

    Route::get('languages', 'Softinline\SfwComponent\Controllers\LanguagesController@index');
    Route::get('languages/data', 'Softinline\SfwComponent\Controllers\LanguagesController@data');
    Route::get('languages/add', 'Softinline\SfwComponent\Controllers\LanguagesController@add');
    Route::post('languages/add', 'Softinline\SfwComponent\Controllers\LanguagesController@create');
    Route::get('languages/{id}', 'Softinline\SfwComponent\Controllers\LanguagesController@edit');
    Route::post('languages/{id}', 'Softinline\SfwComponent\Controllers\LanguagesController@update');
    Route::delete('languages/{id}', 'Softinline\SfwComponent\Controllers\LanguagesController@delete');

    Route::get('tasks', 'Softinline\SfwComponent\Controllers\TasksController@index');
    Route::get('tasks/data', 'Softinline\SfwComponent\Controllers\TasksController@data');
    Route::get('tasks/add', 'Softinline\SfwComponent\Controllers\TasksController@add');
    Route::post('tasks/add', 'Softinline\SfwComponent\Controllers\TasksController@create');
    Route::get('tasks/{id}', 'Softinline\SfwComponent\Controllers\TasksController@edit');
    Route::post('tasks/{id}', 'Softinline\SfwComponent\Controllers\TasksController@update');
    Route::delete('tasks/{id}', 'Softinline\SfwComponent\Controllers\TasksController@delete');

    Route::get('translations', 'Softinline\SfwComponent\Controllers\TranslationsController@index');
    Route::get('translations/data', 'Softinline\SfwComponent\Controllers\TranslationsController@data');
    Route::get('translations/{id}', 'Softinline\SfwComponent\Controllers\TranslationsController@edit');
    Route::post('translations/{id}', 'Softinline\SfwComponent\Controllers\TranslationsController@update');
    Route::delete('translations/{id}', 'Softinline\SfwComponent\Controllers\TranslationsController@delete');

    Route::get('audits', 'Softinline\SfwComponent\Controllers\AuditsController@index');
    Route::get('audits/data', 'Softinline\SfwComponent\Controllers\AuditsController@data');
    Route::get('audits/{id}', 'Softinline\SfwComponent\Controllers\AuditsController@edit');

    Route::get('email-templates', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@index');
    Route::get('email-templates/data', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@data');
    Route::get('email-templates/add', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@add');
    Route::post('email-templates/add', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@create');
    Route::get('email-templates/{id}', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@edit');
    Route::post('email-templates/{id}', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@update');
    Route::delete('email-templates/{id}', 'Softinline\SfwComponent\Controllers\EmailTemplatesController@delete');

    Route::get('email-logs', 'Softinline\SfwComponent\Controllers\EmailLogsController@index');
    Route::get('email-logs/data', 'Softinline\SfwComponent\Controllers\EmailLogsController@data');

    Route::get('tokens', 'Softinline\SfwComponent\Controllers\TokensController@index');
    Route::get('tokens/data', 'Softinline\SfwComponent\Controllers\TokensController@data');
    Route::get('tokens/{id}/edit', 'Softinline\SfwComponent\Controllers\TokensController@edit');

    Route::get('users', 'Softinline\SfwComponent\Controllers\UsersController@index');
    Route::get('users/data', 'Softinline\SfwComponent\Controllers\UsersController@data');
    Route::get('users/add', 'Softinline\SfwComponent\Controllers\UsersController@add');
    Route::post('users/add', 'Softinline\SfwComponent\Controllers\UsersController@create');
    Route::get('users/{id}/edit', 'Softinline\SfwComponent\Controllers\UsersController@edit');
    Route::get('users/{id}/objects-access', 'Softinline\SfwComponent\Controllers\UsersController@objectsAccess');
    Route::get('users/{id}/objects-access/data', 'Softinline\SfwComponent\Controllers\UsersController@objectsAccessData');
    Route::post('users/{id}/objects-access-exec', 'Softinline\SfwComponent\Controllers\UsersController@objectsAccessExec');
    Route::get('users/{id}/add-all-privileges-objects', 'Softinline\SfwComponent\Controllers\UsersController@addAllPrivilegesObjects');
    Route::get('users/{id}', 'Softinline\SfwComponent\Controllers\UsersController@edit');
    Route::post('users/{id}', 'Softinline\SfwComponent\Controllers\UsersController@update');
    Route::delete('users/{id}', 'Softinline\SfwComponent\Controllers\UsersController@delete');
});