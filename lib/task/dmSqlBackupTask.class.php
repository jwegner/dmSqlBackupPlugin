<?php

class dmSqlBackupTask extends dmContextTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
//      new sfCommandOption('module', null, sfCommandOption::PARAMETER_REQUIRED, 'The module name'),
      new sfCommandOption('tag', null, sfCommandOption::PARAMETER_OPTIONAL, 'add a tag name to the backups file name', ''),
    ));

    $this->namespace = 'dm';
    $this->name = 'sql-backup';
    $this->briefDescription = 'Creates a sql backup';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();

    $connection = dmDb::table('DmPage')->getConnection();

    $this->get('sql_backup')
    ->setLogCallable(array($this, 'customLog'))
    ->execute($connection, $arguments, $options);
  }

  public function customLog($msg)
  {
    return $this->logSection('diem-sql-backup', $msg);
  }
}