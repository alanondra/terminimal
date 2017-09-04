<?php

namespace Terminimal\Exceptions;

use Exception;

class MissingClassException extends Exception
{
	public function __construct($className, $code = 0, Exception $previous = null)
	{
		parent::__construct(sprintf('Class "%s" does not exist.', $className), $code, $previous);
	}
}
