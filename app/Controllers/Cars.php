<?php namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CarsModel;


class Cars extends ResourceController
{
    use ResponseTrait;

    public function Brands()
    {
        $model = new CarsModel();
        $arrResult = cache('car_brands');
        
        if(empty($arrResult)){
            $arrResult = array();
            $arrBrands = $model->getCarBrands();
            if($arrBrands){
                foreach ($arrBrands as $key => $item) {
                    $arrResult[] = array('name' => $item->name, 'slug' => aloparca::validUrl($item->name, '_'));
                }
            }
            //cache()->save($arrResult, 'car_brands',604800);
        }


        if ($arrResult) {
            $response = [
                'status'    => 201,
                'error'     => null,
                'result'      => $arrResult
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'No result found'
                ]
            ];
            return $this->respond($response);
        }
    }
    
    public function Models($brand)
    {
        helper('aloparca');
        $cacheKey = 'car_models:'.$brand;
        $arrResult = cache($cacheKey);
        $model = new CarsModel();
        if(empty($arrResult)){
            $arrModels = $model->getCarModels(str_replace('_',' ',$brand));
            $arrResult = array();
            if($arrModels){
                foreach ($arrModels as $key => $item) {
                    $arrResult[] = array('name' => $item->name, 'slug' => aloparca::validUrl($item->name, '_'), 'featured' => (int)$item->futured);
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }


        if ($arrResult) {
            $response = [
                'status'    => 201,
                'error'     => null,
                'result'      => $arrResult
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'No result found'
                ]
            ];
            return $this->respond($response);
        }
    }

    public function Bodies($brand, $carModel)
    {
        helper('aloparca');
        $cacheKey = 'car_bodies:'.$brand.':'.$carModel;
        $arrResult = cache($cacheKey);
        $model = new CarsModel();
        if(empty($arrResult)){
            $arrResult = array();
            $arrBrands = $model->getCarBodies($brand, $carModel);
            if($arrBrands){
                foreach ($arrBrands as $key => $item) {
                    $arrResult[] = array('name' => $item->name, 'slug' => aloparca::validUrl($item->name, '_'));
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }


        if ($arrResult) {
            $response = [
                'status'    => 201,
                'error'     => null,
                'result'      => $arrResult
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'No result found'
                ]
            ];
            return $this->respond($response);
        }
    }

    public function ModelYears($brand, $carModel, $body)
    {
        
        $cacheKey = 'car_model_years:'.$brand.':'.$carModel.':'.$body;
        $arrResult = cache($cacheKey);
        $model = new CarsModel();
        if(empty($arrResult)){
            $arrResult = $model->getCarModelYears($brand, $carModel, $body);
            //cache()->save($arrResult, $cacheKey,604800);
        }


        if ($arrResult) {
            $response = [
                'status'    => 201,
                'error'     => null,
                'result'      => $arrResult
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'No result found'
                ]
            ];
            return $this->respond($response);
        }
    }

    public function Engines($brand, $carModel, $body, $modelYear)
    {
        helper('aloparca');
        $cacheKey = 'car_engines:'.$brand.':'.$carModel.':'.$body.':'.$modelYear;
        $arrResult = cache($cacheKey);
        $model = new CarsModel();
        if(empty($arrResult)){
            $arrResult = array();
            $arrEngines = $model->getCarEngines($brand, $carModel, $body, $modelYear);
            if($arrEngines){
                foreach ($arrEngines as $key => $item) {
                    $arrResult[] = array('name' => $item->name, 'slug' => aloparca::validUrl($item->name, '_'));
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }


        if ($arrResult) {
            $response = [
                'status'    => 201,
                'error'     => null,
                'result'      => $arrResult
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'No result found'
                ]
            ];
            return $this->respond($response);
        }
    }

    public function Kw($brand, $carModel, $body, $modelYear, $engine)
    {
        helper('aloparca');
        $cacheKey = 'car_kw:'.$brand.':'.$carModel.':'.$body.':'.$modelYear.':'.$engine;
        $arrResult = cache($cacheKey);
        $model = new CarsModel();
        if(empty($arrResult)){
            $arrResult = array();
            $arrKw = $model->getCarKw($brand, $carModel, $body, $modelYear, $engine);
            if($arrKw){
                foreach ($arrKw as $key => $item) {
                    $arrResult[] = array('name' => $item->name, 'slug' => aloparca::validUrl($item->name, '_'));
                }
            }
            //cache()->save($arrResult, $cacheKey,604800);
        }


        if ($arrResult) {
            $response = [
                'status'    => 201,
                'error'     => null,
                'result'      => $arrResult
            ];
            return $this->respond($response);
        } else {
            $response = [
                'status'   => 201,
                'error'    => null,
                'messages' => [
                    'success' => 'No result found'
                ]
            ];
            return $this->respond($response);
        }
    }


}