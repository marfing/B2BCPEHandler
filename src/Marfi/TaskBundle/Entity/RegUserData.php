<?php

namespace Marfi\TaskBundle\Entity;

class RegUserData
{
	protected $email;
	
	protected $password;
	
	public function getEmail()
	{
		return $this->email;
	}
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	public function printValues(){
		echo 'Email: ' . $this->email. ' - Password: ' .$this->password;
	}
		
}
	
	
	?>
