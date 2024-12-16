<?php
namespace App\Models;

//This class is just response model throughout the project
use Illuminate\Http\JsonResponse;

class ResponseResult
{
    // false if there is an error otherwise true
    //
    //  case true, so the operation is success
    //  case false, so there is error. it can be a validation error or general error
    //              clients should check both errors and validation parameters
    public bool $status;
    // output, if success
    public object $output;
    //list of errors in case of failure
    public array $errors;
    //List of validations:
    public object $validations;

    public function __construct(bool $status = false, object $output = null, array $errors = [], object $validations = null)
    {
        $this->status = $status;
        $this->output = $output ?? (object)[];
        $this->errors = $errors;
        $this->validations = $validations ?? (object)[];
    }

    public static function Success(object|null $output): JsonResponse
    {
        return response() ->json(new self(status: true, output: $output));
    }
    public static function Failure(array $errors): JsonResponse
    {
        return response() ->json(new self(status:false, errors: $errors));
    }
    public static function ValidationError(object|null $validations): JsonResponse
    {
        return response() ->json(new self(status:false, validations: $validations), 200, [], JSON_UNESCAPED_UNICODE);
    }
}
