# ShellPhant0m

**ShellPhant0m** is a lightweight PHP webshell with a built-in reverse shell launcher. It provides an interactive dropdown interface to trigger reverse connections using either Netcat or Socat, directly from the web interface.
---

<img width="770" height="431" alt="image" src="https://github.com/user-attachments/assets/833e2bcc-0ac5-4008-8010-07be8cf050d8" />
---
## Features

- Single-file PHP shell for remote deployment
- Supports both Netcat and Socat reverse shell connections
- Accepts Ngrok or TCP address directly
- Auto-parses the host and port
- Displays listener command to be run on attacker's machine

## Requirements

- PHP-enabled web server (e.g., Apache, Nginx with PHP)
- A public listener (Ngrok, VPS, port-forwarded server)

## Usage

1. Upload `ShellPhant0m.php` to the target server.
2. Access the shell via browser.
3. Choose either **Netcat** or **Socat** from the dropdown menu.
4. Paste your Ngrok or TCP address (e.g., `0.tcp.ap.ngrok.io:12345`).
5. Click **Connect** to trigger the reverse shell.

### Example Ngrok Setup

```bash
ngrok tcp 4444
````

Paste the generated address (e.g., `0.tcp.ap.ngrok.io:12345`) into PhantomShell.

### Example Listener (on your machine)

For **Netcat**:

```bash
nc -lvnp 4444
```

For **Socat**:

```bash
socat -d -d tcp-l:4444,reuseaddr,fork exec:/bin/bash,pty,stderr,setsid,sigint,sane
```

## Disclaimer

This tool is intended for **educational** and **authorized security testing** purposes only. Do not use it on systems you do not own or have explicit permission to test.

---
Anu by : [AndKnownMaly](https://github.com/andknownmaly)
