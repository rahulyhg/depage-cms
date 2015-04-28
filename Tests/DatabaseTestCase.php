<?php

namespace Depage\XmlDb\Tests;

class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    // {{{ variables
    protected $pdo  = null;
    protected $conn = null;
    // }}}

    // {{{ setUp
    protected function setUp() {
        $this->getConnection();
        $this->pdo->query('SET foreign_key_checks = 0');
        parent::setUp();
        $this->pdo->query('SET foreign_key_checks = 1');
    }
    // }}}
    // {{{ getConnection
    final public function getConnection()
    {
        $this->pdo = new \Depage\Db\Pdo(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD'],
            array(
                'prefix' => 'xmldb',
                \PDO::ATTR_PERSISTENT => true,
            )
        );
        $this->conn = $this->createDefaultDBConnection($this->pdo->getPdoObject(), $GLOBALS['DB_DBNAME']);

        return $this->conn;
    }
    // }}}
    // {{{ getDataSet
    protected function getDataSet() {
        return $this->createXMLDataSet(__DIR__.'/xmldb_dataset.xml');
    }
    // }}}

    // {{{ prepareDatabase
    public static function prepareDatabase()
    {
        $pdo = new \Depage\Db\Pdo(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD']
        );

        function prefix($name)
        {
            return 'xmldb_proj_test' . $name;
        };

        $schema = new \Depage\Db\Schema($pdo);
        $schema->setReplace('prefix');
        $schema->loadGlob(__DIR__ . '/../Sql/*.sql');

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0;');
        $schema->update();
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1;');
    }
    // }}}
}

/* vim:set ft=php sw=4 sts=4 fdm=marker et : */
