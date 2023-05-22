<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        // ...
    ];

    protected $dontFlash = [
        // ...
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => 'failed', 'error' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(['status' => 'failed', 'error' => 'You must login to access this resource.'], Response::HTTP_UNAUTHORIZED);;
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'failed', 'errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return redirect()->back()->withInput($request->input())->withErrors($errors);
    }

    protected function convertHttpExceptionToResponse(HttpException $e)
    {
        $status = $e->getStatusCode();
        $message = Response::$statusTexts[$status] ?? 'Error';

        return response()->json(['status' => 'failed', 'error' => $message], $status);
    }
}