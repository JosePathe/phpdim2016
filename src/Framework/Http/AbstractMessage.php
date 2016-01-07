<?php

namespace Framework\Http;

abstract class AbstractMessage implements MessageInterface
{
	protected $scheme;				//Protocole utilisé
	protected $schemeVersion; 		//Version du protocole
	protected $headers;				//entêtes de la requête
	protected $body; 				//Corps de la requête

	/**
	 *	Constructor.
	 *	
	 *	@param string $scheme 			The protocole name (HTTP or HTTPS)
	 *	@param string $schemeVersion 	The scheme version (ie: 1.0, 1.1 or 2.0)
	 *	@param array  $headers 			An associative array of headers
	 *	@param string $body 			The request content
	 */
	public function __construct($scheme, $schemeVersion, array $headers = [], $body = '')
	{
		$this->headers = [];

		$this->setScheme($scheme);
		$this->setSchemeVersion($schemeVersion);
		$this->setHeaders($headers);
		$this->body = $body;

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
		$headers = [];
		foreach ($this->headers as $header) {
			$headers = array_merge($headers, $header->toArray());
		}
		return $headers;
	}

	public function getBody()
	{
		return $this->body;
	}

	private function setScheme($scheme)
	{
		$schemes = [ self::HTTP, self::HTTPS ];

		if(!in_array($scheme, $schemes)) {
			throw new \InvalidArgumentException(sprintf(
				'Scheme %s is not a supported and must be one of %s.',
				$scheme,
				implode(', ', $schemes)
			));	
		}

		$this->scheme = $scheme;
	}

	private function setSchemeVersion($version)
    {
        $versions = [ self::VERSION_1_0, self::VERSION_1_1, self::VERSION_2_0 ];

        if (!in_array($version, $versions)) {
            throw new \InvalidArgumentException(sprintf(
                'Scheme version %s is not supported and must be one of %s.',
                $version,
                implode(', ', $versions)
            ));
        }

        $this->schemeVersion = $version;
    }

    private function setHeaders(array $headers)
    {
    	foreach ($headers as $header => $value) {
    		$this->addHeader($header, $value);
    	}
    }

    public function getHeader($name)
    {
    	if ($header = $this->findHeader($name)) {
    		return  $header->getValue();
    	}
    }

    public function findHeader($name)
    {
    	foreach ($this->headers as $header) {
    		if ($header->match($name)) {
    			return $header;
    		}
    	}
    }

    /**
     * Adds a new normalized header value to th elist of all headers
     * 
     * @param string $header 	The HTTP header name
     * @param string $value 	The HTTP header value
     * 
     * @throws \RuntimeException
     */
    private function addHeader($name, $value)
    {
		if ($this->findHeader($name)) {
			throw new \RuntimeException(sprintf(
				"Header %s is already defined and cannot be set twice.",
				$name
			));
		}

		$this->headers[] = new Header($name, (string) $value);
    }

    protected abstract function createPrologue();

	final public function getMessage()
	{
		$message = $this->createPrologue();

		if(count($this->headers)) {
			$message.= PHP_EOL; //PHP_EOL == "\n"
			foreach ($this->headers as $header) {
	    		$message.= $header.PHP_EOL;
	    	}
		}

		$message.= PHP_EOL;
		if ($this->body) {
			$message.= $this->body;
		}
		
		return $message;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getMessage();
	}

	protected static function parseHeaders($message)
	{
		$start = strpos($message, PHP_EOL) + 1;
		$end = strpos($message, PHP_EOL.PHP_EOL);
		$length = $end - $start;
		$lines = explode(PHP_EOL, substr($message, $start, $length));

		// 2. Parse list of headers (if any)
		$i = 0;
		$headers = [];
		while (!empty($lines[$i])) {
			$headers = array_merge($headers, static::parseHeader($lines[$i], $i));
			$i++;
		}

		return $headers;
	}

	private static function parseHeader($line, $position)
	{
		try {
			return Header::createFromString($line)->toArray();
		} catch (MalformedHttpMessageException $e) {
			throw new MalformedHttpMessageException(
				sprintf('Invalid header line at position %u: %s', $position+2, $line),
				0,
				$e
			);
		}
	}

	protected static function parseBody($message)
	{
		// 3. Parse content (if any)
		$pos = strpos($message, PHP_EOL.PHP_EOL);

		return (string) substr($message, $pos+2);
	}
}