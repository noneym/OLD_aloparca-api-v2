<?php namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
    public function getProductDetail($productNo)
    {
        $q = "
        select
        id,
        no as product_no,
        stokadi as name,
        stokkodu as stock_code,
        stokkodu_ismail as supplier_stock_code,
        yeni_isim as alternative_name,
        stokmarka as part_brand_name,
        resim as img_url,
        stokfiyati as sale_price,
        ifnull(listefiyati,0) as list_price,
        gelisfiyati as cost_price,
        stokdurumu as stock_status,
        ifnull(bedava_kargo,0) as free_shipping_status,
        ifnull(indirim_tl,0) as discount_price,
        ifnull(zam_tl,0) as raise_price,
        ifnull(indirim_zam_yuzde,1) as price_rate,
        urunAdi as supplier_description,
        kate1 as accessory_category,
        kampanyali as campain_status,
        arac_marka as campain_name,
        madeniyag as oil_status,
        aksesuar as accessory_status,
        dezenfektan as disinfectant_status,
        ART_CROSS as oem_codes
        from stokyonetimi s
        where
        no = {$productNo}
        and stokfiyati > 0
        and status = 1
        limit 1
        ";

        return $this->db->query($q)->getResult();
    }

    public function getParts($arrParams = [])
    {
        $q = "";

        return $this->db->query($q)->getResult();
    }

    public function getAccesoriesParts($categoryName, $page)
    {
        $pageCount = 1;
        $productCount = 20;
        if ($page == 1) {
            $q = "
            select
            no
            from stokyonetimi s
            where
            aksesuar = 1
            and stokfiyati > 0
            and status = 1 ";
            if ($categoryName != 0) {
                $categoryName = str_replace("-", " ", $categoryName);
                $q .= " and kate1 = '{$categoryName}'";
            }

            $prods = $this->db->query($q)->getResult();
            if ($prods) {
                $passCount = 0;
                $productCount = count($prods);
                $pageCount = ceil(count($prods) / 20);
            } else {
                return false;
            }
        } else {
            $passCount = 20 * ($page - 1);
        }

        $q = "
        select
        no
        from stokyonetimi s
        where
        aksesuar = 1
        and stokfiyati > 0
        and status = 1 ";
        if ($categoryName != 0) {
            $categoryName = str_replace("-", " ", $categoryName);
            $q .= " and kate1 = '{$categoryName}'";
        }
        $q .= " order by stokdurumu desc, stokfiyati asc ";
        $q .= " limit $passCount, 20";

        $products = $this->db->query($q)->getResult();

        if ($products) {
            return [
                "total_pages" => $pageCount,
                "total_products" => $productCount,
                "products" => $products,
            ];
        }
        return false;
    }

    public function getOilProducts($mainCategory, $subCategory, $page)
    {
        $pageCount = 1;
        $productCount = 20;
        if ($page == 1) {
            $q = "
            select
            s.no
            from kategori_urun k
            join stokyonetimi s on s.no = k.urun_no and s.madeniyag = 1
            where
            stokfiyati > 0
            and status = 1 ";
            if ($mainCategory != 0) {
                $q .= " and k.ustkate = '{$mainCategory}'";
            }

            if ($subCategory != 0) {
                $q .= " and k.altkat_slug = '{$subCategory}'";
            }

            $prods = $this->db->query($q)->getResult();
            if ($prods) {
                $passCount = 0;
                $productCount = count($prods);
                $pageCount = ceil(count($prods) / 20);
            } else {
                return false;
            }
        } else {
            $passCount = 20 * ($page - 1);
        }

        $q = "
        select
            s.no
            from kategori_urun k
            join stokyonetimi s on s.no = k.urun_no and s.madeniyag = 1
            where
            stokfiyati > 0
            and status = 1 ";
        if ($mainCategory != 0) {
            $q .= " and k.ustkate = '{$mainCategory}'";
        }

        if ($subCategory != 0) {
            $q .= " and k.altkat_slug = '{$subCategory}'";
        }
        $q .= " order by stokdurumu desc, stokfiyati asc ";
        $q .= " limit $passCount, 20";

        $products = $this->db->query($q)->getResult();

        if ($products) {
            return [
                "total_pages" => $pageCount,
                "total_products" => $productCount,
                "products" => $products,
            ];
        }
        return false;
    }

    public function getProductDescription($productNo, $supplierCode, $name, $brandName)
    {
        $q = "select aciklama as description from stokyonetimi2 where no = {$productNo} limit 1";

        $prod = $this->db->query($q)->getResult();
        if ($prod) {
            $prod = $prod[0];
            $description = str_replace("\r", " ", (string) $prod->description);
            $description = str_replace("\n", " ", $description);
            $description = str_replace("  ", "", ucfirst($description));
        }
        if (strlen($description) < 10) {
            $siteLink = ' <a href=\"https://www.aloparca.com/\">aloparca.com</a> \'da ';
            $description =
                "En cazip fiyatlar ile " .
                $siteLink .
                " satışa sunulan " .
                $supplierCode .
                " " .
                $brandName .
                " stok kodlu " .
                $name .
                ",  müşterilerimize en güvenli şekilde en hızlı kargo ile teslim edilmektedir.";
        }

        return $description;
    }

    public function getProdBrandLogo($brandName)
    {
        $q = "select TTC_BRA_ID from TABLEBRANDS where BRAND = '{$brandName}' limit 1";

        $prod = $this->db->query($q)->getResult();
        if ($prod) {
            $prod = $prod[0];
            return "/Brand_logos/" . $prod->TTC_BRA_ID . ".jpg";
        }

        return "";
    }

    public function getcompatibleVehicleStatus($productID)
    {
        $q = "select TTC_TYP_ID from EXPORTADDITION_TYPID_ARTID where TTC_ART_ID = '{$productID}' limit 1";

        $prod = $this->db->query($q)->getResult();
        if ($prod) {
            return true;
        }

        return false;
    }

    public function getOilProductCategories($productNo)
    {
        $q = "
        select
        k.id as mainCatID,
        k.isim as mainCatName,
        u.altkate as subCatName
        from ustkategori k
        join kategori_urun u on u.ustkate = k.id
        where
        urun_no = {$productNo}
        and k.status = 1
        limit 1
        ";

        return $this->db->query($q)->getResult();
    }

    public function getBrandDescription($brandName)
    {
        $q = "
        select
            aciklama as description
        from marka_aciklama
        where marka = '{$brandName}'
        limit 1
        ";

        $result = $this->db->query($q)->getResult();
        if ($result) {
            return $result[0]->description;
        }
        return null;
    }

    public function getCompatibleVehicles($productID)
    {
        $q = "
        select
            group_concat(TTC_TYP_ID) as typId
        from EXPORTADDITION_TYPID_ARTID 
        where 
            TTC_ART_ID = {$productID}
            and ifnull(disable,0) = 0 
        limit 100
        ";

        $result = $this->db->query($q)->getResult();

        $arrCars = [];
        if ($result) {
            $q =
                'select 
            CAR_BRANDS,MODEL_CAR,TYP_CAR,OF_THE_YEAR,UP_TO_A_YEAR,KV,BODY_TYPE
            from TABLECARS 
            where 
            TTC_TYP_ID in (' .
                $result[0]->typId .
                ')
            and ifnull(disable,0) = 0
            ORDER BY CAR_BRANDS,MODEL_CAR,TYP_CAR,OF_THE_YEAR,UP_TO_A_YEAR,KV,BODY_TYPE
            ';
            $arrCarResult = $this->db->query($q)->getResult();
            if ($arrCarResult) {
                foreach ($arrCarResult as $key => $car) {
                    $arrCars[] = [
                        "car_brand" => $car->CAR_BRANDS,
                        "car_model" => $car->MODEL_CAR,
                        "body_type" => $car->BODY_TYPE,
                        "engine" => $car->TYP_CAR,
                        "kv" => $car->KV,
                        "year" =>
                            $car->OF_THE_YEAR .
                            " - " .
                            ($car->UP_TO_A_YEAR < 1 ? "Sonrası" : $car->UP_TO_A_YEAR),
                    ];
                }
            }
        }
        return $arrCars;
    }

    public function getProdFeatures($productID)
    {
        $q = "SELECT NAMECRITERIA,VALUECRITERIA FROM MAINTABLEPARTSCRI WHERE TTC_ART_ID = {$productID}";

        $result = $this->db->query($q)->getResult();

        $arrFeatures = [];
        if ($result) {
            foreach ($arrFeatures as $key => $car) {
                $arrFeatures[] = [
                    "name" => $car->NAMECRITERIA,
                    "value" => $car->VALUECRITERIA,
                ];
            }
        }
        return $arrFeatures;
    }
}
