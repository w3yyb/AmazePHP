<?php
namespace AmazePHP;

use eftec\PdoOne;

class DB
{
    use SingletonTrait;
    public $dao;

    // private $cache;

    public function __construct()
    {

        $config =config('database');

        $driver=$config['default'];
        

        if ($driver=='mysql') {
            $mysqlconfig=$config['connections']['mysql'];
            $this->dao=new PdoOne("mysql",$mysqlconfig['host'],$mysqlconfig['username'],$mysqlconfig['password'],$mysqlconfig['database'],"");
            $this->dao->logLevel=3; // it is for debug purpose and it works to find problems.
            $this->dao->connect();
        } elseif ($driver=='sqlsrv') {
            // $dao=new PdoOne("sqlsrv","(local)\sqlexpress","sa","abc.123","sakila","");
            // $conn->logLevel=3; // it is for debug purpose and it works to find problems.
            // $dao->connect();
        } else {
            throw new Exception("database driver not found");
        }


        //todo array ,file

        return $this->dao;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->dao, $name], $arguments);
    }
   
}
