<?php
class User
{
    public $name;
    public $login;
    public $password;
    function __construct($name, $login, $password)
    {
        $this->name = $name;
        $this->login = $login;
        $this->password = $password;
    }
    function __destruct()
    {
        echo "</br>Пользователь $this->name удален";
    }
    function showInfo()
    {
        echo sprintf('%sName: %s<br>Login: %s<br>Password: %s', $this->drawLine(), $this->name, $this->login, $this->password);
    }
    function drawLine()
    {
        echo '<hr>';
    }
}
class SuperUser extends User
{
    public $role;
    function __construct($name, $login, $password, $role)
    {
        parent::__construct($name, $login, $password);
        $this->role = $role;
    }
    function showInfo()
    {
        parent::showInfo();
        echo "<br>Role: {$this->role}";
    }
}

$user = new SuperUser("Vasya", "monalisa", "1234", "admin");
$user1 = new User("John Snow", "johntheking", "pass1");
$user2 = new User("Sam Wisely", 'samwise', "pass2");
$user3 = new User("Jamie Lannister", "lannister", "pass2");
$user->showInfo();
$user1->showInfo();
$user2->showInfo();
$user3->showInfo();