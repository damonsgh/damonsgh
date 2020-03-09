<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::get('registerform',[  
//     'uses' => 'Controller@registerForm',
//   ], function () {
//      header('Access-Control-Allow-Origin: *');
//      header('Access-Control-Allow-Headers: Origin, Content-Type');
//   }); //product content

// Route::post('admin/login','Controller@adminLogin')->name('adminLogin');
// Route::post('admin/logout','Controller@adminLogout')->name('adminLogout');

Route::post('user-admin/login','UserController@secuserLogin')->name('secuserlogin');
Route::post('user-admin/logout','UserController@secuserLogout')->name('UserLogout');

Route::post('superadmin/login','Controller@superAdminLogin')->name('UserLogin');

Route::middleware('auth:api')->group( function () {

        Route::middleware('CheckRole:admin')->group( function () {

                Route::post('admin/dashboard','Controller@adminDashboardData');
                
                Route::post('admin/tabledata','Controller@userTableDetails')->name('userTableDetails');
                Route::post('admin/company','Controller@companyTableDetails')->name('companyTableDetails');

                Route::post('admin/update/company','Controller@updateComp')->name('updateComp');
                Route::post('admin/update/user','Controller@updateUser')->name('updateUser');
                Route::post('admin/register/user','Controller@register_user')->name('register_user');
                Route::post('admin/register/company','Controller@register_company')->name('register_company');
                Route::post('admin/delete/company','Controller@deleteCompany')->name('deleteCompany');
                Route::post('admin/delete/user','Controller@deleteUser')->name('deleteUser');

                Route::post('admin/view/CompanyAccess','Controller@companyAccesstable');
                Route::post('admin/update/CompanyAccess','Controller@updateCompanyAccess');
                Route::post('admin/delete/CompanyAccess','Controller@deleteCompanyAccess');
                Route::post('admin/companyList','Controller@companyList');
                
                Route::post('admin/currencyreg','Controller@regCurrency');
                Route::post('admin/currencylist','Controller@currencyList');
        });


        Route::middleware('CheckRole:user')->group( function () {
            
                
                Route::post('user/dashboard','UserController@dashboardData');
                
                Route::post('user/view/chains','UserController@viewChains');
                Route::post('user/add/chain','UserController@addChain')->name('secuser');
                Route::post('user/delete/chain','UserController@deleteChain')->name('secuser');
                Route::post('user/update/chain','UserController@updateChain')->name('secuser');
                
                Route::post('user/view/locations','UserController@viewLocations');
                Route::post('user/add/location','UserController@addLocation')->name('secuser');
                Route::post('user/delete/location','UserController@deleteLocation')->name('secuser');
                Route::post('user/update/location','UserController@updateLocation')->name('secuser');
                
                Route::post('user/view/localusers','UserController@viewLocalUsers');
                Route::post('user/add/user','UserController@addLocalUser')->name('secuser');
                Route::post('user/delete/user','UserController@deleteLocalUser')->name('secuser');
                Route::post('user/update/user','UserController@updateLocalUser')->name('secuser');
                
                Route::post('user/view/costcenteraccess','UserController@costCenterAccessView');
                Route::post('user/add/costcenteraccess','UserController@addCostCenterAccessView');
                Route::post('user/update/costcenteraccess','UserController@updateCostCenterAccess')->name('secuser');
                Route::post('user/delete/costcenteraccess','UserController@deleteCostCenterAccess')->name('secuser');
                
                Route::post('user/chainlist','UserController@chainAndLocationList');
                Route::post('user/locationlist','UserCOntroller@locationList');
        });
        
       
});
 Route::post('userdata','UserDetailsController@userdata')->name('userdata');
        Route::post('all_data','UserDetailsController@all_data')->name('all_data');
        Route::post('userdataonchange','UserDetailsController@userdataonchange')->name('userdataonchange');
        Route::post('all_dataonchange','UserDetailsController@all_dataonchange')->name('all_dataonchange');
  