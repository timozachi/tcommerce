<?php

namespace TCommerce\Api\Controllers;

use Phalcon\Http\Response;
use TCommerce\Core\Models\State;

class StatesController extends Controller
{

	/**
	 * Display a listing of the resource.
	 */
	public function indexAction()
	{
		$states = State::find([
			'order' => 'name'
		]);

		return $this->createResponse($states);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function storeAction()
	{

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showAction($id)
	{
		$state = State::findFirst($id);

		return $this->createResponse($state);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function updateAction($id)
	{

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroyAction($id)
	{

	}

}
