<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . "Config/Routes.php")) {
    require SYSTEMPATH . "Config/Routes.php";
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace("App\Controllers");
$routes->setDefaultController("Home");
$routes->setDefaultMethod("index");
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// Cars
$routes->get("/Cars/Brands", "Cars::Brands");
$routes->get("/Cars/Models/(:segment)", 'Cars::Models/$1');
$routes->get("/Cars/Bodies/(:segment)/(:segment)", 'Cars::Bodies/$1/$2');
$routes->get("/Cars/ModelYears/(:segment)/(:segment)/(:segment)", 'Cars::ModelYears/$1/$2/$3');
$routes->get(
    "/Cars/Engines/(:segment)/(:segment)/(:segment)/(:segment)",
    'Cars::Engines/$1/$2/$3/$4'
);
$routes->get(
    "/Cars/Kw/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)",
    'Cars::Kw/$1/$2/$3/$4/$5'
);

// Catalog
$routes->get("/Catalog/PartBrands", "Catalog::PartBrands");
$routes->get("/Catalog/AccesoriesCategories", "Catalog::AccesoriesCategories");
$routes->get("/Catalog/MineralOilCategories", "Catalog::MineralOilCategories");
$routes->get("/Catalog/CampaignList", "Catalog::CampaignList");
$routes->get("/Catalog/PartBrandCategories/(:segment)", 'Catalog::PartBrandCategories/$1');

// Products
$routes->get("/Products/SparePartList", "Products::SparePartList");
$routes->get("/Products/AccesoriesList", "Products::AccesoriesList");
$routes->get("/Products/MineralOilList", "Products::MineralOilList");
$routes->get("/Products/Detail", "Products::Detail");
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
if (file_exists(APPPATH . "Config/" . ENVIRONMENT . "/Routes.php")) {
    require APPPATH . "Config/" . ENVIRONMENT . "/Routes.php";
}
