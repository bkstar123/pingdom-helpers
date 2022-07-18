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
function readTimeForHuman($second)
{
    if ($second < 60) {
        return "$second seconds";
    } else if ($second >= 60 && $second < 3600) {
        $extraSeconds = readTimeForHuman($second % 60);
        $minutes = ($second - ($second % 60)) / 60;
        return "$minutes minutes $extraSeconds";
    } else if ($second >= 3600 && $second < 86400) {
        $extraMinutes = readTimeForHuman($second % 3600);
        $hours = ($second - ($second % 3600)) / 3600;
        return "$hours hours $extraMinutes";
    } else if ($second >= 86400) {
        $extraHours = readTimeForHuman($second % 86400);
        $days = ($second - ($second % 86400)) / 86400;
        return "$days days $extraHours";
    }
}
$writeToReport = function ($checkID) use ($from, $to, $fh, $pingdomCheck) {
    $checkID = trim($checkID);
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
                readTimeForHuman($report->status->totaldown),
                readTimeForHuman($report->status->totalup),
                readTimeForHuman($report->status->totalunknown),
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
