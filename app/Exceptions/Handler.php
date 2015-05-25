<?php namespace ImguBox\Exceptions;

use Exception;
use Slack, Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException',
		'Illuminate\Database\Eloquent\ModelNotFoundException'
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
        Log::error($e);

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
		if ($e->getCode() >= 500) {
			$this->sendNotification($request, $e);
		}

		if($e instanceof ModelNotFoundException)
		{
			return response()->view('errors.404', [], 404);
		}

		return parent::render($request, $e);
	}

	private function sendNotification($request, $e)
	{
        $attachment = [
            'fallback' => 'ImguBox Error',
            'text'     => 'ImguBox Error',
            'color'    => '#c0392b',
            'fields' => [
                [
                    'title' => 'Requested URL',
                    'value' => $request->url(),
                    'short' => true
                ],
                [
                    'title' => 'HTTP Code',
                    'value' => $e->getCode(),
                    'short' => true
                ],
                [
                    'title' => 'Exception',
                    'value' => $e->getMessage(),
                    'short' => true
                ],
                [
                    'title' => 'Input',
                    'value' => json_encode($request->all()),
                    'short' => true
                ]
            ]
        ];

        Slack::attach($attachment)->send('ImguBox Error');
	}

}
