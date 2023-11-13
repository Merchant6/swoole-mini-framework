<?php

namespace App\Utils;
use Swoole\Http\Request;

class Validator
{   
    /**
     * Validation Errors
     * @var array
     */
    private array $errors = [];

    /**
     * Data array
     * @var array
     */
    private array $data = [];

    private \ReflectionMethod $reflectionMethod;
    
    public function __construct()
    {
        
    }

    public function make(Request $request, array $data)
    {
        foreach($data as $dataKey => $dataValue)
        {
            if(array_key_exists($dataKey, $request->post))
            {
                $rules = explode('|', $dataValue);
                foreach($rules as $method)
                {  
                    if(str_contains($method,':'))
                    {   
                        $methodWithArgs = explode(':', $method);

                        if(!method_exists($this, $methodWithArgs[0]))
                        {   
                            throw new \BadMethodCallException("Method '$methodWithArgs[0]' does not exists in " . __CLASS__);
                        }

                        $args =[];
                        $args[] = $request->post[$dataKey];
                        $args = array_merge($args, array_slice($methodWithArgs, 1));

                        call_user_func([$this, $methodWithArgs[0]], ...$args);
                    }

                    elseif(!str_contains($method,':'))
                    {
                        if(!method_exists($this, $method))
                        {   
                            throw new \BadMethodCallException("Method '$method' does not exists in " . __CLASS__);
                        };

                        call_user_func([$this, $method], $request->post[$dataKey]); 
                    } 
                }
            }

        }
    }

    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * Assert that the field is required
     * @param string $field
     * @param string $message
     * @return \App\Core\Validator
     */
    public function required(mixed $value, string $message = "This field is required."): self
    {
        if(!isset($value) || empty($value))
        {
            $this->errors[] = $message;
        }

        return $this;
    }

    /**
     * Assert that the field is string
     * @param mixed $field
     * @param string $message
     * @return \App\Core\Validator
     */
    public function string(mixed $value, string $message = "The field is not a string"): self
    {
        $value = trim($value ?? ''); 
        if(!is_string($value) || empty($value))
        {
            $this->errors[] = $message;
        }

        return $this;
    }

    /**
     * Assert that the field is an email
     * @param mixed $field
     * @param mixed $message
     * @return \App\Core\Validator
     */
    public function email(mixed $value, $message = "This field is not an email."): self
    {
        if(!preg_match("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$^", $value))
        {
            $this->errors[] = $message;
        }

        return $this;
    }

    /**
     * Set the minimum and minimum value of the field
     * @param mixed $field
     * @param int $min
     * @param int $max
     * @param string $message
     * @return \App\Core\Validator
     */
    public function length(mixed $value, int $min, int $max, string $message = "Length is invalid"): self
    {
        $length = strlen($value);

        if($min > $max)
        {
            throw new \Exception("Minimun length cannot be greater than the Maximum length");
        }

        if($length < $min)
        {
            $message = "Length cannot be lesser than $min";
            $this->errors[] = $message;
        }

        if($length > $max)
        {
            $message = "Length cannot be greater than $max";
            $this->errors[] = $message;
        }

        return $this;
    }

    /**
     * Assert that the given field has only aplahbets and whitespaces
     * @param mixed $field
     * @param string $message
     * @return \App\Core\Validator
     */
    public function alpha(mixed $value, string $message = "Invalid name"): self
    {
        if(!preg_match("^[a-zA-Z ]+$^", $value))
        {
            $this->errors[] = $message;
        }

        return $this;
    }

    public function regex(mixed $value, string $pattern, string $message = 'Invalid field')
    {
        if(!preg_match($pattern, $value))
        {
            $this->errors[] = $message; 
        }

        return $this;
    }

	/**
	 * Return validation Errors
	 * @return array
	 */
	public function getErrors(): array {
		return $this->errors;
	}
}
