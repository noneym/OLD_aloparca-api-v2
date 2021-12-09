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
                        $arrResult["products"][] = $this->renderProductDetail($prod, "list");
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
                        $arrResult["products"][] = $this->renderProductDetail($prod, "list");
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

    public function Detail($productNo = 0)
    {
        helper("aloparca");
        $cacheKey = "product_detail:" . $productNo;
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new ProductsModel();
            $prod = $model->getProductDetail($productNo);
            if ($prod) {
                $prod = $prod[0];
                $arrResult["products"][] = $this->renderProductDetail($prod, "detail");
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

    public function renderProductDetail($prod, $renderType)
    {
        $model = new ProductsModel();
        $name =
            trim($prod->part_brand_name) .
            " " .
            $prod->stock_code .
            " " .
            (strlen($prod->alternative_name) > 2 ? $prod->alternative_name : $prod->name);
        $brandName = $prod->part_brand_name ? $prod->part_brand_name : "Diğer";
        $productSlug =
            aloparca::validUrl($brandName) .
            aloparca::validUrl($name) .
            aloparca::validUrl(str_replace("/", ".", $prod->stock_code)) .
            aloparca::validUrl($prod->product_no);
        $listPrice = number_format((float) $prod->list_price, 2, ".", "");
        $salePrice = number_format(
            ($prod->sale_price - $prod->discount_price + $prod->raise_price) * $prod->price_rate,
            2,
            ".",
            ""
        );
        if ($listPrice < $salePrice) {
            $listPrice = number_format((float) ($salePrice * 1.2), 2, ".", "");
        }
        $compatibleVehicleStatus = $model->getcompatibleVehicleStatus($prod->id);
        $arrRet = [
            "id" => (int) $prod->id,
            "product_no" => (int) $prod->product_no,
            "name" => $name,
            "title" =>
                $brandName .
                " - " .
                $name .
                " - " .
                (empty($prod->supplier_stock_code)
                    ? str_replace("/", ".", $prod->stock_code)
                    : $prod->supplier_stock_code),
            "stock_code" => str_replace("/", ".", $prod->stock_code),
            "supplier_stock_code" => $prod->supplier_stock_code,
            "brand_name" => $brandName,
            "product_type" =>
                (int) ($prod->accessory_status == 1 ? 1 : $this->productTypeByBrand($brandName)),
            "stock_status" => (int) $prod->stock_status,
            "free_shipping_status" => (int) $prod->free_shipping_status,
            "list_price" => (float) $listPrice,
            "sale_price" => (float) $salePrice,
            "discount_rate" => (float) number_format(
                $this->getDiscountRate($listPrice, $salePrice),
                2
            ),
            "image" => $prod->img_url,
            "campain_name" => $prod->campain_name,
            "campain_status" => (int) $prod->campain_status,
            "oil_status" => (int) $prod->oil_status,
            "accessory_status" => (int) $prod->accessory_status,
            "disinfectant_status" => (int) $prod->disinfectant_status,
            "brand_logo" => $model->getProdBrandLogo($prod->part_brand_name),
            "min_order_count" => (int) 1,
            "max_order_count" => (int) ($prod->oil_status == 1 ? 4 : 20),
            "compatible_vehicle_status" => (int) $compatibleVehicleStatus,
            "slug" => $productSlug,
            "supplier_description" => $prod->supplier_description,
            "brand_description" => $model->getBrandDescription($brandName),
        ];

        if ($renderType == "detail") {
            $arrCompatibleVehicles = [];
            if ($compatibleVehicleStatus == 1) {
                $arrCompatibleVehicles = $model->getCompatibleVehicles($prod->id);
            }
            $arrFeatures = [];
            if ($prod->oil_status == 0) {
                $arrFeatures = $model->getProdFeatures($prod->id);
            }
            $description = $model->getProductDescription(
                $prod->product_no,
                $prod->supplier_stock_code,
                $name,
                $brandName
            );
            $arrRet["breadcrumb"] = $this->createBreadCrumb($prod, $productSlug);
            $arrRet["compatible_vehicles"] = $arrCompatibleVehicles;
            $arrRet["features"] = $arrFeatures;
            $arrRet["installment"] = aloparca::calculateInstallment($salePrice);
            $arrRet["oem_codes"] = aloparca::parseOemCodes($prod->oem_codes);
            $arrRet["description"] = $description;
        }

        return $arrRet;
    }

    public function productTypeByBrand($brandName)
    {
        //Parçanın markasına göre,
        // 1 - Logolu Orjinal
        // 2 - Logosuz Orjinal
        // 3 - Diğer
        $arrTypes = [
            "AMC" => 3,
            "AYD" => 3,
            "AYFAR" => 3,
            "BEHR" => 2,
            "BILSSTEIN" => 2,
            "BOGE" => 2,
            "BORGWARNER" => 2,
            "BOSCH" => 2,
            "BREMİ" => 3,
            "BSG" => 3,
            "CABU" => 3,
            "CAPAT" => 3,
            "CERKEZ" => 3,
            "CIFAM" => 3,
            "CONTITECH" => 3,
            "COYS" => 3,
            "DELPHI" => 2,
            "DENSO" => 2,
            "DEPO" => 3,
            "DESPA" => 3,
            "EBERSPACHER" => 3,
            "ECO-FIX" => 3,
            "EKOKAP" => 3,
            "ELRING" => 3,
            "EMBO" => 3,
            "EUROBUMP" => 3,
            "EUROLITES" => 3,
            "FAG" => 3,
            "FARBA" => 3,
            "FASE" => 3,
            "FEBI" => 2,
            "FER" => 3,
            "FIAMM" => 3,
            "FROW" => 3,
            "FTE" => 3,
            "GARRETT" => 2,
            "GATES" => 2,
            "GEBA" => 3,
            "GEMO" => 3,
            "GEMŞAFT" => 3,
            "GK" => 3,
            "GKN" => 2,
            "GMB" => 3,
            "GOLD" => 3,
            "GVA" => 3,
            "HAGUS" => 3,
            "HELLA" => 2,
            "HENGST" => 3,
            "HOLI" => 3,
            "INA" => 3,
            "ISAM" => 3,
            "JDEUS" => 3,
            "JESSELAİ" => 3,
            "KALE" => 2,
            "KAPIMSAN" => 3,
            "KYB" => 2,
            "LECOY" => 2,
            "LEMFORDER" => 2,
            "LEMFÖRDER" => 2,
            "LESJÖFORS" => 3,
            "LOBRO" => 3,
            "LUK" => 2,
            "MAGNETI MARELLI" => 2,
            "MAHER" => 3,
            "MAIS" => 1,
            "OEM" => 1,
            "MAKO" => 3,
            "MARS" => 3,
            "MEKRA" => 3,
            "MEYLE" => 3,
            "MINTEX" => 3,
            "MONROE" => 2,
            "NISSENS" => 3,
            "OPTIMAL" => 3,
            "ORAN" => 3,
            "ORIS_RADYATÖR" => 3,
            "ORJINAL" => 3,
            "OSRAM" => 3,
            "PHIRA" => 3,
            "PIERBURG" => 3,
            "PLEKSAN" => 3,
            "PRASCO" => 3,
            "PULO" => 3,
            "R2A" => 3,
            "ROOT" => 3,
            "RUVILLE" => 3,
            "SACHS" => 2,
            "SEGER" => 3,
            "SIDEM" => 3,
            "SIMYI" => 3,
            "SKF" => 3,
            "SNR" => 3,
            "SODSAN" => 3,
            "SOSA" => 3,
            "SPILU" => 3,
            "SPJ" => 3,
            "SPMP" => 3,
            "STABILUS" => 3,
            "S-TEC" => 3,
            "SWAG" => 3,
            "SWF" => 3,
            "SYF" => 3,
            "TEXTAR" => 3,
            "TKY" => 3,
            "TONGYANG" => 3,
            "TRUCKTEC" => 3,
            "ULO" => 2,
            "USP_PRODUCTS" => 3,
            "VALEO" => 2,
            "VDO" => 3,
            "VIEWMAX" => 3,
            "VISTEON" => 2,
            "ZF" => 2,
            "ZIEGLER" => 3,
            "ZKW" => 2,
        ];

        if (array_key_exists($brandName, $arrTypes)) {
            return $arrTypes[$brandName];
        } else {
            return 3;
        }
    }

    public function getDiscountRate($listPrice, $salePrice)
    {
        if ($listPrice == 0) {
            $listPrice = 2;
        }
        if ($salePrice == 0) {
            $salePrice = 1;
        }
        $percentage = (($listPrice - $salePrice) / $listPrice) * 100;

        return round($percentage);
    }

    public function createBreadCrumb($prod, $productSlug)
    {
        $arrRet = [];
        $model = new ProductsModel();
        //Parça Oto Aksesuar Parçası ise;
        if ($prod->accessory_status == 1) {
            $arrRet[0]["category_slug"] = "/otoaksesuar";
            $arrRet[0]["name"] = "Aksesuar";
            $arrRet[0]["slug"] = "/otoaksesuar";
            $arrRet[1]["category_slug"] = aloparca::validUrl($prod->accessory_category);
            $arrRet[1]["name"] = $prod->accessory_category;
            $arrRet[1]["slug"] = "/otoaksesuar" . aloparca::validUrl($prod->accessory_category);
            $arrRet[2]["name"] = $prod->name;
            $arrRet[2]["slug"] = "/yedek-parca" . $productSlug;
            return $arrRet;
        }

        //Parça Madeni Yağ ise;
        if ($prod->oil_status == 1) {
            $oil = $model->getOilProductCategories($prod->product_no);
            $oil = $oil[0];
            $arrRet[0]["category_slug"] = "/madeni-yaglar/motor-yaglari";
            $arrRet[0]["name"] = "Madeni Yağlar";
            $arrRet[0]["slug"] = "/madeni-yaglar/motor-yaglari";

            $arrRet[1]["category_slug"] = "/madeni-yaglar/motor-yaglari/" . $oil->mainCatID;
            $arrRet[1]["name"] = $oil->mainCatName;
            $arrRet[1]["slug"] = "/madeni-yaglar/motor-yaglari/" . $oil->mainCatID;
            $arrRet[2]["name"] = $oil->subCatName;
            $arrRet[2]["slug"] =
                "madeni-yaglar/motor-yaglari/" .
                $oil->mainCatID .
                "/altkat" .
                aloparca::validUrl($oil->subCatName);
            $arrRet[3]["name"] = $prod->name;
            $arrRet[3]["slug"] = "/yedek-parca" . $productSlug;
            return $arrRet;
        }

        //Oto Yedek Parça ise;
    }
}
