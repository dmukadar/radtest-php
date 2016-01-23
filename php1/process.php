<?php
require "common.inc.php";

if (!file_exists($filepath)) throw new RunTimeException ("File not found", 404);

$stat = (object)array(
    'lineCount'=>0,
    'recordCount'=>0,
    'callCount'=>0,
);

$removeFile=false;
$handle = @fopen($filepath, "r");
if ($handle) {
    $removeFile = true;
    $dbLink = new DbLink($dbconfig);
    $dbLink->connect();
    
    $recordBuffer = "";
    while (($buffer = fgets($handle, 4096)) !== false) {
        $stat->lineCount++;
        
        $buffer = trim($buffer);
        //reset buffer when found new Timestamp on beginning of line
        if (preg_match('/^(sun|mon|tue|wed|thu|fri|sat).*20\d{2}$/i', $buffer)) {
            $recordBuffer = "";
        } elseif (! empty($buffer)) {
            //construct an http query like string
            $buffer = str_replace(array(' = ', '"'), array('=', ''), $buffer);
            $recordBuffer .= $buffer . "&";
        } else {
            //make it work
            parse_str($recordBuffer, $record);
            if (! isset($record["Acct-Session-Id"])) {
                $recordBuffer = "";
                continue;
            } else {
                if ($dbLink->save($record)) $stat->recordCount++;
                if ($dbLink->isCallType($record)) $stat->callCount++;
            }
        }
    }
    fclose($handle);
}

if ($removeFile) {
    $dir = dirname($filepath);
    copy($filepath, $dir . '/processed-'.date('Y-m-d_His'));
    unlink($filepath);
}

header("Content-type: application/json");
echo json_encode($stat);