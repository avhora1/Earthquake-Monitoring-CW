<?php
header('Content-Type: application/json');

$lat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$lon = isset($_GET['lon']) ? (float)$_GET['lon'] : null;

if ($lat === null || $lon === null) {
    echo json_encode(['city'=>'','country'=>'','step'=>'missing input']); exit;
}

$url = "https://nominatim.openstreetmap.org/reverse?format=json"
     . "&lat=$lat&lon=$lon&zoom=10&addressdetails=1";

// --- cURL robust fetch ---
function curl_get_url($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Required by Nominatim TOS: real or project contact!
    curl_setopt($ch, CURLOPT_USERAGENT, "YourProject/1.0 (contact@email.com)");
    // If you have SSL CA issues, you can uncomment the next line
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
}

$json = curl_get_url($url);
if ($json === FALSE || !$json) {
    echo json_encode(['city'=>'','country'=>'','step'=>'curl fail','url'=>$url]); exit;
}
$data = json_decode($json, true);

$address = isset($data['address']) ? $data['address'] : [];

$city = $address['city']           ?? 
        $address['town']           ??
        $address['village']        ??
        $address['municipality']   ??
        $address['state_district'] ??
        $address['county']         ??
        $address['region']         ??
        $address['state']          ??
        '';

$country = $address['country'] ?? '';

echo json_encode([
    'city' => $city,
    'country' => $country
    // , 'debug' => $address // uncomment for debugging
]);
exit;
?>