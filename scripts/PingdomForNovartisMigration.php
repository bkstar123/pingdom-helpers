<?php
/**
 * Get the list of all Pingdom checks for Novartis migration
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
        $output = array_filter($checks, function ($check) {
            return preg_match('/\|\| nopc01mstr91h8wprod/', $check->name);
        });

        $fh = fopen(__DIR__ . '/../output/' . 'pingdom_checks.csv', 'w');
        fputcsv($fh, ['Check ID', 'Name', 'Hostname', 'Status']);

        foreach ($output as $key => $check) {
            fputcsv($fh, [$check->id, $check->name, $check->hostname, $check->status]);
        }
        fclose($fh);
        print "Done\n";
    }
} else {
    print "Failed to make request to the Pingdom\n";
    print "************\n{$pingdomCheck->executionError}\n************\n";
}
