<?php namespace App\Controllers;

use aloparca;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UrlControlModel;
use App\Models\ProductsModel;

class UrlControl extends ResourceController
{
    use ResponseTrait;

    public function RedirectUri()
    {
        helper("aloparca");
        $url = $this->request->getVar("url");
        $cacheKey = "uri_redirect:" . $url;
        $arrResult = cache($cacheKey);
        if (empty($arrResult)) {
            $model = new UrlControlModel();
            //301 Yönlendirme kontrolü yapılıyor
            $redirectControl = $model->redirectContol($url);
            if ($redirectControl) {
                $arrResult["redirect_uri"] = $redirectControl;
            } else {
                $arrUrl = explode("/", $url);
                if ($arrUrl) {
                    switch ($arrUrl[1]) {
                        case "oto-yedek-parca":
                            $arrResult = $model->parseSparePartUri($arrUrl, $url);
                            break;
                        case "marka":
                            $arrResult = $model->parsePartBrandUri($arrUrl, $url);
                            break;
                        case "madeni-yaglar":
                            $arrResult = [
                                "title" =>
                                    "Motor Yağı - Motor Yağı Fiyatları - Madeni Yağ | Aloparca.com",
                                "description" =>
                                    "Motor yağı. Motor yağı fiyatları. Motor yağları çeşitleri. Madeni yağlar. Motor yağı markaları. En iyi motor yağları. En uygun ve ucuz araba motor yağları.",
                                "cannonical" => "https://www.aloparca.com" . $url,
                            ];
                            break;
                        case "otoaksesuar":
                            $arrResult = $model->parseAccessoriesUri($arrUrl, $url);
                            break;
                        case "kampanyali-urunler":
                            $arrResult = $model->parseCampainsUri($arrUrl, $url);
                            break;
                        case "blog":
                            $arrResult = $model->parseBlogUri($arrUrl, $url);
                            break;
                        case "blog_detay":
                            $arrResult = $model->parseBlogUri($arrUrl, $url, "detail");
                            break;
                        default:
                            $arrResult = [
                                "title" => "Oto Yedek Parça | Aloparca.com",
                                "description" =>
                                    "Tüm otomobil markalarının tüm araba parçalarını bulabileceğiniz, Türkiyenin en büyük online yedek parça satışı sitesi. Orijinal yedek parça fiyatları ve yan sanayi yedek parça fiyatları online yedek parça listemizde sizi bekliyor. En uygun fiyat ve garantili oto yedek parça sitesi Aloparça.",
                                "cannonical" => "https://www.aloparca.com" . $url,
                            ];
                            break;
                    }
                }
            }
        }
        if ($arrResult) {
            $response = [
                "status" => 201,
                "error" => false,
                "result" => $arrResult,
            ];
            return $this->respond($response);
        } else {
            $response = [
                "status" => 201,
                "error" => true,
                "messages" => [
                    "success" => "No result found",
                ],
            ];
            return $this->respond($response);
        }
    }
}
