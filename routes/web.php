<?php

Route::get('/', 'ShopController@showHome');

Auth::routes();

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {

    Route::resource('ingredients', 'IngredientsController');
    Route::resource('ingredient-categories', 'IngredientCategoriesController');
    Route::resource('pizzas', 'PizzasController');
    Route::resource('delivery-personnel', 'DeliveryPersonnelController');

    Route::get('manage-orders', 'OrdersController@masterList')->name('admin.manage-orders');
    Route::post('update-order-status', 'OrdersController@updateStatus')->name('admin.update-order-status');

    Route::get('pizza/{pizza}/ingredients/{size}', 'PizzaIngredientsController@showForm')->name('pizza.ingredients.show');
    Route::patch('pizza/{pizza}/ingredients', 'PizzaIngredientsController@save')->name('pizza.ingredients.save');
});

Route::group(['prefix' => 'shop', 'namespace' => 'Shop'], function () {

    Route::get('/', 'HomeController@showHome')->name('shop.show.home');
    Route::get('build-your-own-pizza', 'CustomPizzaController@showForm')->name('shop.show.custom-pizza-form');

    Route::group(['middleware' => 'guest'], function () {

        Route::get('register', 'CustomRegistrationController@showForm')->name('shop.show.registration');
        Route::post('register', 'CustomRegistrationController@doRegister')->name('shop.do.registration');

        Route::get('login', 'CustomLoginController@showForm')->name('shop.show.login');
        Route::post('login', 'CustomLoginController@doLogin')->name('shop.do.login');

    });

    Route::post('logout', 'CustomLogoutController')->name('shop.do.logout');

    Route::post('cart-add-pizza', 'OrderPizzaController@addToCart')->name('shop.do.cart-add-pizza');
    Route::post('cart-add-custom-pizza', 'CustomPizzaController@addToCart')->name('shop.do.cart-add-custom-pizza');
    Route::post('cart-update-custom-pizza', 'CustomPizzaController@updateOrder')->name('shop.do.cart-update-custom-pizza');

    Route::post('cart-update-quantity', 'CartController@updateQuantity')->name('shop.do.cart-update-quantity');
    Route::post('cart-remove-item', 'CartController@removeItem')->name('shop.do.cart-remove-item');

    Route::post('confirm-order', 'OrderPizzaController@confirmOrder')->name('shop.do.confirm-order');

    Route::get('cart', 'CartController@showCart')->name('shop.show.cart');
});

Route::get('session', function () {
    dd(\MyCart::all());
});
