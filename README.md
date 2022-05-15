# Nameless-Websend
Send commands from your website to your Minecraft server using the Websend plugin

## Requirements
- NamelessMC version 2 pre-release 11+
- Websend
    - [MC 1.8-1.12](https://dev.bukkit.org/projects/websend)
    - [MC 1.13+](https://github.com/samerton/Websend/releases/tag/v2.5.11)

## Installation
- Upload the contents of the **upload** directory straight into your NamelessMC installation's directory
- Activate the module in the StaffCP -> Modules tab

Install the Websend plugin on your minecraft server. (re)start it and open `plugins/Websend/config.txt`. In here,
uncomment `WEBLISTENE_ACTIVE` and set it to true. After this, uncommment `ALTPORT` and set it to an open port on your server. 
This port will be used by the plugin to spin up a small webserver the module can communicate with. Now head back to your website, go to the websend tab and input your connection details. 

`Connection Adress` Will be the url / IP to your server.
`Connection Port` Will be the port you inputted in `ALTPORT`.
`Connection Password` Will be the value of `PASS` in the Websend config.

After this, submit it. You can then click on one of the hooks in the list and enable them / configure commands.
