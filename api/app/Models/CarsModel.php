<?php

namespace App\Models;

use CodeIgniter\Model;

class CarsModel extends Model
{
    public function getCarBrands()
    {
        helper("aloparca");
        $q = "
        SELECT
        DISTINCT CAR_BRANDS as name
        FROM TABLECARS 
        WHERE disable = 0
        ORDER BY CAR_BRANDS
        ";
        return $this->db->query($q)->getResult();
    }

    public function getCarModels(string $brand)
    {
        $q = "
        SELECT
        DISTINCT MODEL_CAR as name, futured
        FROM TABLECARS 
        WHERE disable = 0
        AND CAR_BRANDS = ?
        ORDER BY MODEL_CAR
        ";
        return $this->db->query($q, [$brand])->getResult();
    }

    public function getCarBodies(string $brand, string $model)
    {
        $q = "
        SELECT
        DISTINCT BODY_TYPE as name
        FROM TABLECARS 
        WHERE disable = 0
        AND CAR_BRANDS = ?
        AND MODEL_CAR = ?
        ORDER BY BODY_TYPE
        ";
        return $this->db->query($q, [$brand, $model])->getResult();
    }

    public function getCarModelYears(string $brand, string $model, string $body)
    {
        $q = "
        SELECT
        MIN(OF_THE_YEAR) AS start_year,
        MAX(IFNULL(UP_TO_A_YEAR,YEAR(CURDATE()))) AS end_year
        FROM TABLECARS 
        WHERE disable = 0
        AND CAR_BRANDS = ?
        AND MODEL_CAR = ?
        AND BODY_TYPE = ?
        ";
        return $this->db->query($q, [$brand, $model, $body])->getResult();
    }

    public function getCarEngines(string $brand, string $model, string $body, int $modelYear)
    {
        $q = "
        SELECT
        DISTINCT TYP_CAR as name
        FROM TABLECARS 
        WHERE disable = 0
        AND CAR_BRANDS = ?
        AND MODEL_CAR = ?
        AND BODY_TYPE = ?
        AND OF_THE_YEAR <= ?
        AND IFNULL(UP_TO_A_YEAR,YEAR(CURDATE())) >= ?
        ORDER BY TYP_CAR
        ";
        return $this->db->query($q, [$brand, $model, $body, $modelYear])->getResult();
    }

    public function getCarKw(string $brand, string $model, string $body, int $modelYear, string $engine)
    {
        $q = "
        SELECT
        DISTINCT KV as name
        FROM TABLECARS 
        WHERE disable = 0
        AND CAR_BRANDS = ?
        AND MODEL_CAR = ?
        AND BODY_TYPE = ?
        AND OF_THE_YEAR <= ?
        AND IFNULL(UP_TO_A_YEAR,YEAR(CURDATE())) >= ?
        AND LOWER(REPLACE(REPLACE(REPLACE(TYP_CAR, '.', '_'), ',', '_'), ' ', '_')) = ?
        ORDER BY KV
        ";
        return $this->db->query($q, [$brand, $model, $body, $modelYear, $engine])->getResult();
    }
}
