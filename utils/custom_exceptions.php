<?php

/**
 * Define a custom exception class
 */
class PermissionsException extends Exception
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



class DBQueryException extends Exception
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



// subtype of db query excerption
class EmptyDBTableException extends DBQueryException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



// subtype of db query excerption
class InsertRecordException extends DBQueryException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



// subtype of db query excerption
class UpdateRecordException extends DBQueryException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



// subtype of db query excerption
class DeleteRecordException extends DBQueryException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



class ImageInputException extends Exception
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}

class NoImageUploadException extends ImageInputException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}



class FileException extends Exception
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}


class FileOpenException extends FileException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}

class FileCloseException extends FileException
{
  public function __construct($message, $code = 0, Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  public function __toString()
  {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

  public function RecoveryFunction()
  {
  }
}
