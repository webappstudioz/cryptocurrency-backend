<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

        /**
         * Get the identifier that will be stored in the subject claim of the JWT.
         *
         * @return mixed
         */
        public function getJWTIdentifier()
        {
            return $this->getKey();
        }

        /**
         * Return a key value array, containing any custom claims to be added to the JWT.
         *
         * @return array
         */
        public function getJWTCustomClaims()
        {
            return [];
        }
        /**
         * The attributes that are mass assignable.
         *
         * @var string[]
         */
    protected $fillable = [
        'first_name', 
        'last_name', 
        'email',
        'role_id',
        'parent_id',
        'password',
        'status',
        'created_at',
        'updated_at',
        'user_name',
        'referral_code',
        'phone_number',
        'country_id',
        'security_key',
        'verified',
        'joining_date',
        'term_condition',
        'supponser_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
        'role_id',
    ];

    public function userdetail() {
		return $this->hasOne(UserDetail::class);
	}

    public function cryptodetail() {
		return $this->hasOne(CryptoAccountDetail::class);
	}

    public function bankdetail() {
		return $this->hasOne(BankAccountDetail::class);
	}

    public function country() {
		return $this->belongsTo(Country::class);
	}

}
