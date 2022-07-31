<?php
/**
*mysql数据库类
* leninx 2013.5
*/

class Mysql
{
    public $link;
    public $result;

    public function __construct()
    {
        $dbname=$_ENV['DB_USERNAME'];
        $dbpassword=$_ENV['DB_PASSWORD'];
        $dbdatabase=$_ENV['DB_DATABASE'];
        $dbhost=$_ENV['DB_HOST'];
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->connect($dbname, $dbpassword, $dbdatabase, $dbhost);
    }

    /*
    *连接数据库
    */
    public function connect($dbname, $dbpassword, $dbdatabase, $dbhost = 'localhost', $dbcharset = 'utf8mb4')
    {
        if (!$this->link) {
            $this->link = mysqli_connect($dbhost, $dbname, $dbpassword) or die(mysqli_error($this->link));
            //设置字符集
            mysqli_set_charset($this->link, $dbcharset) or die("can not set charset");
         
            mysqli_select_db($this->link, $dbdatabase) or die("can not select datebase");
        }
        return $this->link;
    }

    /*
    *执行查询
    */
    public function query($sql)
    {
        if (!$this->link) {
            return false;
        }
        if ($this->result) {
            $this->free();
        }
        if ($this->result = mysqli_query($this->link, $sql)) {
            return $this->result;
        //return $this;
        } else {
            return false;
        }
    }

    /*
    *执行查询２
    */
    public function queryd($sql)
    {
        return mysqli_query($sql, $this->link);
    }

    /*
    * 执行写入语句
    */
    public function execute($sql)
    {
        if (!$this->link) {
            return false;
        }
        if ($this->result) {
            $this->free();
        }
        $result = mysqli_query($sql, $this->link);
        if ($result == false) {
            trigger_error('MySQL execute error: ' . mysqli_error($this->link) . ' ['.$sql.']');
        }
    }

    /*
    *取得所有数据
    */
    public function fetchAll($result_type = MYSQLI_BOTH)
    {
        $data = null;
        //$query = $this->query($sql);
        while ($row = mysqli_fetch_array($this->result, $result_type)) {
            $data[] = $row;
        }
        mysqli_data_seek($this->result, 0);// 移动内部结果的指针从0行开始。
        //mysqli_free_result($result);
        return $data;
    }

    /*
    * 取得一行数据
    */
    public function fetchArray($type = MYSQLI_BOTH)
    {
        return mysqli_fetch_array($this->result, $type);
    }

    /*
    *取得一行另一种用法，在递归时用等
    */
    public function fetchRow($result, $type = MYSQLI_BOTH)
    {
        return mysqli_fetch_array($result, $type);
    }

    /*
    *对象形式取得一行数据
    */
    public function fetchObject()
    {
        return mysqli_fetch_object($this->result);
    }

    /*
    *返回记录行数
    */
    public function numRows()
    {
        return mysqli_num_rows($this->result);
    }

    /*
    *上一次操作的影响数
    */
    public function affectedRows()
    {
        return mysqli_affected_rows($this->link);
    }
    /*
    * 取得最后一次插入记录的ID值
    * @return int 返回最后一次插入记录的ID值
    */
    public function insertId()
    {
        return ($id = mysqli_insert_id($this->link)) >= 0 ? $id : mysqli_result($this->query("SELECT last_insert_id()"), 0);//mysql_result — （mysqli_data_seek() 联合 mysqli_field_seek() and mysqli_fetch_field()）取得结果数据

    }

    /*
    *获取错误信息
    */
    public function error()
    {
        return (($this->link) ? mysqli_error($this->link) : mysqli_error($this->link));
    }

    /*
    *获取错误代码
    */
    public function errno()
    {
        return intval(($this->link) ? mysqli_errno($this->link) : mysqli_errno($this->link));
    }

    /*
    *释放结果集
    */
    public function free()
    {
        if (is_object($this->result)) {
            mysqli_free_result($this->result);
            $this->result = null;
        }
    }

    /*
    *转义字符
    */
    public function escape($str)
    {
        return mysqli_real_escape_string($this->link, $str);
    }

    /*
    *关闭数据库连接
    */
    public function close()
    {
        return @mysqli_close($this->link);
    }

    /*
    *析构
    */
    public function __destruct()
    {
        $this->free();
        $this->close();
    }
}
