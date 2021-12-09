<?php
class aloparca
{
    public static function validUrl($urlStr, string $divider = "-")
    {
        $arrUrl = explode("/", $urlStr);
        $returnStr = "";
        foreach ($arrUrl as $key => $text) {
            if ($text != "") {
                $text = preg_replace("~[^\pL\d]+~u", $divider, $text);
                $text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
                $text = preg_replace("~[^-\w]+~", "", $text);
                $text = trim($text, $divider);
                $text = preg_replace("~-+~", $divider, $text);
                $text = strtolower($text);
                $returnStr .= "/" . $text;
            }
        }

        if ($returnStr == "") {
            return "n-a";
        }

        return $returnStr;
    }

    public static function calculateInstallment($price)
    {
        $arrRet = [];
        $arrRet[0]["installment_count"] = 2;
        $arrRet[0]["price"] = (float) number_format(
            number_format($price * 1.051, 2, ".", "") / 2,
            2,
            ".",
            ""
        );
        $arrRet[1]["installment_count"] = 3;
        $arrRet[1]["price"] = (float) number_format(
            number_format($price * 1.063, 2, ".", "") / 3,
            2,
            ".",
            ""
        );
        $arrRet[2]["installment_count"] = 4;
        $arrRet[2]["price"] = (float) number_format(
            number_format($price * 1.076, 2, ".", "") / 4,
            2,
            ".",
            ""
        );
        $arrRet[3]["installment_count"] = 5;
        $arrRet[3]["price"] = (float) number_format(
            number_format($price * 1.09, 2, ".", "") / 5,
            2,
            ".",
            ""
        );
        $arrRet[4]["installment_count"] = 6;
        $arrRet[4]["price"] = (float) number_format(
            number_format($price * 1.11, 2, ".", "") / 6,
            2,
            ".",
            ""
        );
        $arrRet[5]["installment_count"] = 7;
        $arrRet[5]["price"] = (float) number_format(
            number_format($price * 1.12, 2, ".", "") / 7,
            2,
            ".",
            ""
        );
        $arrRet[6]["installment_count"] = 8;
        $arrRet[6]["price"] = (float) number_format(
            number_format($price * 1.13, 2, ".", "") / 8,
            2,
            ".",
            ""
        );
        $arrRet[7]["installment_count"] = 9;
        $arrRet[7]["price"] = (float) number_format(
            number_format($price * 1.15, 2, ".", "") / 9,
            2,
            ".",
            ""
        );

        $arrRet["cards"][] = "bonus";
        $arrRet["cards"][] = "world";
        $arrRet["cards"][] = "cardfinans";
        $arrRet["cards"][] = "axess";
        $arrRet["cards"][] = "maximum";
        $arrRet["cards"][] = "paraf";

        return $arrRet;
    }

    public static function parseOemCodes($oemCodes)
    {
        $arrResult = [];
        if ($oemCodes) {
            $oemCodes = explode("\n", $oemCodes);
            $arrOem = array_unique($oemCodes);
            foreach ($arrOem as $oem) {
                $oemItem = explode("[=>]", $oem);
                if (@$oemItem[1]) {
                    if (@$oemItem[0]) {
                        $arrResult[] = ["brand" => $oemItem[0], "code" => $oemItem[1]];
                    }
                }
            }
        }
        return $arrResult;
    }
}
?>
