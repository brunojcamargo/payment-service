<?php

namespace App\Models;

use App\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes, UuidableTrait;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'fullName',
        'document',
        'email',
        'password',
        'type',
    ];

    protected $hidden = [
        'password'
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactionsFrom()
    {
        return $this->hasMany(Transaction::class,'from','id');
    }
}
