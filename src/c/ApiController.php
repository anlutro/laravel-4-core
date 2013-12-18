<?php
/**
 * Laravel 4 Core - Base API controller
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use Illuminate\Support\Facades\Response;

/**
 * Abstract class for basic API functionality.
 */
abstract class ApiController extends Controller
{
	/**
	 * Return a generic success response.
	 *
	 * @param  mixed $messages optional
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function success($messages = null)
	{
		$data = ['status' => 'success'];
		$this->addMessages($messages, $data);

		return Response::json($data, 200);
	}

	/**
	 * Return an error response.
	 *
	 * @param  mixed $errors
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function error($errors)
	{
		if ($errors instanceof ArrayableInterface) {
			$errors = $errors->toArray();
		}

		$data = ['status' => 'error', 'errors' => $errors];

		return Response::json($data, 400);
	}

	/**
	 * Return a generic not found response.
	 *
	 * @param  mixed $messages optional
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function notFound($messages = null)
	{
		$data = ['status' => 'not-found'];
		$this->addMessages($messages, $data);

		return Response::json($data, 404);
	}

	/**
	 * Add messages to an array of response data.
	 *
	 * @param  mixed  $messages optional
	 * @param  array &$data     the data that is sent to Response::json later on
	 *
	 * @return void
	 */
	protected function addMessages($messages, &$data)
	{
		if ($messages !== null) {
			if ($messages instanceof ArrayableInterface) {
				$messages = $messages->toArray();
			}

			$data['messages'] = $messages;
		}
	}
}
