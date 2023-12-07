<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
 
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//Webservice 
// Set default route for referral/wsdl



$routes->group('referral', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('wsdl', 'ReferralWsController::index');
    $routes->post('wsdl', 'ReferralWsController::index');
});

$routes->group('auth', ['namespace' => 'Myth\Auth\Controllers'], function ($routes) {
    // Authentication routes    
  
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

    // Registration routes
    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister');

    // Forgot password routes
    $routes->get('forgot', 'AuthController::forgotPassword', ['as' => 'forgot']);
    $routes->post('forgot', 'AuthController::attemptForgot');

    // Reset password routes
    $routes->get('reset/(:hash)', 'AuthController::resetPassword/$1', ['as' => 'reset']);
    $routes->post('reset/(:hash)', 'AuthController::attemptReset/$1');
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
