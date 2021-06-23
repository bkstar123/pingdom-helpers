<?php
/**
 * Get the list of all Pingdom checks
 *
 * @author tuanha
 */
require(__DIR__.'/../bootstrap.php');

$pingdomCheck = new PingdomBuddy\PingdomCheck();

try {
   $checks = $pingdomCheck->getChecks(); 
   if ($checks) {
        $hostnames = [];
        $fh = fopen(__DIR__ . '/../output/' . 'pingdom_checks.csv', 'w');
        fputcsv($fh, ['Check ID', 'Created (UTC)', 'Name', 'Hostname', 'Tags', 'Type', 'Verify Certificate', 'Status', 'Last Check Time (UTC)']);
        foreach ($checks as $key => $check) {
            $hostname = trim($check->hostname);
            array_push($hostnames, trim($hostname));
            fputcsv($fh, [
                $check->id, 
                Carbon\Carbon::createFromTimestamp($check->created)->setTimezone('UTC')->toDateTimeString(), 
                $check->name, 
                $hostname, 
                property_exists($check, 'tags') ? json_encode(array_column($check->tags, 'name')) : '',
                $check->type, 
                $check->verify_certificate, 
                $check->status,
                property_exists($check, 'lasttesttime') ? Carbon\Carbon::createFromTimestamp($check->lasttesttime)->setTimezone('UTC')->toDateTimeString() : '',
            ]);
        }
        fclose($fh);
        $occurrences = array_count_values($hostnames);
        $duplicatedCheckHostnames = array_filter($hostnames, function ($hostname) use ($occurrences) {
            return $occurrences[$hostname] > 1;
        });
        $fh = fopen(__DIR__ . '/../output/' . 'pingdom_check_duplicated_hostnames.txt', 'w');
        fputs($fh, implode(',', $duplicatedCheckHostnames));
        fclose($fh);
        print "Done\n";
    } else {
        print "Failed to make request to the Pingdom\n";
    }
} catch (Exception $e) {
    print "Error: {$e->getMessage()}\n";
}



