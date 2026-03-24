<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $guarded = array('id');

    public static $rules = array(
        'title' => 'required',
        'body' => 'required',
    );

    // Book Modelに関連付けを行う
    public function histories()
    {
        return $this->hasMany('App\Models\BookHistory');
    }
}
