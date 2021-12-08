<?php namespace App\Models;
  
use CodeIgniter\Model;
 
class CarsModel extends Model
{
    public function getCarBrands()
    {
        helper('aloparca'); 
        $q = "
        SELECT
        CAR_BRANDS as name
        FROM TABLECARS 
        WHERE
        disable = 0
        GROUP BY CAR_BRANDS
        ORDER BY CAR_BRANDS
        ";
        return $this->db->query($q)->getResult();
    }

    public function getCarModels($brand)
    {
        $q = "
        SELECT
        MODEL_CAR as name,
        futured
        FROM TABLECARS 
        WHERE
        disable = 0
        AND CAR_BRANDS = '{$brand}'
        GROUP BY MODEL_CAR
        ORDER BY MODEL_CAR
        ";
        return $this->db->query($q)->getResult();
    }

    public function getCarBodies($brand, $model)
    {
        $q = "
        SELECT
        BODY_TYPE as name
        FROM TABLECARS 
        WHERE
        disable = 0
        AND CAR_BRANDS = '{$brand}'
        AND MODEL_CAR = '{$model}'
        GROUP BY BODY_TYPE
        ORDER BY BODY_TYPE
        ";
        return $this->db->query($q)->getResult();
    }

    public function getCarModelYears($brand, $model, $body)
    {
        $q = "
        SELECT
        MIN(OF_THE_YEAR) AS start_year,
        MAX(IFNULL(UP_TO_A_YEAR,YEAR(CURDATE()))) AS end_year
        FROM TABLECARS 
        WHERE
        disable = 0
        AND CAR_BRANDS = '{$brand}'
        AND MODEL_CAR = '{$model}'
        AND BODY_TYPE = '{$body}'
        ";
        return $this->db->query($q)->getResult();
    }

    public function getCarEngines($brand, $model, $body, $modelYear)
    {
        $q = "
        SELECT
        TYP_CAR as name
        FROM TABLECARS 
        WHERE
        disable = 0
        AND CAR_BRANDS = '{$brand}'
        AND MODEL_CAR = '{$model}'
        AND BODY_TYPE = '{$body}'
        AND OF_THE_YEAR <= '{$modelYear}'
        AND IFNULL(UP_TO_A_YEAR,YEAR(CURDATE())) >= '{$modelYear}'
        GROUP BY TYP_CAR 
        ORDER BY MODEL_CAR, BODY_TYPE, TYP_CAR 
        ";
        return $this->db->query($q)->getResult();
    }

    public function getCarKw($brand, $model, $body, $modelYear, $engine)
    {
        $q = "
        SELECT
        KV as name
        FROM TABLECARS 
        WHERE
        disable = 0
        AND CAR_BRANDS = '{$brand}'
        AND MODEL_CAR = '{$model}'
        AND BODY_TYPE = '{$body}'
        AND OF_THE_YEAR <= '{$modelYear}'
        AND IFNULL(UP_TO_A_YEAR,YEAR(CURDATE())) >= '{$modelYear}'
        AND LOWER(REPLACE(REPLACE(REPLACE(TYP_CAR, '.', '_'), ',', '_'), ' ', '_'))  = '{$engine}'
        GROUP BY KV 
        ORDER BY MODEL_CAR, BODY_TYPE, TYP_CAR, KV
        ";
        return $this->db->query($q)->getResult();
    }

    
}