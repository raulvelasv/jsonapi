<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    protected function invalidJson($request, ValidationException $exception): JsonApiValidationErrorResponse
    {
        return new JsonApiValidationErrorResponse($exception);
        /*$title = 'The given data was invalid.';
        $errors = [];
        foreach ($exception->errors() as $field => $message) {
            $pointer = '/' . str_replace('.', '/', $field);
            $errors[] = [
                'title' => $title,
                'detail' => $message[0],
                'source' => [
                    'pointer' => $pointer
                ]
            ];
        }
        return response()->json([
            'errors' => $errors
        ], 422, ['content-type' => 'application/vnd.api+json']);*/
    }
}
