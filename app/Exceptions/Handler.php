<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Throwable;

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
        'senha'
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {

        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return response(
                [
                    'mensagem' => "{$this->retrieveNotFoundEntity($e)} não encontrado."
                ],
                404
            );
        }

        if ($e instanceof UnauthorizedException) {
            return response()->json(
                [
                    'mensagem' => $e->getMessage()
                ],
                401
            );
        }

        if ($e instanceof ValidationException) {
            return response()->json(
                [
                    'mensagem' => $e->validator->errors()->first()
                ],
                400
            );
        }

        if ($e instanceof \InvalidArgumentException) {
            return response()->json(
                [
                    'mensagem' => $e->getMessage()
                ],
                400
            );
        }


        return response()->json(
            [
                (env('APP_ENV') === 'local') ? $e->getMessage() : 'Ocorreu um erro ao executar sua requisição.'
            ],
            500
        );

    }

    private function retrieveNotFoundEntity(ModelNotFoundException $exception): string
    {
        return strtolower(class_basename($exception->getModel()));
    }

}