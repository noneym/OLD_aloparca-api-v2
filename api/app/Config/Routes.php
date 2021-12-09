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
$routes->get("/Brands", "Cars::Brands");
$routes->get("/Models", "Cars::Models");
$routes->get("/Bodies", "Cars::Bodies");
$routes->get("/ModelYears", "Cars::ModelYears");
$routes->get("/Engines", "Cars::Engines");
$routes->get("/Kw", "Cars::Kw");

// Catalog
$routes->get("/PartBrands", "Catalog::PartBrands");
$routes->get("/AccesoriesCategories", "Catalog::AccesoriesCategories");
$routes->get("/MineralOilCategories", "Catalog::MineralOilCategories");
$routes->get("/CampianList", "Catalog::CampianList");
$routes->get("/CampianList", "Catalog::CampianList");
$routes->get("/PartBrandCategories", "Catalog::PartBrandCategories");

// Products
$routes->get("/SparePartList", "Products::SparePartList");
$routes->get("/AccesoriesList", "Products::AccesoriesList");
$routes->get("/MineralOilList", "Products::MineralOilList");
$routes->get("/Detail", "Products::Detail");
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
