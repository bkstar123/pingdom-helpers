<?php
/**
 * Get the list of all Pingdom checks
 *
 * @author tuanha
 */
require(__DIR__.'/../bootstrap.php');
require(__DIR__ . '/../input/2.date_range.php');
$list = file_get_contents(__DIR__ . '/../input/2.checks');
$checks = explode(',', $list);
$pingdomCheck = new PingdomBuddy\PingdomCheck();
$fh = fopen(__DIR__ . '/../output/' . 'pingdom_checks_summary_avg_reports.csv', 'w');
fputcsv($fh, [
    'From',
    'To',
    'Check ID',
    'Total Downtime (seconds)',
    'Total Uptime (seconds)',
    'Total Unknown (seconds)',
    '% Uptime',
    '% Unknown'
]);
$writeToReport = function ($checkID) use ($from, $to, $fh, $pingdomCheck) {
    $fromTS = Carbon\Carbon::parse($from, 'UTC')->timestamp;
    $toTS = Carbon\Carbon::parse($to, 'UTC')->timestamp;
    try {
        $report = $pingdomCheck->getCheckSummaryAvg(
           $checkID,
           $fromTS,
           $toTS
       );
        if ($report) {
            fputcsv($fh, [
                $from,
                $to,
                $checkID,
                $report->status->totaldown,
                $report->status->totalup,
                $report->status->totalunknown,
                round($report->status->totalup*100/($toTS - $fromTS), 2),
                round($report->status->totalunknown*100/($toTS - $fromTS), 2),
            ]);
            print "$checkID => completed\n";
        } else {
            print "Failed to make request to the Pingdom\n";
            fputcsv($fh, [
                $from,
                $to,
                $checkID,
                '',
                '',
                '',
                '',
                '',
            ]);
        }
    } catch (Exception $e) {
        print "$checkID => Error: {$e->getMessage()}\n";
        fputcsv($fh, [
                $from,
                $to,
                $checkID,
                '',
                '',
                '',
                '',
                '',
            ]);
    }
};
foreach ($checks as $checkID) {
    $writeToReport($checkID);
}
fclose($fh);
