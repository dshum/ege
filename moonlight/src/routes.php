<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Moonlight\Middleware\GuestMiddleware;
use Moonlight\Middleware\AuthMiddleware;
use Moonlight\Middleware\HistoryMiddleware;
use Moonlight\Main\LoggedUser;
use Moonlight\Main\Element;

Route::group(['prefix' => 'moonlight'], function() {
    
    Route::group(['middleware' => [
        StartSession::class, 
        GuestMiddleware::class, 
        VerifyCsrfToken::class
    ]], function () {
        Route::get('/login', ['as' => 'moonlight.login', 'uses' => 'Moonlight\Controllers\LoginController@show']);
        
        Route::post('/login', ['as' => 'moonlight.login', 'uses' => 'Moonlight\Controllers\LoginController@login']);
    });
    
    Route::group(['middleware' => [
        StartSession::class, 
        AuthMiddleware::class,
        VerifyCsrfToken::class,
    ]], function () {
        Route::get('/', ['as' => 'moonlight.home', 'uses' => 'Moonlight\Controllers\HomeController@show']);

        Route::get('/logout', ['as' => 'moonlight.logout', 'uses' => 'Moonlight\Controllers\LoginController@logout']);
       
        Route::get('/profile', ['as' => 'moonlight.profile', 'uses' => 'Moonlight\Controllers\ProfileController@show']);
       
        Route::post('/profile', ['as' => 'moonlight.profile', 'uses' => 'Moonlight\Controllers\ProfileController@save']);
        
        Route::get('/password', ['as' => 'moonlight.password', 'uses' => 'Moonlight\Controllers\PasswordController@show']);

        Route::get('/restore', ['as' => 'moonlight.restore', 'uses' => 'Moonlight\Controllers\PasswordController@restore']);
        
        Route::post('/password', ['as' => 'moonlight.password', 'uses' => 'Moonlight\Controllers\PasswordController@save']);

        Route::get('/users', ['as' => 'moonlight.users', 'uses' => 'Moonlight\Controllers\UserController@users']);
        
        Route::get('/users/create', ['as' => 'moonlight.user.create', 'uses' => 'Moonlight\Controllers\UserController@create']);
        
        Route::post('/users/create', ['as' => 'moonlight.user.add', 'uses' => 'Moonlight\Controllers\UserController@add']);
        
        Route::get('/users/{id}', ['as' => 'moonlight.user', 'uses' => 'Moonlight\Controllers\UserController@edit'])->
            where(['id' => '[0-9]+']);
        
        Route::post('/users/{id}', ['as' => 'moonlight.user.save', 'uses' => 'Moonlight\Controllers\UserController@save'])->
            where(['id' => '[0-9]+']);
        
        Route::post('/users/{id}/delete', ['as' => 'moonlight.user.delete', 'uses' => 'Moonlight\Controllers\UserController@delete'])->
            where(['id' => '[0-9]+']);

        Route::get('/groups', ['as' => 'moonlight.groups', 'uses' => 'Moonlight\Controllers\GroupController@groups']);
        
        Route::get('/groups/create', ['as' => 'moonlight.group.create', 'uses' => 'Moonlight\Controllers\GroupController@create']);
        
        Route::post('/groups/create', ['as' => 'moonlight.group.add', 'uses' => 'Moonlight\Controllers\GroupController@add']);
        
        Route::get('/groups/{id}', ['as' => 'moonlight.group', 'uses' => 'Moonlight\Controllers\GroupController@edit'])->
            where(['id' => '[0-9]+']);
        
        Route::post('/groups/{id}', ['as' => 'moonlight.group.save', 'uses' => 'Moonlight\Controllers\GroupController@save'])->
            where(['id' => '[0-9]+']);
        
        Route::post('/groups/{id}/delete', ['as' => 'moonlight.group.delete', 'uses' => 'Moonlight\Controllers\GroupController@delete'])->
            where(['id' => '[0-9]+']);
        
        Route::get('groups/permissions/items/{id}', ['as' => 'moonlight.group.items', 'uses' => 'Moonlight\Controllers\PermissionController@itemPermissions'])->
            where('id', '[0-9]+');
        
        Route::post('groups/permissions/items/{id}', ['as' => 'moonlight.group.items', 'uses' => 'Moonlight\Controllers\PermissionController@saveItemPermission'])->
            where('id', '[0-9]+');
        
        Route::get('groups/permissions/elements/{id}/{class}', ['as' => 'moonlight.group.elements', 'uses' => 'Moonlight\Controllers\PermissionController@elementPermissions'])->
            where('id', '[0-9]+');
        
        Route::post('groups/permissions/elements/{id}/{class}', ['as' => 'moonlight.group.elements', 'uses' => 'Moonlight\Controllers\PermissionController@saveElementPermission'])->
            where('id', '[0-9]+'); 
        
        Route::get('/log', ['as' => 'moonlight.log', 'uses' => 'Moonlight\Controllers\LogController@show']);
        
        Route::get('/log/next', ['as' => 'moonlight.log.next', 'uses' => 'Moonlight\Controllers\LogController@next']);
        
        Route::get('/search', ['as' => 'moonlight.search', 'uses' => 'Moonlight\Controllers\SearchController@index']);

        Route::post('/search/active/{class}/{name}', ['as' => 'moonlight.search.active', 'uses' => 'Moonlight\Controllers\SearchController@active']); 
        
        Route::get('/search/list', ['as' => 'moonlight.search.list', 'uses' => 'Moonlight\Controllers\SearchController@elements']);

        Route::post('search/sort', ['as' => 'moonlight.search.sort', 'uses' => 'Moonlight\Controllers\SearchController@sort']);
        
        Route::get('/trash', ['as' => 'moonlight.trash', 'uses' => 'Moonlight\Controllers\TrashController@index']);
        
        Route::get('/trash/count', ['as' => 'moonlight.trash.count', 'uses' => 'Moonlight\Controllers\TrashController@count']);
        
        Route::get('/trash/list', ['as' => 'moonlight.trash.list', 'uses' => 'Moonlight\Controllers\TrashController@elements']);
        
        Route::get('/trash/{item}', ['as' => 'moonlight.trash.item', 'uses' => 'Moonlight\Controllers\TrashController@item'])->
            where(['item' => '[A-Za-z0-9\.]+']);
        
        Route::get('/elements/list', ['as' => 'moonlight.elements.list', 'uses' => 'Moonlight\Controllers\BrowseController@elements']);
        
        Route::post('/elements/open', ['as' => 'moonlight.elements.open', 'uses' => 'Moonlight\Controllers\BrowseController@open']);

        Route::post('/elements/close', ['as' => 'moonlight.elements.close', 'uses' => 'Moonlight\Controllers\BrowseController@close']);
        
        Route::get('/elements/autocomplete', ['as' => 'moonlight.elements.autocomplete', 'uses' => 'Moonlight\Controllers\BrowseController@autocomplete']);
        
        Route::get('/elements/favorites', ['as' => 'moonlight.home.favorites', 'uses' => 'Moonlight\Controllers\HomeController@favorites']);
        
        Route::post('/elements/favorite', ['as' => 'moonlight.home.favorite', 'uses' => 'Moonlight\Controllers\HomeController@favorite']);
        
        Route::post('/elements/copy', ['as' => 'moonlight.elements.copy', 'uses' => 'Moonlight\Controllers\BrowseController@copy']);
        
        Route::post('/elements/move', ['as' => 'moonlight.elements.move', 'uses' => 'Moonlight\Controllers\BrowseController@move']);
        
        Route::post('/elements/delete', ['as' => 'moonlight.elements.delete', 'uses' => 'Moonlight\Controllers\BrowseController@delete']);
        
        Route::post('/elements/delete/force', ['as' => 'moonlight.elements.delete.force', 'uses' => 'Moonlight\Controllers\BrowseController@forceDelete']);
        
        Route::post('/elements/restore', ['as' => 'moonlight.elements.restore', 'uses' => 'Moonlight\Controllers\BrowseController@restore']);

        Route::get('/browse/{classId}/{item}/create', ['as' => 'moonlight.element.create', 'uses' => 'Moonlight\Controllers\EditController@create'])->
            where(['classId' => '[A-Za-z0-9\.]+', 'item' => '[A-Za-z0-9\.]+']);

        Route::get('/browse/{classId}/edit', ['as' => 'moonlight.element.edit', 'uses' => 'Moonlight\Controllers\EditController@edit'])->
            where(['classId' => '[A-Za-z0-9\.]+']);
        
        Route::post('/browse/{item}/add', ['as' => 'moonlight.element.add', 'uses' => 'Moonlight\Controllers\EditController@add'])->
            where(['item' => '[A-Za-z0-9\.]+']);
        
        Route::post('/browse/{classId}/save', ['as' => 'moonlight.element.save', 'uses' => 'Moonlight\Controllers\EditController@save'])->
            where(['classId' => '[A-Za-z0-9\.]+']);
        
        Route::post('/browse/{classId}/copy', ['as' => 'moonlight.element.copy', 'uses' => 'Moonlight\Controllers\EditController@copy'])->
            where(['classId' => '[A-Za-z0-9\.]+']);
        
        Route::post('/browse/{classId}/move', ['as' => 'moonlight.element.move', 'uses' => 'Moonlight\Controllers\EditController@move'])->
            where(['classId' => '[A-Za-z0-9\.]+']);
        
        Route::post('/browse/{classId}/delete', ['as' => 'moonlight.element.delete', 'uses' => 'Moonlight\Controllers\EditController@delete'])->
            where(['classId' => '[A-Za-z0-9\.]+']);
        
        Route::post('/browse/{classId}/plugin/{method}', ['as' => 'moonlight.browse.plugin', 'uses' => 'Moonlight\Controllers\BrowseController@plugin'])->
            where(['classId' => '[A-Za-z0-9\.]+', 'method' => '[A-Za-z0-9]+']);
        
        Route::post('/order', ['as' => 'moonlight.order', 'uses' => 'Moonlight\Controllers\BrowseController@order']);
        
        Route::group(['middleware' => [HistoryMiddleware::class]], function () {
            Route::get('/search/{item}', ['as' => 'moonlight.search.item', 'uses' => 'Moonlight\Controllers\SearchController@item'])->
                where(['item' => '[A-Za-z0-9\.]+']);
            
            Route::get('/browse', ['as' => 'moonlight.browse', 'uses' => 'Moonlight\Controllers\BrowseController@root']);
        
            Route::get('/browse/root', ['as' => 'moonlight.browse.root', 'uses' => 'Moonlight\Controllers\BrowseController@root']);
            
            Route::get('/browse/{classId}', ['as' => 'moonlight.browse.element', 'uses' => 'Moonlight\Controllers\BrowseController@element'])->
                where(['classId' => '[A-Za-z0-9\.]+']);
        });
    });
});

Route::group(['middleware' => [
    StartSession::class, 
    AuthMiddleware::class,
    VerifyCsrfToken::class,
]], function () {
    Route::post('/plugins/answers/{id}', ['uses' => '\App\Http\Plugins\Answers@correct']);
});