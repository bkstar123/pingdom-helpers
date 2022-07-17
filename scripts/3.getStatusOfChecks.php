<?php
/**
 * Get the status of given Pingdom checks
 *
 * @author tuanha
 */
require(__DIR__.'/../bootstrap.php');

$pingdomCheck = new PingdomBuddy\PingdomCheck();

$list = file_get_contents(__DIR__ . '/../input/' . $_ENV['CHECK_FILE']);
$checks = explode(',', $list);
$fh = fopen(__DIR__ . '/../output/' . 'pingdom_checks_status.csv', 'w');
fputcsv($fh, ['Check ID', 'Status', 'Last Check Time (UTC)']);
$pingdomCheck = new PingdomBuddy\PingdomCheck();
foreach ($checks as $index => $check) {
    try {
        $data = $pingdomCheck->getCheck($check);
        fputcsv($fh, [
            $check,
            $data->status,
            property_exists($data, 'lasttesttime') ? Carbon\Carbon::createFromTimestamp($data->lasttesttime)->setTimezone('UTC')->toDateTimeString() : '',
        ]);
        print ceil(($index + 1)/count($checks)*100). "% - Completed check ID $check\n";
    } catch (Exception $e) {
        fputcsv($fh, [
            $check,
            '',
            '',
        ]);
    }
}
fclose($fh);
