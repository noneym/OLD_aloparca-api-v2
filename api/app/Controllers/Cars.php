<?php

namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CarsModel;

/**
 * @property CarsModel $model
 */
class Cars extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\CarsModel';
    protected $format = 'json';

    public function __construct()
    {
        helper("aloparca");
    }

    public function Brands()
    {
        $arrResult = cache("car_brands");

        if ($arrResult === null) {
            $arrResult = [];
            $arrBrands = $this->model->getCarBrands();
            if ($arrBrands) {
                foreach ($arrBrands as $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::asValidURL($item->name),
                    ];
                }
            }
            cache()->save("car_brands", $arrResult, 1 * WEEK);
        }

        return $this->respond($arrResult);
    }

    public function Models(string $brand)
    {
        $brand = aloparca::slugify($brand);

        $cacheKey = "car_models-$brand";
        $arrResult = cache($cacheKey);

        if ($arrResult === null) {
            $databaseBrand = strtoupper(str_replace("_", " ", $brand));
            $arrModels = $this->model->getCarModels($databaseBrand);
            $arrResult = [];
            if ($arrModels) {
                foreach ($arrModels as $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::asValidURL($item->name),
                        "featured" => ((int) $item->futured) === 1,
                    ];
                }
            }

            cache()->save($cacheKey, $arrResult, 1 * WEEK);
        }

        if (empty($arrResult)) {
            return $this->failNotFound("No models found");
        }

        return $this->respond($arrResult);
    }

    public function Bodies(string $brand, string $carModel)
    {
        $brand = aloparca::slugify($brand);
        $carModel = aloparca::slugify($carModel);

        $cacheKey = "car_bodies-$brand-$carModel";
        $arrResult = cache($cacheKey);

        if ($arrResult === null) {
            $arrResult = [];

            $databaseBrand = strtoupper(str_replace("_", " ", $brand));
            $databaseModel = strtoupper(str_replace("_", " ", $carModel));
            $arrBodies = $this->model->getCarBodies($databaseBrand, $databaseModel);
            if ($arrBodies) {
                foreach ($arrBodies as $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::asValidURL($item->name),
                    ];
                }
            }
            cache()->save($cacheKey, $arrResult, 1 * WEEK);
        }

        dd($arrResult);

        if (empty($arrResult)) {
            return $this->failNotFound('No bodies found');
        }

        return $this->respond($arrResult);
    }

    public function ModelYears(string $brand, string $carModel, string $body)
    {
        $brand = aloparca::slugify($brand);
        $carModel = aloparca::slugify($carModel);
        $body = aloparca::slugify($body);

        $cacheKey = "car_model_years-$brand-$carModel-$body";
        $arrResult = cache($cacheKey);

        if ($arrResult === null) {
            $arrResult = $this->model->getCarModelYears($brand, $carModel, $body);
            cache()->save($cacheKey, $arrResult, 1 * WEEK);
        }

        if (empty($arrResult)) {
            return $this->failNotFound('No years found');
        }

        return $this->respond($arrResult);
    }

    public function Engines(string $brand, string $carModel, string $body, string $modelYear)
    {
        $brand = aloparca::slugify($brand);
        $carModel = aloparca::slugify($carModel);
        $body = aloparca::slugify($body);
        $modelYear = aloparca::asInteger($modelYear);

        if ($modelYear === null) {
            return $this->fail("Invalid model year");
        }

        $cacheKey = "car_engines-$brand-$carModel-$body-$modelYear";
        $arrResult = cache($cacheKey);

        if ($arrResult === null) {
            $arrResult = [];
            $arrEngines = $this->model->getCarEngines($brand, $carModel, $body, $modelYear);
            if ($arrEngines) {
                foreach ($arrEngines as $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::asValidURL($item->name),
                    ];
                }
            }
            cache()->save($cacheKey, $arrResult, 1 * WEEK);
        }

        if (empty($arrResult)) {
            return $this->failNotFound('No engines found');
        }

        return $this->respond($arrResult);
    }

    public function Kw($brand, $carModel, $body, $modelYear, $engine)
    {
        $brand = aloparca::slugify($brand);
        $carModel = aloparca::slugify($carModel);
        $body = aloparca::slugify($body);
        $modelYear = aloparca::asInteger($modelYear);
        $engine = aloparca::slugify($engine);

        if ($modelYear === null) {
            return $this->fail("Invalid model year");
        }

        $cacheKey = "car_kw-$brand-$carModel-$body-$modelYear-$engine";
        $arrResult = cache($cacheKey);

        if ($arrResult === null) {
            $arrResult = [];
            $arrKw = $this->model->getCarKw($brand, $carModel, $body, $modelYear, $engine);
            if ($arrKw) {
                foreach ($arrKw as $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::asValidURL($item->name),
                    ];
                }
            }

            cache()->save($cacheKey, $arrResult, 1 * WEEK);
        }

        if (empty($arrResult)) {
            return $this->failNotFound("Engine power options not found");
        }

        return $this->respond($arrResult);
    }
}
