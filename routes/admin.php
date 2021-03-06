<?php

    Route::GET('/home', 'AdminController@index')->name('admin.home');
    // Login and Logout
    Route::GET('/', 'LoginController@showLoginForm')->name('admin.login');
    Route::POST('/', 'LoginController@login');
    Route::POST('/logout', 'LoginController@logout')->name('admin.logout');

    // Password Resets
    Route::POST('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::GET('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::POST('/password/reset', 'ResetPasswordController@reset');
    Route::GET('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::GET('/password/change', 'AdminController@showChangePasswordForm')->name('admin.password.change');
    // Route::POST('/password/change', 'AdminController@changePassword');
    Route::POST('/password/change', '\App\Http\Controllers\Admin\AdminController@changePassword')->middleware('auth:admin')->middleware('role:super;restaturaa');
    // Register Admins
    Route::get('/register', 'RegisterController@showRegistrationForm')->name('admin.register');
    Route::post('/register', 'RegisterController@register');
    Route::get('/{admin}/edit', 'RegisterController@edit')->name('admin.edit');
    Route::delete('/{admin}', 'RegisterController@destroy')->name('admin.delete');
    Route::patch('/{admin}', 'RegisterController@update')->name('admin.update');

    // Admin Lists
    Route::get('/show', 'AdminController@show')->name('admin.show');
    Route::get('/me', 'AdminController@me')->name('admin.me');

    // Admin Roles
    Route::post('/{admin}/role/{role}', 'AdminRoleController@attach')->name('admin.attach.roles');
    Route::delete('/{admin}/role/{role}', 'AdminRoleController@detach');

    // Roles
    Route::get('/roles', 'RoleController@index')->name('admin.roles');
    Route::get('/role/create', 'RoleController@create')->name('admin.role.create');
    Route::post('/role/store', 'RoleController@store')->name('admin.role.store');
    Route::delete('/role/{role}', 'RoleController@destroy')->name('admin.role.delete');
    Route::get('/role/{role}/edit', 'RoleController@edit')->name('admin.role.edit');
    Route::patch('/role/{role}', 'RoleController@update')->name('admin.role.update');

    // active status
    Route::post('activation/{admin}', 'ActivationController@activate')->name('admin.activation');
    Route::delete('activation/{admin}', 'ActivationController@deactivate');
    Route::resource('permission', 'PermissionController');

    Route::fallback(function () {
        return abort(404);
    });

    Route::get('dashboard','\App\Http\Controllers\Admin\DashboardController@fetch')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('maindashboard','\App\Http\Controllers\Admin\DashboardController@maindashboard')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('update/{id}/{status}','\App\Http\Controllers\Admin\DashboardController@update')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('upload','\App\Http\Controllers\Admin\UploadController@index')->middleware('auth:admin')->middleware('role:super;restaturaa');
    
    Route::post('uploadCategory', '\App\Http\Controllers\Admin\UploadController@uploadCategory')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('uploadCategoryItem', '\App\Http\Controllers\Admin\UploadController@uploadCategoryItem')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('uploadHindiCategory', '\App\Http\Controllers\Admin\UploadController@uploadHindiCategory')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('uploadHindiCategoryItems', '\App\Http\Controllers\Admin\UploadController@uploadHindiCategoryItems')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('profile','\App\Http\Controllers\Admin\DashboardController@profile')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('past_orders','\App\Http\Controllers\Admin\DashboardController@past_orders')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('analytics','\App\Http\Controllers\Admin\AnalyticsController@index')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('menu_upload','\App\Http\Controllers\Admin\MenuController@index')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('category','\App\Http\Controllers\Admin\MenuController@add')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('delete','\App\Http\Controllers\Admin\MenuController@delete')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('edit','\App\Http\Controllers\Admin\MenuController@edit')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('settings','\App\Http\Controllers\Admin\DashboardController@settings')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('update_tax','\App\Http\Controllers\Admin\DashboardController@update_settings')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('license','\App\Http\Controllers\Admin\LicenseController@index')->middleware('auth:admin')->middleware('role:super;restaturaa')->name('license');

    Route::post('update_license','\App\Http\Controllers\Admin\LicenseController@update_license')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::get('generatekey','\App\Http\Controllers\Admin\LicenseController@generatekey')->middleware('auth:admin')->middleware('role:super')->name('generatekey');

    Route::post('license_generate','\App\Http\Controllers\Admin\LicenseController@license_generate')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('refresh','\App\Http\Controllers\Admin\DashboardController@refresh')->middleware('auth:admin')->middleware('role:super;restaturaa');

    Route::post('refreshsection','\App\Http\Controllers\Admin\DashboardController@refreshTable')->middleware('auth:admin');

    Route::get('erp','\App\Http\Controllers\Admin\ErpController@index')->middleware('auth:admin')->name('erp');

    Route::post('add_employee','\App\Http\Controllers\Admin\ErpController@add')->middleware('auth:admin');

    Route::get('employees','\App\Http\Controllers\Admin\ErpController@fetch')->middleware('auth:admin');

    Route::get('employees/{id}','\App\Http\Controllers\Admin\ErpController@details')->middleware('auth:admin');

    Route::get('delete/{id}','\App\Http\Controllers\Admin\ErpController@delete')->middleware('auth:admin');

    Route::post('employees/add_leave','\App\Http\Controllers\Admin\ErpController@addLeave')->middleware('auth:admin');

    Route::get('campaign_manager','\App\Http\Controllers\Admin\CampaignManagerController@index')->middleware('auth:admin');

    Route::get('order/{id}','\App\Http\Controllers\Admin\DashboardController@orderId')->middleware('auth:admin');

    Route::get('table/{id}','\App\Http\Controllers\Admin\DashboardController@tableId')->middleware('auth:admin');

    Route::get('menu/{id}','\App\Http\Controllers\Admin\DashboardController@addTableItem')->middleware('auth:admin');

    Route::post('kitchen','\App\Http\Controllers\Admin\DashboardController@updateItems')->middleware('auth:admin');

    Route::post('customize','\App\Http\Controllers\Admin\DashboardController@customize')->middleware('auth:admin');

    Route::get('newtable','\App\Http\Controllers\Admin\DashboardController@setTable')->middleware('auth:admin');

    Route::get('set/{no}','\App\Http\Controllers\Admin\DashboardController@set')->middleware('auth:admin');

    Route::get('confirmitems/{no}','\App\Http\Controllers\Admin\DashboardController@confirmItems')->middleware('auth:admin');

    Route::post('dopayment', '\App\Http\Controllers\Delivery\RazorpayController@dopayment')->name('dopayment')->middleware('auth:admin');

    Route::get('feedback','\App\Http\Controllers\Admin\FeedbackController@index')->middleware('auth:admin');

    Route::get('kot/{no}','\App\Http\Controllers\Admin\DashboardController@getKot')->middleware('auth:admin');

    Route::get('invoice/{no}','\App\Http\Controllers\Admin\DashboardController@getInvoice')->middleware('auth:admin');

    Route::post('markcomplete','\App\Http\Controllers\Admin\DashboardController@markComplete')->middleware('auth:admin');

    Route::get('invoice','\App\Http\Controllers\Admin\DashboardController@invoiceIndex')->middleware('auth:admin');

    Route::get('online_invoice/{id}','\App\Http\Controllers\Admin\DashboardController@onlineInvoice')->middleware('auth:admin');