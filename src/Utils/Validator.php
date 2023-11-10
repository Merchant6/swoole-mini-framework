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
    
    public function __construct()
    {

    }

    public function requestMake(Request $request, array $data)
    {
        $postArray = [];
        foreach($request->post as $postKey => $postValue)
        {
            $postArray[$postKey] = $postValue;

            foreach($data as $dataKey => $dataValue)
            {
                if(array_key_exists($dataKey, $postArray))
                {
                    var_dump(explode($dataValue));
                }
            }
        }

        // var_dump($postArray);
    }

    public function make(array $data): self
    {
        $this->data = $data;
        return $this;
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
    public function required(mixed $field, string $message = "This field is required."): self
    {
        if(!isset($this->data[$field]) || empty($this->data[$field]))
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
    public function string(mixed $field, string $message = "The field is not a string"): self
    {
        $value = trim($this->data[$field] ?? ''); 
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
    public function email(mixed $field, $message = "This field is not an email."): self
    {
        if(!preg_match("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$^", $this->data[$field]))
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
    public function length(mixed $field, int $min, int $max, string $message = "Length is invalid"): self
    {
        $length = strlen($this->data[$field]);

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
    public function alpha(mixed $field, string $message = "Invalid name"): self
    {
        if(!preg_match("^[a-zA-Z ]+$^", $this->data[$field]))
        {
            $this->errors[] = $message;
        }

        return $this;
    }

    public function regex(mixed $field, string $pattern, string $message = 'Invalid field')
    {
        if(!preg_match($pattern, $this->data[$field]))
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
