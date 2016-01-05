<?php

namespace Framework\Http;

class Request
{
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const PATCH = 'PATCH';
	const OPTIONS = 'OPTIONS';
	const CONNECT = 'CONNECT';
	const TRACE = 'TRACE';
	const HEAD = 'HEAD';
	const DELETE = 'DELETE';

	const HTTP = 'HTTP';
	const HTTPS = 'HTTPS';

	private $method;				//GET ou POST
	private $scheme;				//Protocole utilisé
	private $schemeVersion; 		//Version du protocole
	private $path;					//Chemin
	private $headers;				//entêtes de la requête
	private $body; 					//Corps de la requête

	/**
	 *	Constructor.
	 *	
	 *	@param string $method 			The HTTP verb
	 *	@param string $path 			The resource path on the server
	 *	@param string $scheme 			The protocole name (HTTP or HTTPS)
	 *	@param string $schemeVersion 	The scheme version (ie: 1.0, 1.1 or 2.0)
	 *	@param array  $headers 			An associative array of headers
	 *	@param string $body 			The request content
	 */
	public function __construct($method, $path, $scheme, $schemeVersion, array $headers = [], $body = '')
	{
		$this->setMethod($method);
		$this->path = $path;
		$this->scheme = $scheme;
		$this->schemeVersion = $schemeVersion;
		$this->headers = $headers;
		$this->body = $body;

	}

	private function setMethod($method)
	{
		$methods = [
			self::GET,
			self::POST,
			self::PUT,
			self::PATCH,
			self::OPTIONS,
			self::CONNECT,
			self::TRACE,
			self::HEAD,
			self::DELETE,
		];

		if(!in_array($method, $methods)) {
			throw new \InvalidArgumentException(sprintf(
				'Method %s is not a supported and must be one of %s.',
				$method,
				implode(', ', $methods)
			));	
		}

		$this->method = $method;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getScheme()
	{
		return $this->scheme;
	}

	public function getSchemeVersion()
	{
		return $this->schemeVersion;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function getBody()
	{
		return $this->body;
	}

}