<?php namespace App\Models;

use CodeIgniter\Model;

class ProductsModel extends Model
{
    public function getProductDetail($productNo, $type = "list")
    {
        $q = "
        select
        s.id,
        no as product_no, ";
        if ($type == "detail") {
            $q .= "
            IFNULL(TTC_GA_ID,0) as category_id,
            ";
        }
        $q .= "stokadi as name,
        stokkodu as stock_code,
        stokkodu_ismail as supplier_stock_code,
        yeni_isim as alternative_name,
        stokmarka as part_brand_name,
        if(s.resim=0,null,s.resim) as img_url,
        (stokfiyati-ifnull(indirim_tl,0)+ifnull(zam_tl,0))*ifnull(if(indirim_zam_yuzde='',1,indirim_zam_yuzde),1) as sale_price,
        stokfiyati as base_sale_price,
        if(ifnull(listefiyati,0)=0,(stokfiyati*1.2),listefiyati) as list_price,
        gelisfiyati as cost_price,
        stokdurumu as stock_status,
        ifnull(bedava_kargo,0) as free_shipping_status,
        ifnull(indirim_tl,0) as discount_price,
        ifnull(zam_tl,0) as raise_price,
        ifnull(if(indirim_zam_yuzde='',1,indirim_zam_yuzde),1) as price_rate,
        urunAdi as supplier_description,
        kate1 as accessory_category,
        kampanyali as campain_status,
        arac_marka as campain_name,
        madeniyag as oil_status,
        aksesuar as accessory_status,
        dezenfektan as disinfectant_status,
        ART_CROSS as oem_codes
        from stokyonetimi s ";
        if ($type == "detail") {
            $q .= " 
            LEFT JOIN LINKEDTABLEPARTS L ON L.LA_ART_ID = s.id
            LEFT JOIN EXPORTADDITION_GAINFO E ON E.TTC_GA_ID = L.LA_GA_ID
            ";
        }
        $q .= " where
        s.no = {$productNo}
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
        $description = "";
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

    public function getPartBreadCrumbInfo($productID)
    {
        $q = "SELECT
        GA_ASSEMBLY as mainCategory,
        GA_STANDARD as subCategory,
        s.stokkodu as stockCode,
        s.stokmarka as partBrand,
        s.stokkodu_ismail as supplierCode,
        s.stokadi as partName
        FROM LINKEDTABLEPARTS L
        JOIN EXPORTADDITION_GAINFO E ON E.TTC_GA_ID = L.LA_GA_ID
        JOIN stokyonetimi s on s.id = L.LA_ART_ID
        WHERE
        L.LA_ART_ID = {$productID}
        LIMIT 1";

        return $this->db->query($q)->getResult();
    }

    public function getBestSellerList()
    {
        $q = "
        select
        s.no
        from manuel_sepet ms 
        join stokyonetimi s on s.stokkodu_ismail = ms.stok_kodu
        join siparisyonetimi sy on sy.id = ms.siparisid
        where
        sy.siparistarihi > DATE_SUB(now(), INTERVAL 6 MONTH)
        group by s.no
        order by 1 desc limit 10
        ";

        return $this->db->query($q)->getResult();
    }

    public function getSimilarProducts($categoryID)
    {
        $q = "SELECT
        s.no
        FROM LINKEDTABLEPARTS L
        JOIN EXPORTADDITION_GAINFO E ON E.TTC_GA_ID = L.LA_GA_ID
        JOIN stokyonetimi s on s.id = L.LA_ART_ID
        WHERE
        L.LA_GA_ID = {$categoryID}
        and s.status = 1
        and s.stokfiyati > 0
        and L.disable = 0
        and ifnull(E.durum,0) = 1
        LIMIT 10";

        return $this->db->query($q)->getResult();
    }

    /*
        Amortisör alana amortisör takozu
        Filtre alana motor yağı
        Triger seti alana devirdaim
        Fren balatası alana balata fişi
        Ateşleme bobini alana buji
    */
    public function getRecomendedProducts($categoryID)
    {
        $arrBaseCategories = [
            [
                "base" => [7, 8, 9, 424, 4055, 4682],
                "recomend_type" => "oil",
                "recomend" => [3],
            ],
            [
                "base" => [306, 307],
                "recomend_type" => "part",
                "recomend" => [34, 999, 1260, 1351],
            ],
            [
                "base" => [281, 402, 1419, 1537, 3371, 3405],
                "recomend_type" => "part",
                "recomend" => [407, 3403],
            ],
            [
                "base" => [689, 2284, 41309],
                "recomend_type" => "part",
                "recomend" => [686, 696, 698, 699],
            ],
        ];

        foreach ($arrBaseCategories as $key => $cat) {
            if (in_array($categoryID, $cat["base"])) {
                $recInfo = $cat;
            }
        }

        if (!empty($recInfo)) {
            $q =
                "
            select
            s.no
            from LINKEDTABLEPARTS L 
            join stokyonetimi s on s.id = L.LA_ART_ID
            where
            L.LA_GA_ID in (" .
                implode(",", $recInfo["recomend"]) .
                ") 
            and L.disable = 0
            and s.status = 1
            and s.stokdurumu = 1
            and s.stokfiyati > 0
            order by s.stokfiyati asc
            limit 10
            ";

            return $this->db->query($q)->getResult();
        }
        return false;
    }

    public function getFreeShippingProducts()
    {
        $q = "
        select
        no,
        stokfiyati
        from stokyonetimi 
        where
        ifnull(bedava_kargo,0) = 1
        and status = 1
        and stokdurumu = 1
        and stokfiyati > 1
        order by stokfiyati
        ";

        return $this->db->query($q)->getResult();
    }

    public function getRepairCarBrands()
    {
        $q = "
        select
        marka as car_brand
        from 
        bakim_paketi 
        group by marka
        ";

        return $this->db->query($q)->getResult();
    }

    public function getRepairCarModels($carBrand)
    {
        $q = "
        select
        model as car_model
        from 
        bakim_paketi 
        where 
        marka = '{$carBrand}'
        group by model
        ";

        return $this->db->query($q)->getResult();
    }

    public function getRepairCarEngines($carBrand, $carModel)
    {
        $q = "
        select
        motor_tipi as engine
        from 
        bakim_paketi 
        where 
        marka = '{$carBrand}'
        and model = '{$carModel}'
        group by motor_tipi
        ";

        return $this->db->query($q)->getResult();
    }

    public function getRepairPacks($carBrand, $carModel, $engine, $km)
    {
        $arrResult = [];
        $q =
            "
        select 
        if(b.parca_marka = 'OEMparts', 0, 1) as sort, 
        if(b.urun_adi = 'MOTOR YAĞI', myag.no, 'empty') as product_no, 
        if(b.urun_adi = 'MOTOR YAĞI', myag.stokkodu_ismail, 'empty') as supplier_code, 
        if(b.urun_adi = 'MOTOR YAĞI', myag.stokkodu,  'empty') as stock_code, 
        if(b.urun_adi = 'MOTOR YAĞI', myag.gelisfiyati, 'empty') as cost_price, 
        if(b.urun_adi = 'MOTOR YAĞI', if(ifnull(listefiyati,0)=0,(stokfiyati*1.2),listefiyati), 'empty') as list_price, 
        (stokfiyati-ifnull(indirim_tl,0)+ifnull(zam_tl,0))*ifnull(if(indirim_zam_yuzde='',1,indirim_zam_yuzde),1) as sale_price,
        b.urun_adi as product_type, 
        if(b.urun_adi = 'MOTOR YAĞI', myag.stokdurumu, 'empty') as stock_status, 
        b.km, 
        b.oem_kod as oem_code, 
        b.marka_kodu as brand_code, 
        REPLACE(b.parca_marka, 'parts', '') as part_brand_name, 
        concat('/Brand_logos/', m.id, '.jpg') as brand_logo, 
        if( b.urun_adi = 'MOTOR YAĞI', myag.urunAdi, 'empty') as product_name 
        from bakim_paketi b
        join stokyonetimi myag on myag.no = b.motor_yag_no  
        join TABLEBRANDS m on m.BRAND = b.parca_marka
        where 
        b.marka = '" .
            $carBrand .
            "'
        and b.model = '" .
            $carModel .
            "'
        and b.motor_tipi = '" .
            $engine .
            "'
        and mod(round((" .
            $km .
            "/b.bakim_aralik))*b.bakim_aralik,b.km) = 0
        and myag.status = 1
        and myag.stokfiyati > 0
        group by b.id, b.parca_marka
        order by sort, b.parca_marka";

        $arrPacks = $this->db->query($q)->getResult();
        if ($arrPacks) {
            $index = 1;
            $totalPrice = 0;
            $arrBrands = [];
            foreach ($arrPacks as $key => $item) {
                $salePrice = 0;
                if ($item->product_no == "empty") {
                    $query = "
                    select
                    no product_no,
                    urunAdi product_name,
                    stokkodu_ismail supplier_code,
                    stokkodu stock_code,
                    gelisfiyati cost_price,
                    if(ifnull(listefiyati,0)=0,(stokfiyati*1.2),listefiyati) as list_price,
                    (stokfiyati-ifnull(indirim_tl,0)+ifnull(zam_tl,0))*ifnull(if(indirim_zam_yuzde='',1,indirim_zam_yuzde),1) as sale_price,
                    stokdurumu as stock_status
                    from stokyonetimi
                    where 
                    ifnull(status,0) = 1
                        ";
                    if ($item->part_brand_name != "OEM") {
                        $query .= " 
                            and match (ART_CROSS) against ('{$item->oem_code}' in natural language mode)
                            and match (stokmarka) against ('{$item->part_brand_name}' in natural language mode) ";
                    } else {
                        $query .= " 
                        and stokmarka = '{$item->part_brand_name}' 
                        and stokkodu = '{$item->brand_code}'
                        ";
                    }
                    $query .= "
                    and ifnull(stokfiyati,0) > 0
                    and status = 1
                    order by stokdurumu desc, stokfiyati asc
                    limit 1;
                    ";

                    $part = $this->db->query($query)->getResult();
                    if ($part) {
                        $part = $part[0];
                        if (!is_null($part->product_no)) {
                            $arrResult[$item->part_brand_name]["products"][] = [
                                "pack_id" => $item->part_brand_name . "_" . $part->product_no,
                                "product_no" => $part->product_no,
                                "product_name" => $part->product_name,
                                "stock_code" => $part->supplier_code,
                                "sale_price" => $part->sale_price,
                                "stock_status" => $part->stock_status,
                                "checked" => false,
                            ];
                            $salePrice = $part->sale_price;
                        }
                    }
                } else {
                    if (!is_null($item->product_no)) {
                        $arrResult[$item->part_brand_name]["products"][] = [
                            "pack_id" => $item->part_brand_name . "_" . $item->product_no,
                            "product_no" => $item->product_no,
                            "product_name" => $item->product_name,
                            "stock_code" => $item->supplier_code,
                            "sale_price" => $item->sale_price,
                            "stock_status" => $item->stock_status,
                            "checked" => false,
                        ];
                        $salePrice = $item->sale_price;
                    }
                }

                $arrResult[$item->part_brand_name]["brand_name"] =
                    $item->part_brand_name .
                    ($item->part_brand_name == "OEM" ? "(Logolu Orjinal)" : "(Logosuz Orjinal)") .
                    " Bakım Paketi";
                $arrResult[$item->part_brand_name]["brand_label"] = $item->part_brand_name;
                $arrResult[$item->part_brand_name]["logo"] = $item->brand_logo;
                $totalPrice += $salePrice;
                $arrResult[$item->part_brand_name]["total_price"] = $totalPrice;
                $index++;
            }

            return ["pack_count" => count($arrResult), "packs" => $arrResult];
        }
        return false;
    }
}
