<?php

namespace App\Exceptions;

use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistException;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionHandler
{
    use ApiResponse;

    public function handle(Throwable $e, $request) {
        if($e instanceof ValidationException){
            return $this->error('Validation Error', $e->status, $e->errors());
        }

        if($e instanceof AuthenticationException){
            return $this->error('Unauthenticated', 401);
        }

        if($e instanceof UserAlreadyExistException){
            return $this->error($e->getMessage(), 409);
        }
        if($e instanceof InvalidCredentialsException){
            return $this->error($e->getMessage(), 401);
        }

        if($e instanceof ModelNotFoundException){
            return $this->error('Resource not found', 404);
        }

        if($e instanceof NotFoundHttpException){
            return $this->error('Route not found', 404);
        }

        if($e instanceof HttpExceptionInterface){
            return $this->error(
                $e->getMessage() ?: 'Http Error',
                $e->getStatusCode() ?: 500
            );
        }
        
        return $this->error(
            config('app.debug') ? $e->getMessage() : 'Internal Server Error',
            500,
            config('app.debug') ? $e->getTrace() : null
        );
    }

}