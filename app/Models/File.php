<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    # Не забывай про Guarded везде в моделях
    protected $guarded = ['id'];

    public function access()
    {
        return $this->hasMany(Access::class);
    }

    public function nameFile()
    {
        return $this->name . ($this->version ? ' (' . $this->version . ')' : '') . '.' . $this->type;
    }
}
