<?php
class SimpleHouse{
    public $model = "";
    public $square = 0;
    public $floors = 0;
    public $color = "none";

    // Конструктор класса
    function __construct ($model, $square = 0, $floors = 1)
    {
        $this->model = $model;
        $this->square = $square;
        $this->floors = $floors;
    }
    function startProject()
    {
        echo "Start. Model: {$this->model}<br>";
    }
    function stopProject()
    {
        echo "Stop. Model: {$this->model}<br><br>";
    }
    function build()
    {
        echo "Build. House: {$this->square}x{$this->floors}<br>";
    }
    function paint()
    {
        echo "Paint. Color: {$this->color}<br>";
    }
}
// Создание простого дома
$simple = new SimpleHouse("A-100-123", 120, 2);
$simple->color = "red";
$simple->startProject();
$simple->build();
$simple->paint();$simple->stopProject();

// Создание класса-наследника
class SuperHouse extends SimpleHouse
{
    public $fireplace = true;
    public $patio = true;

    function fire()
    {
        if ($this->fireplace) {
            echo "Fueled fireplace<br>";
        }
    }
}
$super = new SuperHouse("A-100-125", 320, 3);
$super->color = "green";
$super->startProject();
$super->build();
$super->paint();
$super->fire();
$super->stopProject();

// Создание класса-наследника
class FabricHouse extends SimpleHouse
{
// Перегрузка метода
    function build()
    {
        echo "Build. Fabric: {$this->square}x{$this->floors}<br>";
    }
}
$fabric = new FabricHouse("B-200-007", 3250, 5);
$fabric->color = "white";
$fabric->startProject();
$fabric->build();
$fabric->paint();
$fabric->stopProject();

// Создание класса-наследника
class SuperFabricHouse extends FabricHouse
{
    function build()
    {
        echo "=========================================<br>";
        // Вызов родительского метода
        echo parent::build();
        echo "=========================================<br>";
    }
}
$super_fabric = new SuperFabricHouse("C-201-034", 5150, 7);
$super_fabric->color = "black";
$super_fabric->startProject();
$super_fabric->build();
$super_fabric->paint();
$super_fabric->stopProject();