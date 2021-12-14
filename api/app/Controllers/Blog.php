<?php namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\BlogModel;
use App\Models\ProductsModel;

class Blog extends ResourceController
{
    use ResponseTrait;

    public function index()
    {
        $model = new BlogModel();
        $arrResult = $model
            ->where("status", 1)
            ->orderBy("id", "DESC")
            ->findAll();
        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
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

    public function PostList($catID = 0, $populer = 0, $limit = 0)
    {
        $model = new BlogModel();
        $arrResult = $model->getPostList($catID, $populer, $limit);
        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
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

    public function Detail($itemID)
    {
        helper("aloparca");
        $model = new BlogModel();
        $productModel = new ProductsModel();
        $arrResult = [];
        $arrDbResult = $model->getDetail($itemID);
        if ($arrDbResult) {
            $arrResult["detail"] = $arrDbResult["detail"];
            $arrResult["products"] = [];
            if (!empty($arrDbResult["products"])) {
                foreach ($arrDbResult["products"] as $key => $item) {
                    $prod = $productModel->getProductDetail($item->no);
                    if ($prod) {
                        $prod = $prod[0];
                        $ttt = aloparca::renderProductDetail($prod, "list");
                        $arrResult["products"][] = $ttt;
                    }
                }
            }
        }
        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => null,
                "result" => $arrResult,
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
}
