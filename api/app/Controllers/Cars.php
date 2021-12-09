<?php namespace App\Controllers;

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

    public function __construct()
    {
        helper("aloparca");
        $this->setModel(new CarsModel());
    }

    private function respondWith($result)
    {
        if ($result) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $result,
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

    public function Brands()
    {
        $arrResult = cache("car_brands");

        if ($arrResult === null) {
            $arrResult = [];
            $arrBrands = $this->model->getCarBrands();
            if ($arrBrands) {
                foreach ($arrBrands as $key => $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::validUrl($item->name, "_"),
                    ];
                }
            }
            cache()->save("car_brands", $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function Models($brand)
    {
        $cacheKey = "car_models-$brand";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrModels = $this->model->getCarModels(str_replace("_", " ", $brand));
            $arrResult = [];
            if ($arrModels) {
                foreach ($arrModels as $key => $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::validUrl($item->name, "_"),
                        "featured" => (int) $item->futured,
                    ];
                }
            }

            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function Bodies($brand, $carModel)
    {
        $cacheKey = "car_bodies-$brand-$carModel";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrBodies = $this->model->getCarBodies($brand, $carModel);
            if ($arrBodies) {
                foreach ($arrBodies as $key => $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::validUrl($item->name, "_"),
                    ];
                }
            }
            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function ModelYears($brand, $carModel, $body)
    {
        $cacheKey = "car_model_years-$brand-$carModel-$body";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = $this->model->getCarModelYears($brand, $carModel, $body);
            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function Engines($brand, $carModel, $body, $modelYear)
    {
        $cacheKey = "car_engines-$brand-$carModel-$body-$modelYear";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrEngines = $this->model->getCarEngines($brand, $carModel, $body, $modelYear);
            if ($arrEngines) {
                foreach ($arrEngines as $key => $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::validUrl($item->name, "_"),
                    ];
                }
            }
            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResponse);
    }

    public function Kw($brand, $carModel, $body, $modelYear, $engine)
    {
        $cacheKey = "car_kw-$brand-$carModel-$body-$modelYear-$engine";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrKw = $this->model->getCarKw($brand, $carModel, $body, $modelYear, $engine);
            if ($arrKw) {
                foreach ($arrKw as $key => $item) {
                    $arrResult[] = [
                        "name" => $item->name,
                        "slug" => aloparca::validUrl($item->name, "_"),
                    ];
                }
            }

            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($response);
    }
}
