<?php

class dmSqlBackupAdapterMysql extends dmSqlBackupAdapter {

  public function getInfos() {
        $dsn = $this->connection->getOption('dsn');

        $arrConnectionData = array(
            'user' => $this->connection->getOption('username'),
            'pass' => $this->connection->getOption('password'),
            'host' => preg_replace('/mysql\:host=([-\.\w]+);.*/i', '$1', $dsn),
            'name' => preg_replace('/mysql\:host=[-\.\w]+;dbname=([-\.\w]+);.*/i', '$1', $dsn)
        );

    $arrMatches = array();
        if (preg_match('/mysql\:host=[-\.\w]+;dbname=[-\.\w]+;port=([\d]+);.*/i', $dsn, $arrMatches)) {
            $arrConnectionData['port'] = $arrMatches[1];
    }

    $arrMatches = array();
        if (preg_match('/mysql\:host=[-\.\w]+;dbname=[-\.\w]+;port=[\d]+;unix_socket=([\/\.\w\d]+).*/i', $dsn, $arrMatches)) {
            $arrConnectionData['sock'] = $arrMatches[1];
    }

        return $arrConnectionData;
    }

    public function execute($file) {
        $infos = $this->getInfos();

        if (isset($infos['port']) && isset($infos['sock'])) {
            $format = 'mysqldump --skip-extended-insert -c -h "%s" -u "%s" -p"%s" -P"%d" -S"%s" "%s" > "%s"';
        } elseif (isset($infos['port'])) {
            $format = 'mysqldump --skip-extended-insert -c -h "%s" -u "%s" -p"%s" -P"%d" "%s" > "%s"';
        } elseif (isset($infos['sock'])) {
            $format = 'mysqldump --skip-extended-insert -c -h "%s" -u "%s" -p"%s" -S"%s" "%s" > "%s"';
    } else {
            $format = 'mysqldump --skip-extended-insert -c -h "%s" -u "%s" -p"%s" "%s" > "%s"';
    }

        $func_args = array();
        $func_args[] = $format;
        $func_args[] = $infos['host'];
        $func_args[] = $infos['user'];
        $func_args[] = $infos['pass'];

        if(isset ($infos['port'])) { $func_args[] = $infos['port']; }
        if(isset ($infos['sock'])) { $func_args[] = $infos['sock']; }

        $func_args[] = $infos['name'];
        $func_args[] = $file;

        $command = call_user_func_array('sprintf', $func_args);

    return $this->filesystem->execute($command);
  }
}
