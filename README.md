
#SSH Remote Actions
It is a toolbox where you can add modules to run shell commands through SSH.

It Is made using **Silex & phpseclib**, phpseclib can easily be changed by PHP SSH2.

The login is made trying to open a connection to the remote host, but you are free to make your own auth method and use a secured SSH public key.

*The purpose of this code is learning some Silex and Composer autoload by doing something useful at the same time.*

##Installation##
```bash
git clone git://github.com/cristianszwarc/sshra.git
cd sshra
composer install
```
*be sure of only make public the web folder. You may run this only on localhost to prevent your non encrypted password go over the net*

##Configuration File##
The ```config/config.yml``` file can keep module configurations, the options will be passed to each module

```
config:
    someModule:
        someOption: "the value"

    scripts:
        Domain Scripts: "/var/www/vhosts/*/scripts/*.sh"
        User Scripts: "/home/*/scripts/*.sh"
        Do something: "/path/to/doSomeThing.sh"
```

##Included Modules##

###Scripts###
This module will show a list of available scripts and will allow the execution of these scripts.

You can define the available scripts on the config file. You can add the exact path to an specified script or add a dynamic group of scripts.

Specified scripts:
```
config:
    scripts:
        Command One: "/path/to/one.sh"
        Command Two: "/path/to/two.sh"
```

Dynamic group of scripts:
```
config:
    scripts:
        User Scripts: "/home/*/scripts/*.sh"
        Shared Scripts: "/some/shared/scripts/*.sh"
```
###Dashboard###
This module generates the home page where you can get a review or alerts coming from all your modules.

###Ram###
A simple example of the use of the **Dashboard**, each module can run commands at login and save a preview or alert on the Dashboard area.

##Licence##
Use without restrictions, learn something and give a feedback