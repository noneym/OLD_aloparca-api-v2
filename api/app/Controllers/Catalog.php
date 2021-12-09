<?php namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CatalogModel;

/**
 * @property CatalogModel $model
 */
class Catalog extends ResourceController
{
    use ResponseTrait;

    public function __construct()
    {
        helper("aloparca");
        $this->setModel(new CatalogModel());
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

    public function PartBrands()
    {
        $limit = (int) ($this->request->getGet("limit") ?? 0);
        $featured = $this->request->getGet("featured") !== null ? 1 : 0;

        $cacheKey = "part_brands-limit=$limit-featured=$featured";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $this->model->getPartBrands($limit, $featured);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    if (strlen($item->name) > 2) {
                        $arrResult[] = [
                            "name" => $item->name,
                            "slug" => aloparca::validUrl($item->name, "-"),
                            "featured" => (int) $item->futured,
                            "product_count" => (int) $item->product_count,
                            "logo" =>
                                $item->bra_id == 0 ? null : "/Brand_logos/{$item->bra_id}.jpg",
                        ];
                    }
                }
            }

            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function PartCategories()
    {
        $cacheKey = "part_categories";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $this->model->getPartCategories();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $prodControl = $this->model->prodStockControlForCategory($item->partLinkID);

                    if ($prodControl) {
                        $catUrl = aloparca::validUrl($item->mainCatName);
                        $subcatUrl = aloparca::validUrl($item->subCatName);
                        $arrResult[$catUrl]["category_info"] = [
                            "name" => (string) $item->mainCatName,
                            "slug" => (string) $catUrl,
                        ];
                        $arrResult[$catUrl]["sub_categories"][] = [
                            "name" => (string) $item->subCatName,
                            "slug" => (string) $subcatUrl,
                        ];
                    }
                }
            }
            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function AccesoriesCategories()
    {
        $cacheKey = "acc_categories:";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $this->model->getAccCategories();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $arrResult[] = [
                        "name" => (string) $item->name,
                        "slug" => (string) aloparca::validUrl($item->name),
                    ];
                }
            }

            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function MineralOilCategories()
    {
        $cacheKey = "mineral_oils_categories";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $this->model->getOilCategories();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $catUrl = aloparca::validUrl($item->mainCatName);
                    $subcatUrl = aloparca::validUrl($item->subCatName);
                    $arrResult[$item->mainCatID]["category_info"] = [
                        "id" => (int) $item->mainCatID,
                        "name" => (string) $item->mainCatName,
                        "slug" => (string) $catUrl,
                    ];
                    $arrResult[$item->mainCatID]["sub_categories"][] = [
                        "name" => (string) $item->subCatName,
                        "slug" => (string) $subcatUrl,
                    ];
                }
            }
            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResponse);
    }

    public function CampaignList()
    {
        $cacheKey = "campaign_categories";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $this->model->getCampainCategories();
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    if (strlen($item->campain_name) > 0 && strlen($item->campain_type) > 0) {
                        $campaignName = "{$item->campain_name} {$item->campain_type} Kampanyası";
                        $arrResult[] = [
                            "name" => (string) ucwords(strtolower($campaignName)),
                            "slug" => (string) aloparca::validUrl(
                                "{$item->campain_name}_{$item->campain_type}"
                            ),
                        ];
                    }
                }
            }

            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }

    public function PartBrandCategories($brandName)
    {
        $cacheKey = "partbrand_categories-$brandName";
        $arrResult = cache($cacheKey);

        if (empty($arrResult)) {
            $arrResult = [];
            $arrDbResult = $this->model->getPartBrandCategories($brandName);
            if ($arrDbResult) {
                foreach ($arrDbResult as $key => $item) {
                    $arrResult[] = [
                        "name" => (string) ucwords(strtolower($item->category_name)),
                        "slug" =>
                            (string) aloparca::validUrl($brandName) .
                            aloparca::validUrl($item->category_name),
                    ];
                }
            }
            cache()->save($cacheKey, $arrResult, 604800);
        }

        return $this->respondWith($arrResult);
    }
}
