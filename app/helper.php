if (! function_exists('serialToDate')) {
function serialToDate($serialNumber) {
$unixTimestamp = ($serialNumber - 25569) * 86400; // adjust for Unix epoch and convert to seconds
$date = \Carbon\Carbon::createFromTimestamp($unixTimestamp);
return $date->format('d-m-Y');
}
}
