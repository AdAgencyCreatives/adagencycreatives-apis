<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class ApiException extends Exception
{
    public $e;
    public $code;

    public function __construct(Exception $e, $code)
    {
        $this->e = $e;
        $this->code = $code;
    }

    public function render(Request $request)
    {   
        $data = [
            'status' => 'failed',
            'code' => $this->code
        ];

        if(env('APP_ENV') != 'production'){
            $data['message'] = $this->e->getMessage();
        }

        return response($data, 400);
    }
}