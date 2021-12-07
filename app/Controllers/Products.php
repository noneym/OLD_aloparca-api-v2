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
        $carBrand       = '',
        $carModel       = '',
        $carBody        = '',
        $modelYear      = '',
        $engine         = '',
        $kw             = '',
        $partBrand       = '',
        $original       = '',
        $mainCategory   = '',
        $subCategory    = '',
        $page           = 0,
        $listType       = 0,
    )
    {
        helper('aloparca');
        $cacheKey = 'spare_part_list:'.$carBrand.
        ':'.$carModel.
        ':'.$carBody.
        ':'.$modelYear.
        ':'.$engine.
        ':'.$kw.
        ':'.$partBrand.
        ':'.$original.
        ':'.$mainCategory.
        ':'.$subCategory.
        ':'.$page.
        ':'.$listType;
        $arrResult = cache($cacheKey);
        if(empty($arrResult)){
            $model = new ProductsModel();
            $arrResult = array();
            $arrParams = array(
                'car_brand'         => $carBrand,
                'car_model'         => $carModel,
                'car_body'          => $carBody,
                'model_year'        => $modelYear,
                'engine'            => $engine,
                'kw'                => $kw,
                'part_brand'        => $partBrand,
                'original'          => $original,
                'main_category'     => $mainCategory,
                'sub_category'      => $subCategory,
                'list_type'         => $listType,
            );
            $arrDbResult = $model->getParts($arrParams);
            if($arrDbResult){
                foreach ($arrDbResult as $key => $item) {
                   
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