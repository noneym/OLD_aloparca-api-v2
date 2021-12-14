<?php namespace App\Models;

use CodeIgniter\Model;

class BlogModel extends Model
{
    public $table = "blog_kategori";
    protected $primaryKey = "id";
    protected $allowedFields = ["name", "slug", "hit"];

    public function getPostList($catID = 0, $populer = 0, $limit = 0)
    {
        $orderByPoperler = "";
        if ($populer > 0) {
            $orderByPoperler = " ,hit";
        }
        $catQuery = "";
        if ($catID > 0) {
            $catQuery = " and b.kategori_id =  " . $catID;
        }

        $q = "
			select 
                b.id as post_id,
				b.baslik as title,
                b.meta_baslik as meta_title,
				b.img,
				b.ozet as summery,
				b.url as slug,
				b.hit,
				b.yayin_tarihi as publish_date,
				k.name as category_name,
				k.slug as category_slug
			from blog_yazi b
			join blog_kategori k on k.id = b.kategori_id
			where 
			b.status = 1
			and b.yayin_tarihi<NOW()
			and k.status = 1
			$catQuery
			order by b.eklenme_tarihi $orderByPoperler  desc
		";

        if ($limit > 0) {
            $q .= " limit $limit ";
        }

        return $this->db->query($q)->getResult();
    }

    public function getDetail($blogID)
    {
        $q = "
			select 
				b.baslik as title,
                b.meta_baslik as meta_title,
				b.img,
				b.ozet as summery,
				b.url as slug,
				b.hit,
				b.yayin_tarihi as publish_date,
				k.name as category_name,
				k.slug as category_slug,
                b.icerik as post,
                b.query
			from blog_yazi b
			join blog_kategori k on k.id = b.kategori_id
			where 
			b.id = {$blogID}
            and b.status = 1
		";

        $result = $this->db->query($q)->getResult();
        if ($result) {
            $arrProds = [];
            $this->table = "blog_yazi";
            $this->where("id", $blogID)
                ->set("hit", (int) $result[0]->hit + 1)
                ->update();
            if (!is_null($result[0]->query)) {
                $arrProds = $this->getQueryProds($result[0]->query, 5);
            }
            return ["detail" => $result, "products" => $arrProds];
        }
        return false;
    }

    public function getQueryProds($query, $limit = 10)
    {
        $query = trim($query);
        $q = "
				select
				no
				from stokyonetimi 
				where 
				ifnull(status,0) = 1
				and ifnull(stokdurumu,0) = 1
				and ifnull(stokfiyati,0) > 0
				and urunAdi like '%{$query}%'
				and 
				(
					match (stokadi) against ('{$query}' IN BOOLEAN MODE) > 0
					or match (yeni_isim) against ('{$query}' IN BOOLEAN MODE) > 0
					or match (urunAdi) against ('{$query}' IN BOOLEAN MODE) > 0
				)
				order by stokfiyati 
				limit {$limit} ";

        $result = $this->db->query($q)->getResult();
        if (!$result) {
            $q = "
				select
				s.no
				from EXPORTADDITION_GAINFO eg 
				join LINKEDTABLEPARTS lp on lp.LA_GA_ID = eg.TTC_GA_ID
				join stokyonetimi s on s.id = lp.LA_ART_ID
				where 
				ifnull(s.status,0) = 1
				and ifnull(s.stokdurumu,0) = 1
				and ifnull(stokfiyati,0) > 0
				and urunAdi like '%{$query}%'
				and 
				(
					match (eg.GA_STANDARD) against ('{$query}' IN BOOLEAN MODE) > 0
					or match (eg.GA_ASSEMBLY) against ('{$query}' IN BOOLEAN MODE) > 0
				)
				order by s.stokfiyati
				limit {$limit} ";

            $result = $this->db->query($q)->getResult();
        }

        return $result;
    }
}
