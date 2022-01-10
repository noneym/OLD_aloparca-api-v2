<?php
use App\Models\ProductsModel;
class aloparca
{
    public static function slugify(string $s, string $replacementCharacter = "_"): string
    {
        $s = preg_replace("~[^\pL\d]+~u", $replacementCharacter, $s);
        $s = iconv("utf-8", "us-ascii//TRANSLIT", $s);
        $s = preg_replace("~[^-\w]+~", "", $s);
        $s = trim($s, $replacementCharacter);
        $s = preg_replace("~-+~", $replacementCharacter, $s);
        $s = strtolower($s);

        return $s;
    }

    public static function asValidURL(string $name, string $replacementCharacter = "_")
    {
        $components = explode("/", $name);
        $output = "";

        foreach ($components as $component) {
            if ($component !== "") {
               $output .= '/' . slugify($component, $replacementCharacter);
            }
        }

        if ($output === "") {
            return "n-a";
        }

        return $output;
    }

    public static function calculateInstallment($price)
    {
        $arrRet = [];
        $arrRet[0]["installment_count"] = 2;
        $arrRet[0]["price"] = (float) number_format(
            number_format($price * 1.051, 2, ".", "") / 2,
            2,
            ".",
            ""
        );
        $arrRet[1]["installment_count"] = 3;
        $arrRet[1]["price"] = (float) number_format(
            number_format($price * 1.063, 2, ".", "") / 3,
            2,
            ".",
            ""
        );
        $arrRet[2]["installment_count"] = 4;
        $arrRet[2]["price"] = (float) number_format(
            number_format($price * 1.076, 2, ".", "") / 4,
            2,
            ".",
            ""
        );
        $arrRet[3]["installment_count"] = 5;
        $arrRet[3]["price"] = (float) number_format(
            number_format($price * 1.09, 2, ".", "") / 5,
            2,
            ".",
            ""
        );
        $arrRet[4]["installment_count"] = 6;
        $arrRet[4]["price"] = (float) number_format(
            number_format($price * 1.11, 2, ".", "") / 6,
            2,
            ".",
            ""
        );
        $arrRet[5]["installment_count"] = 7;
        $arrRet[5]["price"] = (float) number_format(
            number_format($price * 1.12, 2, ".", "") / 7,
            2,
            ".",
            ""
        );
        $arrRet[6]["installment_count"] = 8;
        $arrRet[6]["price"] = (float) number_format(
            number_format($price * 1.13, 2, ".", "") / 8,
            2,
            ".",
            ""
        );
        $arrRet[7]["installment_count"] = 9;
        $arrRet[7]["price"] = (float) number_format(
            number_format($price * 1.15, 2, ".", "") / 9,
            2,
            ".",
            ""
        );

        $arrRet["cards"][] = "bonus";
        $arrRet["cards"][] = "world";
        $arrRet["cards"][] = "cardfinans";
        $arrRet["cards"][] = "axess";
        $arrRet["cards"][] = "maximum";
        $arrRet["cards"][] = "paraf";

        return $arrRet;
    }

    public static function parseOemCodes($oemCodes)
    {
        $arrResult = [];
        if ($oemCodes) {
            $oemCodes = explode("\n", $oemCodes);
            $arrOem = array_unique($oemCodes);
            foreach ($arrOem as $oem) {
                $oemItem = explode("[=>]", $oem);
                if (@$oemItem[1]) {
                    if (@$oemItem[0]) {
                        $arrResult[] = ["brand" => $oemItem[0], "code" => $oemItem[1]];
                    }
                }
            }
        }
        return $arrResult;
    }

    public static function renderProductDetail($prod, $renderType)
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
            aloparca::asValidURL($brandName) .
            aloparca::asValidURL($name) .
            aloparca::asValidURL(str_replace("/", ".", $prod->stock_code)) .
            aloparca::asValidURL($prod->product_no);
        $compatibleVehicleStatus = $model->getcompatibleVehicleStatus($prod->id);
        $listPrice = $prod->list_price;
        if ((int) $prod->list_price < (int) $prod->sale_price) {
            $listPrice = $prod->sale_price * 1.2;
        }

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
                (int) ($prod->accessory_status == 1 ? 1 : aloparca::productTypeByBrand($brandName)),
            "stock_status" => (int) $prod->stock_status,
            "free_shipping_status" => (int) $prod->free_shipping_status,
            "list_price" => (float) $listPrice,
            "sale_price" => (float) $prod->sale_price,
            "discount_rate" => (float) number_format(
                aloparca::getDiscountRate($listPrice, $prod->sale_price),
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
            $arrRet["category"] = (int) $prod->category_id;
            $arrRet["breadcrumb"] = aloparca::createBreadCrumb($prod, $productSlug);
            $arrRet["compatible_vehicles"] = $arrCompatibleVehicles;
            $arrRet["features"] = $arrFeatures;
            $arrRet["installment"] = aloparca::calculateInstallment($prod->sale_price);
            $arrRet["oem_codes"] = aloparca::parseOemCodes($prod->oem_codes);
            $arrRet["description"] = $description;
        }

        return $arrRet;
    }

    public static function productTypeByBrand($brandName)
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

    public static function getDiscountRate($listPrice, $salePrice)
    {
        if ($listPrice == 0) {
            $listPrice = 2;
        }
        if ($salePrice == 0) {
            $salePrice = 1;
        }
        $percentage = ((intval($listPrice) - $salePrice) / intval($listPrice)) * 100;

        return round($percentage);
    }

    public static function createBreadCrumb($prod, $productSlug)
    {
        $arrRet = [];
        $model = new ProductsModel();
        //Parça Oto Aksesuar Parçası ise;
        if ($prod->accessory_status == 1) {
            $arrRet[0]["category_slug"] = "/otoaksesuar";
            $arrRet[0]["name"] = "Aksesuar";
            $arrRet[0]["slug"] = "/otoaksesuar";
            $arrRet[1]["category_slug"] = aloparca::asValidURL($prod->accessory_category);
            $arrRet[1]["name"] = $prod->accessory_category;
            $arrRet[1]["slug"] = "/otoaksesuar" . aloparca::asValidURL($prod->accessory_category);
            $arrRet[2]["name"] = $prod->name;
            $arrRet[2]["slug"] = "/yedek-parca" . $productSlug;
            return $arrRet;
        }

        //Parça Madeni Yağ ise;
        if ($prod->oil_status == 1) {
            $oil = $model->getOilProductCategories($prod->product_no);
            $oil = $oil[0];

            $arrRet[0]["name"] = "Madeni Yağlar";
            $arrRet[0]["slug"] = "/madeni-yaglar/motor-yaglari";
            $arrRet[1]["name"] = $oil->mainCatName;
            $arrRet[1]["slug"] = "/madeni-yaglar/motor-yaglari/" . $oil->mainCatID;
            $arrRet[2]["name"] = $oil->subCatName;
            $arrRet[2]["slug"] =
                "/madeni-yaglar/motor-yaglari/" .
                $oil->mainCatID .
                "/altkat" .
                aloparca::asValidURL($oil->subCatName);
            $arrRet[3]["name"] = $prod->name;
            $arrRet[3]["slug"] = "/yedek-parca" . $productSlug;
            return $arrRet;
        }

        //Oto Yedek Parça ise;
        $breadCrumb = $model->getPartBreadCrumbInfo($prod->id);
        if ($breadCrumb) {
            $breadCrumb = $breadCrumb[0];
            $arrRet[0]["name"] =
                "/oto-yedek-parca/ustkategori" . aloparca::asValidURL($breadCrumb->mainCategory);
            $arrRet[0]["slug"] = $breadCrumb->mainCategory;
            $arrRet[1]["name"] =
                "/oto-yedek-parca/ustkategori" .
                aloparca::asValidURL($breadCrumb->mainCategory) .
                "/altkategori" .
                aloparca::asValidURL($breadCrumb->subCategory);
            $arrRet[1]["slug"] = $breadCrumb->subCategory;
            $arrRet[2]["name"] =
                "yedek-parca" .
                aloparca::asValidURL($breadCrumb->partBrand) .
                aloparca::asValidURL($breadCrumb->partName);
            $arrRet[2]["slug"] = $breadCrumb->supplierCode;
            return $arrRet;
        }
    }

    public static function ucFirstSentence($str)
    {
        return preg_replace_callback(
            "/([.!?])\s*(\w)/",
            function ($matches) {
                return strtoupper($matches[1] . " " . $matches[2]);
            },
            ucwords(mb_strtolower($str))
        );
    }

    public static function clearSpecialCharsForSearch($str)
    {
        return str_replace(" ", "", str_replace("_", "", str_replace("-", "", $str)));
    }

    public static function inappropriateCharCheck($string)
    {
        if (strpos($string, " ") || (preg_match("/[A-Z]/", $string) && $string != "/")) {
            return aloparca::asValidURL($string, "-");
        }
        return false;
    }
}
