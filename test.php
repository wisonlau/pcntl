<?php
include 'pcntl.php';
$pcntl = new Process\pcntl();

$dbConf = array(
    'dbBase' => 'test',
    'dbHost' => '127.0.0.1',
    'dbPost' => '3306',
    'dbUser' => 'root',
    'dbPass' => 'root',
);
$dsn = "mysql:host={$dbConf['dbHost']};port={$dbConf['dbPost']};dbname={$dbConf['dbBase']}";

/**
 * 需要在守护进程中运行的代码
 * run Code
 */
$runCode = function($opt = array(), $num) use ($dsn, $dbConf)
{
    $oPdo = new \PDO( $dsn, $dbConf['dbUser'], $dbConf['dbPass'], array(PDO::ATTR_PERSISTENT => true) );
    $oPdo->exec( "SET SQL_MODE=ANSI_QUOTES" );
    $fp1 = fopen( "{$opt[$num]}.txt", "a+" );
    $sql = "SELECT * FROM log WHERE p_id={$opt[$num]};";
    $query = $oPdo->query( $sql );
    $row = $query->fetchAll( PDO::FETCH_ASSOC );
    foreach ($row as $value)
    {
        $save = "{$value['p_id']},{$value['kw_id']}\r\n";
        fwrite($fp1, iconv('UTF-8','GB2312', $save));
    }
    fclose( $fp1 );
    $oPdo = null;
};


/**
 * 入口函数
 * 在terminal中运行 /usr/local/php/bin/php ProcessOpera.php &
 * 查看进程 ps aux|grep php
 */
$pcntl->processOpera($runCode, array(633,21,671), 3);
