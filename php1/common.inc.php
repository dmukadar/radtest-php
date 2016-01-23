<?php

$filepath = './uploads/spool.txt';
$uploaddir = dirname($filepath);

$dbconfig = (object) array(
    'host'=>'localhost',
    'dbname'=>'perltest',
    'uname'=>'tester',
    'password'=>'0',
);

class DbLink {
    public $db;
    public $dsn;
    public $username;
    public $password;

    public function __construct($config)
    {
        $this->dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8', $config->host, $config->dbname);
        $this->username = $config->uname;
        $this->password = $config->password;
    }

    public function connect()
    {
        $this->db = new PDO($this->dsn, $this->username, $this->password);
    }
    
    public function save($row) {
        $params = array(
            ':sessionId'       => $row['Acct-Session-Id'],
            ':callingStationId'=> isset($row['Calling-Station-Id']) ? $row['Calling-Station-Id'] : null,
            ':calledStationId' => $row['Called-Station-Id'],
            ':setupTime'       => $this->timeConvert($row['setup-time']),
            ':connectTime'     => null,
            ':disconnectTime'  => $this->timeConvert(@$row['disconnect-time']),
        );

        $query = <<<EOL
INSERT INTO `cdr` (`acctSessionId`, `callingStationId`, `calledStationId`, `setupTime`, `connectTime`, `disconnectTime`)
VALUES (:sessionId, :callingStationId, :calledStationId, :setupTime, :connectTime, :disconnectTime)
EOL;

        $statement = $this->db->prepare($query);
        $statement->execute($params);
        $result = $statement->rowCount();

        if ($result == 0) {
            $query = <<<EOL
UPDATE `cdr`
    SET `callingStationId` = :callingStationId
    , `calledStationId` = :calledStationId
    , `setupTime` = :setupTime
    , `connectTime` = :connectTime
    , `disconnectTime` = :disconnectTime
 WHERE
    `acctSessionId` = :sessionId
EOL;
            $statement = $this->db->prepare($query);
            $statement->execute($params);
            $result = $statement->rowCount();
        }
        return $result > 0;
    }
    
    public function isCallType($row) {
        return isset($row['Acct-Status-Type']) && $row['Acct-Status-Type'] == 'Start';
    }

    public function timeConvert($radiusDate)
    {
        if (empty($radiusDate)) return null;

        $formatter = '@^(?P<weekday>[A-Z]{3}) (?P<month>[A-Z]{3}) (?P<day>\d{2}) (?P<hour>\d{2}):(?P<minute>\d{2}):(?P<second>\d{2}):\d+ (?P<year>\d{4})$@';
        preg_match($formatter, $radiusDate, $datePiece);

        if (! empty($datePiece)) {
            $timestamp = mktime(
                $datePiece['hour'], $datePiece['minute'], $datePiece['second'],
                date('n', strtotime($datePiece['month'])),
                $datePiece['day'], $datePiece['year']
            );
            
            if ($timestamp) return date('Y-m-d H:i:s', $timestamp);
        }
        return null;
    }
}