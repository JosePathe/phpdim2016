<?php

namespace Framework;

use Framework\Http\RequestInterface;

interface KernelInterface
{
	/**
	 * Converts a Request object into a Response object
	 */
	public function handle(RequestInterface $request);
}