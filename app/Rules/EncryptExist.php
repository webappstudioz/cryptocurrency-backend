<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EncryptExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $model_class , $field;

    public function __construct( $model_class , $field = null )
    {
        //
        $this->model_class = $model_class;
        if( $field ) {
            $this->field = $field;
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if( empty($value) )
            return true;
        if( $this->field ) {
            $attribute = $this->field;
        }
        $object = new $this->model_class;
        
        $request = request();

        $request->request->add(["decrypt_$attribute"   => decryptData($value)  ]);
        return $object->where( $attribute , decryptData($value) )->first();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Field :attribute does not exist'  ;
    }
}
