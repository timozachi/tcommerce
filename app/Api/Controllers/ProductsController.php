<?php

namespace TCommerce\Api\Controllers;

use Phalcon\Exception;
use Phalcon\Http\Response;
use TCommerce\Api\Exceptions\APIException;
use TCommerce\Api\Exceptions\NotFoundAPIException;
use TLib\Http\Input;
use Phalcon\Validation\Message;
use Phalcon\Validation\Message\Group;
use Phalcon\Validation\Validator\Date;
use TCommerce\Api\Validations\Validation;
use TCommerce\Api\Validations\ProductValidation;
use TLib\Validations\Messages\Parser as MessagesParser;
use TCommerce\Core\Models\Product;
use TCommerce\Core\Models\Store;

class ProductsController extends Controller
{

	/**
	 * Display a listing of the resource.
	 */
	public function indexAction()
	{
		$model = Product::class;
		$query = Product::query();

		$query->columns(["$model.*"])
			->innerJoin(Store::class, "s.id = $model.store_id", 's');

		$products = $query->execute()->toArray();
		Product::parse($products);

		return $this->createResponse($products);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeAction()
	{
		$input = Input::post();

		$validation = new ProductValidation();
		$validation->mode = Validation::CREATE;

		/** @var Group $messages */
		$messages = $validation->validate($input);
		if($messages->count())
		{
			$errors = MessagesParser::parse($messages);

			return $this->createErrorsResponse(
				[
					'id' => 'invalid-resource-input',
					'title' => 'Invalid resource input',
					'detail' => 'Invalid resource input. Please fix the errors and try again.'
				],
				$errors,
				422
			);
		}
		else
		{
			$product = new Product();
			$product->active = 1;
			$product->create($input, $validation->getWhiteList());
		}

		return $this->createResponse(true, 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showAction($id)
	{
		$product = $this->_getProduct($id);

		$response = $product->toArray();
		Product::parse($response);

		return $this->createResponse($response);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function updateAction($id)
	{
		/** @var Product $product */
		$product = Product::findFirst((int)$id);

		$input = Input::post();

		$validation = new ProductValidation();
		$validation->mode = Validation::UPDATE;

		/** @var Group $messages */
		$messages = $validation->validate($input);
		if($messages->count())
		{
			$errors = MessagesParser::parse($messages);

			return $this->createErrorsResponse(
				[
					'id' => 'invalid-resource-input',
					'title' => 'Invalid resource input',
					'detail' => 'Invalid resource input. Please fix the errors and try again.'
				],
				$errors,
				403
			);
		}
		else
		{
			$product->update($input, $validation->getWhiteList());
		}

		return $this->createResponse(true, 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroyAction($id)
	{
		$product = Product::findFirst($id);

		$product->delete();

		return $this->createResponse(true, 200);
	}

	protected function _getProduct($id)
	{
		$product = Product::findFirst($id);
		if(!$product) {
			throw new NotFoundAPIException('Product not found: ' . $id);
		}

		return $product;
	}

}
