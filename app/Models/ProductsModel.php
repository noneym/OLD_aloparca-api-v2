<?php namespace App\Models;
  
use CodeIgniter\Model;
 
class ProductsModel extends Model
{
    public function getParts($arrParams = array())
    {
        $q = "";
		
        return $this->db->query($q)->getResult();
    }

}