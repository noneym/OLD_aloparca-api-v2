<?php namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductsModel;

use function PHPUnit\Framework\isNull;

class Products extends ResourceController
{
    use ResponseTrait;

    public function SparePartList(
        $carBrand = "",
        $carModel = "",
        $carBody = "",
        $modelYear = "",
        $engine = "",
        $kw = "",
        $partBrand = "",
        $original = "",
        $mainCategory = "",
        $subCategory = "",
        $page = 0,
        $listType = 0
    ) {
        helper("aloparca");
        $cacheKey =
            "spare_part_list:" .
            $carBrand .
            ":" .
            $carModel .
            ":" .
            $carBody .
            ":" .
            $modelYear .
            ":" .
            $engine .
            ":" .
            $kw .
            ":" .
            $partBrand .
            ":" .
            $original .
            ":" .
            $mainCategory .
            ":" .
            $subCategory .
            ":" .
            $page .
            ":" .
            $listType;
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrParams = [
                "car_brand" => $carBrand,
                "car_model" => $carModel,
                "car_body" => $carBody,
                "model_year" => $modelYear,
                "engine" => $engine,
                "kw" => $kw,
                "part_brand" => $partBrand,
                "original" => $original,
                "main_category" => $mainCategory,
                "sub_category" => $subCategory,
                "list_type" => $listType,
            ];
            $arrDbResult = $model->getParts($arrParams);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function AccesoriesList($category = 0, $page = 1)
    {
        helper("aloparca");
        $cacheKey = "accesories_spare_part_list:" . $category . ":" . $page;
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrDbResult = $model->getAccesoriesParts($category, $page);
            if ($arrDbResult) {
                foreach ($arrDbResult["products"] as $key => $item) {
                    $prod = $model->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $arrResult["products"][] = aloparca::renderProductDetail($prod, "list");
                    }
                }
                $arrResult["page"] = (int) $page;
                if ($page == 1) {
                    $arrResult["total_pages"] = (int) $arrDbResult["total_pages"];
                    $arrResult["total_products"] = (int) $arrDbResult["total_products"];
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function MineralOilList($mainCategory = 0, $subCategory = 0, $page = 1)
    {
        helper("aloparca");
        $cacheKey = "oil_products_list:" . $mainCategory . ":" . $subCategory . ":" . $page;
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrDbResult = $model->getOilProducts($mainCategory, $subCategory, $page);
            if ($arrDbResult) {
                foreach ($arrDbResult["products"] as $key => $item) {
                    $prod = $model->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $arrResult["products"][] = aloparca::renderProductDetail($prod, "list");
                    }
                }
                $arrResult["page"] = (int) $page;
                if ($page == 1) {
                    $arrResult["total_pages"] = (int) $arrDbResult["total_pages"];
                    $arrResult["total_products"] = (int) $arrDbResult["total_products"];
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function BestSellerProducts()
    {
        helper("aloparca");
        $cacheKey = "best_seller_part_list:";
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrDbResult = $model->getBestSellerList();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $prod = $model->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $arrResult["products"][] = aloparca::renderProductDetail($prod, "list");
                    }
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function SimilarProducts($categoryID = 0)
    {
        helper("aloparca");
        $cacheKey = "best_similar_part_list:";
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrDbResult = $model->getSimilarProducts($categoryID);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $prod = $model->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $arrResult["products"][] = aloparca::renderProductDetail($prod, "list");
                    }
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function RecomendedProducts($categoryID = 0)
    {
        helper("aloparca");
        $cacheKey = "recomended_part_list:";
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrDbResult = $model->getRecomendedProducts($categoryID);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $prod = $model->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $arrResult["products"][] = aloparca::renderProductDetail($prod, "list");
                    }
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function FreeShippingProducts()
    {
        helper("aloparca");
        $cacheKey = "free_shipping_part_list:";
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $arrResult = [];
            $arrDbResult = $model->getFreeShippingProducts();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $prod = $model->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $arrResult["products"][] = aloparca::renderProductDetail($prod, "list");
                    }
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function Detail($productNo = 0)
    {
        helper("aloparca");
        $cacheKey = "product_detail:" . $productNo;
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $prod = $model->getProductDetail($productNo, "detail");
            if ($prod) {
                $prod = $prod[0];
                $arrResult[] = aloparca::renderProductDetail($prod, "detail");
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function RepairPacksCarBrands()
    {
        $model = new ProductsModel();
        $arrResult = cache("repair_packs_car_brands");

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $model->getRepairCarBrands();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $arrResult[] = $item->car_brand;
                }
            }
            //cache()->save($arrResult, 'car_brands',604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function RepairPacksCarModels($carBrand)
    {
        $model = new ProductsModel();
        $cacheKey = "repair_packs_car_models:" . $carBrand;
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $model->getRepairCarModels($carBrand);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $arrResult[] = $item->car_model;
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function RepairPacksCarEngines($carBrand, $carModel)
    {
        $model = new ProductsModel();
        $cacheKey = "repair_packs_car_engines:" . $carBrand . ":" . $carModel;
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $model->getRepairCarEngines($carBrand, $carModel);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $arrResult[] = $item->engine;
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }

    public function RepairPacks($carBrand, $carModel, $engine, $km)
    {
        $model = new ProductsModel();
        $cacheKey = "repair_packs:" . $carBrand . ":" . $carModel . ":" . $engine . ":" . $km;
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = $model->getRepairPacks($carBrand, $carModel, $engine, $km);

            //cache()->save($arrResult, $cacheKey,604800);
        }

        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => null,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }
}
