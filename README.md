# SmartService
Simple rpc framework which is based on Swoole.

# Quick start
## Install Swoole (php extension)
## Create a new server instance demo.
Please rename Conf-example folder to Conf, and then execute command "php SmartService.php start. You will get a service which is named Example."

You can find the process whith command ("ps -ef | grep example").
If you need a new server, Please add a new class like `Config/Server/Example.php`, usually you need other config file (Config/Client/Example.php) for client.
