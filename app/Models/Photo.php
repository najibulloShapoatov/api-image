<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Photo extends Model
{



    public function getList()
    {
        return $this->get();
    }

    public function getListByEID($id){
        return $this->where('external_id', $id)->get();
    }

    public function getByUID($uid){
        return $this->where('uid', $uid)->get()->first();
    }
}
