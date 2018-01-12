<?php

namespace TCommerce\Cli\Tasks;

use Phalcon\Cli\Task;

class TestTask extends Task
{

    public function indexAction()
    {
        echo "This is the test:index action\n";
    }

    public function anotherAction()
    {
        echo "This is the test:another action\n";
    }

}