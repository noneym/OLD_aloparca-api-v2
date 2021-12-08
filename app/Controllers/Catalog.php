<?php namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CatalogModel;


class Catalog extends ResourceController
{
    use ResponseTrait;

    public function PartBrands($limit = 0, $featured = 0)
    {
        helper('aloparca');
        $cacheKey = 'part_brands:'.$limit.':'.$featured;
        $arrResult = cache($cacheKey);
        if(empty($arrResult)){
            $model = new CatalogModel();
            $arrResult = array();
            $arrDbResult = $model->getPartBrands($limit, $featured);
            if($arrDbResult){
                foreach ($arrDbResult as $key => $item) {
                    if(strlen($item->name) > 2){
                        $arrResult[] = array(
                            'name'          => $item->name, 
                            'slug'          => aloparca::validUrl($item->name, '-'),
                            'featured'      => (int) $item->futured,
                            'product_count' => (int)$item->product_count,
                            'logo'          => ($item->bra_id ==  0 ? null : '/Brand_logos/'.$item->bra_id.'.jpg')
                        );
                    }
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

    public function PartCategories()
    {
        helper('aloparca');
        $cacheKey = 'part_categories:';
        $arrResult = cache($cacheKey);
        if(empty($arrResult)){
            $model = new CatalogModel();
            $arrResult = array();
            $arrDbResult = $model->getPartCategories();
            if($arrDbResult){
                foreach ($arrDbResult as $key => $item) {
                    $prodControl = $model->prodStockControlForCategory($item->partLinkID);  
                    if($prodControl){
                        $catUrl = aloparca::validUrl($item->mainCatName);
                        $subcatUrl = aloparca::validUrl($item->subCatName);
                        $arrResult[$catUrl]['category_info'] = array(
                            'name'      => (string) $item->mainCatName,
                            'slug'      => (string) $catUrl
                        );
                        $arrResult[$catUrl]['sub_categories'][] = array(
                            'name'      => (string) $item->subCatName,
                            'slug'      => (string) $subcatUrl
                        );
                    }
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

    public function AccesoriesCategories()
    {
        helper('aloparca');
        $cacheKey = 'acc_categories:';
        $arrResult = cache($cacheKey);
        if(empty($arrResult)){
            $model = new CatalogModel();
            $arrResult = array();
            $arrDbResult = $model->getAccCategories();
            if($arrDbResult){
                foreach ($arrDbResult as $key => $item) {
                    $arrResult[] = array(
                        'name'      => (string) $item->name,
                        'slug'      => (string) aloparca::validUrl($item->name)
                    );
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

    public function MineralOilCategories()
    {
        helper('aloparca');
        $cacheKey = 'mineral_oils_categories:';
        $arrResult = cache($cacheKey);
        if(empty($arrResult)){
            $model = new CatalogModel();
            $arrResult = array();
            $arrDbResult = $model->getOilCategories();
            if($arrDbResult){
                foreach ($arrDbResult as $key => $item) {
                    $catUrl = aloparca::validUrl($item->mainCatName);
                    $subcatUrl = aloparca::validUrl($item->subCatName);
                    $arrResult[$item->mainCatID]['category_info'] = array(
                        'id'        => (int) $item->mainCatID,
                        'name'      => (string) $item->mainCatName,
                        'slug'      => (string) $catUrl
                    );
                    $arrResult[$item->mainCatID]['sub_categories'][] = array(
                        'name'      => (string) $item->subCatName,
                        'slug'      => (string) $subcatUrl
                    );
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