<?php
function insertActivityLog($pdo, $userId, $actionId, $details) {
    try {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if ($ipAddress == '::1') {
            $ipAddress = getHostByName(getHostName()); // get local network ip
        }
        $deviceBrowser = getDeviceBrowser($_SERVER['HTTP_USER_AGENT']);
        $stmt = $pdo->prepare('INSERT INTO `activitylog` (`userId`, `actionId`, `details`, `ipAddress`, `deviceBrowser`) 
                            VALUES (:userId, :actionId, :details, :ipAddress, :deviceBrowser)');
        $stmt->execute([
            ':userId' => $userId,
            ':actionId' => $actionId,
            ':details' => $details,
            ':ipAddress' => $ipAddress,
            ':deviceBrowser' => $deviceBrowser
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Activity log insertion error: " . $e->getMessage()); // Log it
        echo json_encode(["success" => false, "error" => $e->getMessage()]); // Show exact error
        return false;
    }
}

function getDeviceBrowser($userAgent) {
    $osArray = [
        '/windows nt 10/i'    => 'Windows 10',
        '/windows nt 6.3/i'   => 'Windows 8.1',
        '/windows nt 6.2/i'   => 'Windows 8',
        '/windows nt 6.1/i'   => 'Windows 7',
        '/macintosh|mac os x/i' => 'MacOS',
        '/linux/i'            => 'Linux',
        '/ubuntu/i'           => 'Ubuntu',
        '/iphone/i'           => 'iPhone',
        '/android/i'          => 'Android',
    ];
    $browserArray = [
        '/chrome/i'  => 'Chrome',
        '/firefox/i' => 'Firefox',
        '/safari/i'  => 'Safari',
        '/edge/i'    => 'Edge',
        '/opera/i'   => 'Opera',
        '/msie/i'    => 'Internet Explorer',
    ];
    $os = 'Unknown OS';
    $browser = 'Unknown Browser';
    foreach ($osArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $os = $value;
            break;
        }
    }
    foreach ($browserArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $browser = $value;
            break;
        }
    }
    return "$browser on $os";
}
?>