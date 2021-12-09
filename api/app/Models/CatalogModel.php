<?php namespace App\Models;

use CodeIgniter\Model;

class CatalogModel extends Model
{
    public function getPartBrands($limit, $featured = 0)
    {
        $q = "select
        t.id,
        s.stokmarka as name,
        count(s.no) as product_count,
        t.TTC_BRA_ID as bra_id,
        t.futured
        from
        stokyonetimi s 
        inner join TABLEBRANDS t on t.BRAND = s.`stokmarka`
        where stokmarka is not null
        and ifnull(stokfiyati,0) > 0
        and ifnull(s.status,0) = 1 ";
        if ($featured) {
            $q .= " and t.futured = 1 ";
        }
        $q .= " group by s.stokmarka
        order by s.stokmarka ";
        if ($limit > 0) {
            $q .= " limit $limit ";
        }
        return $this->db->query($q)->getResult();
    }

    public function getPartCategories()
    {
        $q = "
        SELECT
        TTC_GA_ID as mainCatID,
        L.LA_ART_ID as partLinkID,
        GA_ASSEMBLY as mainCatName,
        GA_STANDARD as subCatName
        FROM EXPORTADDITION_GAINFO E
        JOIN LINKEDTABLEPARTS L ON L.LA_GA_ID = E.TTC_GA_ID
        WHERE
        IFNULL(durum,0) = 1
        and E.GA_ASSEMBLY is not null
        and L.disable = 0
        ORDER BY GA_ASSEMBLY, E.GA_STANDARD ";

        return $this->db->query($q)->getResult();
    }

    public function prodStockControlForCategory($prodID)
    {
        $q = "
        select
        no 
        from stokyonetimi 
        where
        id = {$prodID}
        and aksesuar = 0
        and madeniyag = 0
        and status = 1
        and ifnull(stokfiyati,0) > 0 
        limit 1 ";

        return $this->db->query($q)->getResult();
    }

    public function getAccCategories()
    {
        $q = "
        select
        DISTINCT kate1 as name
        from stokyonetimi
        where
        kate1 is not null
        and aksesuar = 1
        and status = 1
        and stokfiyati > 0
        order by kate1 
        ";

        return $this->db->query($q)->getResult();
    }

    public function getOilCategories()
    {
        $q = "
        select
        k.id as mainCatID,
        k.isim as mainCatName,
        u.altkate as subCatName
        from ustkategori k
        join kategori_urun u on u.ustkate = k.id
        where
        k.status = 1
        group by u.altkate
        ";

        return $this->db->query($q)->getResult();
    }

    public function getCampainCategories()
    {
        $q = "
        select
        kampanya as campain_type,
        arac_marka as campain_name,
        stokfiyati
        from stokyonetimi
        where
        kampanyali = 1
        and ifnull(arac_marka,'YOK') != 'YOK'
        and ifnull(kampanya,'YOK') != 'YOK'
        and stokfiyati > 0
        group by kampanya, arac_marka
        order by arac_marka
        ";

        return $this->db->query($q)->getResult();
    }

    public function getPartBrandCategories($brandName)
    {
        $q = "
        select
        eg.GA_STANDARD as category_name
        from EXPORTADDITION_GAINFO eg 
        join LINKEDTABLEPARTS l on l.LA_GA_ID = eg.TTC_GA_ID
        join stokyonetimi s on s.id = l.LA_ART_ID and s.stokmarka = '{$brandName}' 
        where
        ifnull(stokfiyati,0) > 0
        and ifnull(s.status,0) = 1
        and ifnull(s.stokdurumu,0) > 0
        and ifnull(l.disable,0) = 0 
        and ifnull(eg.durum,0) = 1
        group by eg.GA_STANDARD
        having count(eg.GA_STANDARD) > 0
        order by eg.GA_STANDARD
        ";

        return $this->db->query($q)->getResult();
    }
}
