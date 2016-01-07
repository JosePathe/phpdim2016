<?php

namespace Framework;

use Framework\Http\Response;
use Framework\Http\RequestInterface;

class Kernel implements KernelInterface
{
	/**
	 * Converts a Request object into a Response object
	 */
	public function handle(RequestInterface $request)
	{
		return new Response(
			Response::HTTP_OK,
			$request->getScheme(),
			$request->getSchemeVersion(),
			[ 'Content-Type' => 'application/json'],
			json_encode([ 'Hello' => 'World!'])
		);
	}
}