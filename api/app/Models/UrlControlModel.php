<?php namespace App\Models;
use aloparca;
use CodeIgniter\Model;

class UrlControlModel extends Model
{
    public function redirectContol($url)
    {
        $charControl = aloparca::inappropriateCharCheck($url);

        if ($charControl) {
            return $charControl;
        }

        $q = "select yonlendirilecek_url as redirect_uri from url_301_listesi where kaynak_url = '{$url}'";

        $result = $this->db->query($q)->getResult();
        if ($result) {
            return $result[0]->redirect_uri;
        }
    }

    public function mainCategoryCheck($categoryName)
    {
        $q =
            "select GA_ASSEMBLY as main_category_name from EXPORTADDITION_GAINFO where replace(replace(replace(replace(GA_ASSEMBLY,'.',''),' ',''),'-',''),'_','') = '" .
            $categoryName .
            "' limit 1";
        return $this->db->query($q)->getResult();
    }

    public function subCategoryCheck($categoryName)
    {
        $q =
            "select GA_STANDARD as sub_category_name from EXPORTADDITION_GAINFO where replace(replace(replace(replace(GA_STANDARD,'.',''),' ',''),'-',''),'_','') = '" .
            $categoryName .
            "' limit 1";
        return $this->db->query($q)->getResult();
    }

    /**
     * Parça markalarının marka ve ürün listesi sayfaları
     */
    public function parsePartBrandUri($arrParams = [], $url)
    {
        helper("aloparca");
        $itemCount = count($arrParams);
        if ($itemCount < 3) {
            return false;
        }

        $brandSlug = aloparca::clearSpecialCharsForSearch($arrParams[2]);

        $q = "select no,stokmarka as brand_name from stokyonetimi where replace(replace(replace(stokmarka,' ',''),'-',''),'_','') = '{$brandSlug}' limit 1";

        $brandCheck = $this->db->query($q)->getResult();
        if (!$brandCheck) {
            return false;
        } else {
            $brandName = aloparca::ucFirstSentence($brandCheck[0]->brand_name);
            $categoryName = "";
            if ($itemCount == 4) {
                $categorySlug = end($arrParams);
            }
            if ($categorySlug != "") {
                $cat = $this->subCategoryCheck(aloparca::clearSpecialCharsForSearch($categorySlug));
                if ($cat) {
                    $categoryName = $cat[0]->sub_category_name;
                }
            }

            if ($categoryName == "") {
                $title =
                    $brandName .
                    " Yedek Parça - " .
                    $brandName .
                    " Yedek Parça Fiyatları | Aloparca.com";
                $description =
                    $brandName .
                    " yedek parça ürünleri en uygun fiyatlarla Aloparca.com'da! Orjinal " .
                    $brandName .
                    " oto yedek parçalarını online satın almak için tıkla!";
            } else {
                $title = $brandName . " " . $categoryName . " Fiyatları | Aloparca.com";
                $description =
                    $brandName .
                    " " .
                    $categoryName .
                    " ürünleri en uygun fiyatlarla Aloparca.com'da! Orjinal " .
                    $brandName .
                    " oto yedek parçalarını online satın almak için tıkla!";
            }

            return [
                "title" => $title,
                "description" => $description,
                "cannonical" => "https://www.aloparca.com" . $url,
            ];
        }
    }

    /**
     * Aksesuar ürün listesi sayfaları
     */
    public function parseAccessoriesUri($arrParams = [], $url)
    {
        helper("aloparca");
        $itemCount = count($arrParams);

        $categoryName = "";
        if ($itemCount == 3) {
            $categorySlug = end($arrParams);
        }
        if (!empty($categorySlug)) {
            $categoryName = ucwords(
                mb_strtolower(str_replace("_", " ", str_replace("-", " ", $categorySlug)))
            );
        }

        if ($categoryName == "") {
            $title = "Oto Aksesuar Ürünleri - Araç Aksesuarları | Aloparca.com";
            $description =
                "Oto aksesuar. Araç aksesuarları. Araba aksesuar ürünleri. Otomobil aksesuar malzemeleri. Araç aksesuar çeşitleri. Araba aksesuar fiyatları.";
        } else {
            $title = $categoryName . " Modelleri ve Fiyatları - Oto Aksesuarları | Aloparca.com";
            $description =
                $categoryName .
                " oto aksesuar modelleri en uygun fiyatlarla Aloparca.com'da! " .
                $categoryName .
                " araç aksesuar ürünlerini online satın almak için tıkla!";
        }

        return [
            "title" => $title,
            "description" => $description,
            "cannonical" => "https://www.aloparca.com" . $url,
        ];
    }

    /**
     * Kampanya ürün listesi sayfaları
     */
    public function parseCampainsUri($arrParams = [], $url)
    {
        helper("aloparca");
        $itemCount = count($arrParams);

        if ($itemCount == 3) {
            return false;
        }

        if ($itemCount == 4) {
            $category = str_replace("-", " ", $arrParams[2]);
            $brand = str_replace("-", " ", $arrParams[3]);

            if (strlen($brand) < 3) {
                return false;
            }

            $q = "select no from stokyonetimi where ifnull(kampanyali,0) = 1 and kampanya = '{$category}' and arac_marka = '{$brand}' limit 1";

            $result = $this->db->query($q)->getResult();
            if (!$result) {
                return false;
            }
        }

        return [
            "title" => "Oto Yedek Parça | Aloparca.com",
            "description" =>
                "Tüm otomobil markalarının tüm araba parçalarını bulabileceğiniz, Türkiyenin en büyük online yedek parça satışı sitesi. Orijinal yedek parça fiyatları ve yan sanayi yedek parça fiyatları online yedek parça listemizde sizi bekliyor. En uygun fiyat ve garantili oto yedek parça sitesi Aloparça.",
            "cannonical" => "https://www.aloparca.com" . $url,
        ];
    }

    /**
     * Araç Marka Model ve Kategori Kombinasyonu Sayfaları
     */
    public function parseSparePartUri($arrParams = [], $url)
    {
        helper("aloparca");
        $itemCount = count($arrParams);

        if ($itemCount < 3) {
            return false;
        }

        $carBrandCheck = $this->carBrandCheck($arrParams[2]);
        $carBrand = "";
        $modelName = "";
        if ($carBrandCheck) {
            $carBrand = $carBrandCheck[0]->car_brand;
            if ($itemCount > 3) {
                $carModelCheck = $this->carModelCheck($arrParams[3]);
                if ($carModelCheck) {
                    $modelName = " " . $carModelCheck[0]->model_name . " ";
                }
                $title = ucwords(mb_strtolower($carBrand . $modelName . "Yedek Parça"));
                $description =
                    ucwords(mb_strtolower($carBrand . $modelName)) .
                    "yedek parça. " .
                    ucwords(mb_strtolower($carBrand . $modelName)) .
                    "yedek parça fiyat listesi. " .
                    ucwords(mb_strtolower($carBrand . $modelName)) .
                    "orjinal yedek parça fiyatları. " .
                    ucwords(mb_strtolower($carBrand . $modelName)) .
                    "oto yedek parça online satış yeri.";

                $mainCategoryIndex = array_search("ustkategori", $arrParams);
                if ($mainCategoryIndex) {
                    $categoryCheck = $this->categoryCheck($arrParams);
                    if ($categoryCheck) {
                        if ($categoryCheck["main_category_status"] == true) {
                            $categoryName = $categoryCheck["main_category_name"];
                            if ($categoryCheck["sub_category_status"] == true) {
                                $categoryName = $categoryCheck["sub_category_name"];
                            }
                            $title =
                                ucwords(mb_strtolower($carBrand . $modelName . $categoryName)) .
                                " Yedek Parça Fiyatları";
                            $description =
                                ucwords(mb_strtolower($carBrand . $modelName . $categoryName)) .
                                ". " .
                                ucwords(mb_strtolower($carBrand . $modelName . $categoryName)) .
                                " yedek parça fiyat listesi. " .
                                ucwords(
                                    mb_strtolower(
                                        $carBrand . $modelName . "Orjinal " . $categoryName
                                    )
                                ) .
                                " yedek parça fiyatları.";
                        }
                    }
                }
            }
        } else {
            $categoryCheck = $this->categoryCheck($arrParams);
            if ($categoryCheck) {
                if ($categoryCheck["main_category_status"] == true) {
                    $categoryName = $categoryCheck["main_category_name"];
                    if ($categoryCheck["sub_category_status"] == true) {
                        $categoryName = $categoryCheck["sub_category_name"];
                    }
                    $title =
                        ucwords(mb_strtolower($carBrand . $modelName . $categoryName)) .
                        " Yedek Parça Fiyatları";
                    $description =
                        ucwords(mb_strtolower($carBrand . $modelName . $categoryName)) .
                        ". " .
                        ucwords(mb_strtolower($carBrand . $modelName . $categoryName)) .
                        " yedek parça fiyat listesi. " .
                        ucwords(
                            mb_strtolower($carBrand . $modelName . "Orjinal " . $categoryName)
                        ) .
                        " yedek parça fiyatları.";
                }
            }
            return false;
        }
        return [
            "title" => $title . " | Aloparca.com",
            "description" => $description,
            "cannonical" => "https://www.aloparca.com" . $url,
            "tesst" => $arrParams,
        ];
    }

    public function carBrandCheck($brand)
    {
        $q =
            "select CAR_BRANDS as car_brand from TABLECARS where CAR_BRANDS = '" .
            $brand .
            "' limit 1";
        return $this->db->query($q)->getResult();
    }

    public function carModelCheck($model)
    {
        $q =
            "select MODEL_CAR as model_name from TABLECARS where MODEL_CAR = '" .
            $model .
            "' limit 1";
        return $this->db->query($q)->getResult();
    }

    public function categoryCheck($arrParams)
    {
        $mainCategoryIndex = array_search("ustkategori", $arrParams);
        $arrReturn = [];
        if ($mainCategoryIndex) {
            $mainCategoryCheck = $this->mainCategoryCheck($arrParams[$mainCategoryIndex + 1]);
            if ($mainCategoryCheck) {
                $arrReturn["main_category_status"] = true;
                $arrReturn["main_category_name"] = $mainCategoryCheck[0]->main_category_name;
                $subCategoryIndex = array_search("altkategori", $arrParams);
                if ($subCategoryIndex) {
                    $subCategoryCheck = $this->subCategoryCheck($arrParams[$subCategoryIndex + 1]);
                    if ($subCategoryCheck) {
                        $arrReturn["sub_category_status"] = true;
                        $arrReturn["sub_category_name"] = $subCategoryCheck[0]->sub_category_name;
                    }
                }
                return $arrReturn;
            }
        }
        return false;
    }
}
