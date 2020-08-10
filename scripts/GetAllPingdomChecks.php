<?php
/**
 * Get the list of all Pingdom checks
 *
 * @author tuanha
 */
require(__DIR__.'/../bootstrap.php');

$pingdomCheck = new PingdomBuddy\PingdomCheck();

if ($pingdomCheck->getChecks()) {
    if (property_exists(json_decode($pingdomCheck->result), 'error')) {
        $serverError = json_decode($pingdomCheck->result)->error;
        print "************\nError Code: {$serverError->statuscode}\n";
        print "Error Description: {$serverError->statusdesc}\n************\n";
    } else {
        $checks = json_decode($pingdomCheck->result)->checks;
        $hostnames = [];
        $fh = fopen(__DIR__ . '/../output/' . 'pingdom_checks.csv', 'w');
        fputcsv($fh, ['Check ID', 'Created (in UTC)', 'Name', 'Hostname', 'Type', 'Verify Certificate', 'Status']);
        foreach ($checks as $key => $check) {
            $hostname = trim($check->hostname);
            array_push($hostnames, trim($hostname));
            fputcsv($fh, [
                $check->id, 
                Carbon\Carbon::createFromTimestamp($check->created)->setTimezone('UTC')->toDateTimeString(), 
                $check->name, 
                $hostname, 
                $check->type, 
                $check->verify_certificate, 
                $check->status
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
    }
} else {
    print "Failed to make request to the Pingdom\n";
    print "************\n{$pingdomCheck->executionError}\n************\n";
}
