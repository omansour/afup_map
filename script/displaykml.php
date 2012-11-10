<?php


require_once __DIR__.'/../vendor/autoload.php';
//require_once __DIR__.'/../src/Afup/Attendees/Geocode.php';


$adapter  = new \Geocoder\HttpAdapter\BuzzHttpAdapter();
$geocoder = new \Geocoder\Geocoder();
$geocoder->registerProviders(array(
    new \Geocoder\Provider\GoogleMapsProvider($adapter)
    ));

$dumper = new \Geocoder\Dumper\KmlDumper();


// geocoding

$rs = array();

// open csv 
$adress = file($argv[1], FILE_IGNORE_NEW_LINES);
foreach ($adress as $line) {
    echo $line."\n";
    sleep(2);
    $t = explode(";", $line);
    try {
    $rs[] = $geocoder->geocode($t[0].', '.$t[1].' '.$t[2].', '.$t[3]);
    } catch (\Geocoder\Exception\NoResultException $e) {
        // nothing
    }
}

$kml = <<<KML
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
    <Document>
KML;

foreach ($rs as $k => $r) {
    $strKml = $dumper->dump($r);
    $pattern = '<Placemark>
            <name><![CDATA[%s]]></name>
            <description><![CDATA[%s]]></description>
            <Point>
                <coordinates>%.7F,%.7F,0</coordinates>
            </Point>
        </Placemark>';
    $kml .= sprintf($pattern, $k, $k, $r->getLongitude(), $r->getLatitude());
}

$kml .= <<<KML
</Document>
</kml>
KML;

// $dumper = new \Geocoder\Dumper\KmlDumper();
// $strKml = $dumper->dump($result);



echo $kml;