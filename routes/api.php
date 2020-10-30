<?php

use Illuminate\Http\Request;
Route::get('shella', function() {
    $output = [];
    \Artisan::call('config:clear', $output);
    \Artisan::call('cache:clear', $output);
    var_dump($output);
});
Route::middleware('cors')->prefix('v1')->group(function() {
    Route::prefix('auth')->group(function() {
        Route::post('login', 'Api\AuthController@login')->name('api.login');
        Route::post('signup', 'Api\AuthController@signup')->name('api.signup');
        Route::get('signup/activate/{token}', 'Api\AuthController@signupActivate')->name('api.signup.activate');
        Route::POST('signup/request/confirmation-email', 'Api\AuthController@requestConfirmationEmail')->name('api.request.email');

        Route::middleware('auth:api')->group(function() {
            Route::get('logout', 'Api\AuthController@logout')->name('api.logout');
            Route::get('user', 'Api\AuthController@user');
            Route::post('password/change', 'Api\AuthController@changePassword');
        });
    });

    Route::middleware('api')->prefix('password')->group(function () {    
        Route::post('request', 'Api\PasswordResetController@create');
        Route::get('find/{token}', 'Api\PasswordResetController@find');
        Route::post('reset', 'Api\PasswordResetController@reset');
    });
    /**
     * Property Routes that don't need authentication.
     */
    Route::get('properties/search', 'Api\v1\PropertyController@search')->name('properties.search');
    Route::get('properties/{property}', 'Api\v1\PropertyController@show')->name('properties.show');
    Route::get('properties', 'Api\v1\PropertyController@index')->name('properties.index');
    Route::get('promotion/properties', 'Api\v1\PromotionController@getRandomProperties')->name('promotion.random.properties');

    /**
     * Blog routes that require need authentication.
     */
    Route::get('blog/search', 'Api\v1\BlogController@search')->name('blog.search');
    Route::get('blog/{blog}', 'Api\v1\BlogController@show')->name('blog.show');
    Route::get('blog', 'Api\v1\BlogController@index')->name('blog.index');

    /**
     * Profile routes that don't require authentication.
     */
    Route::get('profile', 'Api\v1\ProfileController@showProfile')->name('profile.show');

    Route::middleware('auth:api')->group(function() {
        Route::post('profile/image', 'API\v1\ProfileController@updateImage')->name('profile.update.image');
        Route::post('profile', 'Api\v1\ProfileController@updateProfile')->name('profile.update');

        // Properties API endpoints.
        Route::get('profile/properties', 'Api\v1\PropertyController@showMyProperties')->name('properties.profile.show');
        Route::post('properties', 'Api\v1\PropertyController@create')->name('properties.create');
        Route::put('properties/{property}', 'Api\v1\PropertyController@update')->name('properties.update');
        Route::get('properties/{property}/delete', 'Api\v1\PropertyController@destroy')->name('properties.destroy');
        Route::get('properties/{property}/close', 'Api\v1\PropertyController@close')->name('properties.close');
        Route::get('properties/{property}/open', 'Api\v1\PropertyController@open')->name('properties.open');

        // Comments API endpoints.
        Route::post('comments', 'Api\v1\CommentController@create')->name('comments.create');
        Route::get('comments/{comment}/delete', 'Api\v1\CommentController@destroy')->name('comments.destroy');

        // Like and Unlike endpoints.
        Route::get('properties/{property}/like', 'Api\v1\PropertyController@like')->name('property.like');
        Route::get('properties/{property}/unlike', 'Api\v1\PropertyController@unlike')->name('property.unlike');

        // Blog API endpoints.
        Route::post('blog', 'Api\v1\BlogController@create')->name('blog.create');
        Route::put('blog/{blog}', 'Api\v1\BlogController@update')->name('blog.update');
        Route::get('blog/{blog}/delete', 'Api\v1\BlogCOntroller@destroy')->name('blog.destroy');

        // Subscribe to a promotion plan
        Route::get('promotion/subscription/details', 'Api\v1\PromotionController@showSubscriptionDetails')->name('subscription.details');
        Route::get('promotion/account/properties', 'Api\v1\PromotionController@showMyPromotedProperties')->name('show.my.promoted.properties');
        Route::post('promotion/views', 'Api\v1\PromotionController@updateViewCount')->name('promotion.update.view.count');
        Route::get('promotion/properties/one', 'Api\v1\PromotionController@getRandomProperty')->name('promotion.random.propery');
        Route::post('promotion/subscribe', 'Api\v1\PromotionController@subscribe')->name('promotion.subscribe');
    });

    Route::get('tags/search', 'Api\v1\TagController@search')->name('tags.search');

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });
});