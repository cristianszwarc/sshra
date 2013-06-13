
#SSH Remote Actions
This is a toolbox where you can add modules to run shell commands through SSH.

This is made using **Silex & phpseclib**, phpseclib can easily be changed by PHP SSH2.

The login is made trying to open a connection to the remote host, but you are free to make your own auth method and use a secured SSH public key.

*The purpose of this code is learning some Silex and Composer autoload by doing something useful at the same time.*

##Screenshots##

*Scritps module*
![Scripts Module](https://raw.github.com/cristianszwarc/sshra/master/screenshots/04.png)

*Scritps module - Run*
![Scripts Module - Run](https://raw.github.com/cristianszwarc/sshra/master/screenshots/06.png)


##Installation##
```bash
git clone git://github.com/cristianszwarc/sshra.git
cd sshra
composer install
```
*Be sure of only make public the web folder. 
You may run this on your localhost to prevent enter your password in a form over a non encrypted web page.*

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

http://cristianszwarc.com.ar/