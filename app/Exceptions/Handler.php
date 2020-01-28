<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
		//return parent::render($request, $exception);
        if ($e instanceof TokenMismatchException){
            //Redirect to login form if session expires
            return redirect()->route('doLogin');
        }
		return parent::render($request, $e);
		/*
        if($this->isHttpException($exception)){
            if (view()->exists('errors.'.$exception->getStatusCode()))
            {
                return response()->view('errors.'.$exception->getStatusCode(), [], $exception->getStatusCode());
            }else{
            return response()->view('errors.generic', ['error'=>''], $exception->getStatusCode());
            }
        }
        
        return response()->view('errors.generic', ['error' => $exception->getMessage()]);*/
	}

}
