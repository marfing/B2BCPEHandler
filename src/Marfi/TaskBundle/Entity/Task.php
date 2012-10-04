<?php

namespace Marfi\TaskBundle\Entity;

class RegUserData
{
    //protected $task;

    //protected $dueDate;
		
	protected $email;
	
	protected $password;

   /* public function getTask()
    {
        return $this->task;
    }
    public function setTask($task)
    {
        $this->task = $task;
    }
	
	public function getDueDate()
    {
        return $this->dueDate;
    }
    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }
	*/
	 public function getEmail()
	 
	{
		return $this->email;
	}
	public function setEmail($email){
		$this->email=$email;
	}
    
		
	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($password)
	{
		$this->password=$password;
	}
	
	public function printValues(){
		echo 'Email: ' . $this->email. ' - Password: ' .$this->password;
	}

}

?>
