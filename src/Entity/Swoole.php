<?php

namespace App\Entity;
use BadMethodCallException;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('swoole')]
class Swoole
{
    #[Id]
    #[Column, GeneratedValue]
    private int $id;

    #[Column]
    private string $fname;

    #[Column]
    private string $lname;

	/**
	 * @return int
	 */
	public function getId(): int 
    {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getFname(): string {
		return $this->fname;
	}
	
	/**
	 * @param string $fname 
	 * @return self
	 */
	public function setFname(string $fname): self {
		$this->fname = $fname;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLname(): string {
		return $this->lname;
	}
	
	/**
	 * @param string $lname 
	 * @return self
	 */
	public function setLname(string $lname): self {
		$this->lname = $lname;
		return $this;
	}

	public function setProperties(array $data)
	{
		foreach($data as $property => $value)
		{
			$setterMethod = 'set' . ucfirst($property);
			if(method_exists($this, $setterMethod))
			{
				$this->$setterMethod($value);
			}

		}
	}
}
