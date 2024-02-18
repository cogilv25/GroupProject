# Cleaning App
## Introduction

---
## How to get up and running
First a small disclaimer, these instructions for Windows and they use [XAMPP](https://www.apachefriends.org/) to provide the database (MariaDB), web server (Apache) and PHP(8.0+) required. I also have included instructions for installing Git and cloning this repo into a local folder which will allow us to run the application, however, if you intend to contribute then you will need to fork the repo and push to your fork before submitting a pull request.

### Git
This guide uses Git BASH, but you could use Git GUI.
- Goto [Git For Windows](https://gitforwindows.org/) and click download near the top of the page.
- Run the installer, it doesn't matter where you install it.
- When the option appears Override the default branch name to main as this is what the project uses, the other option that matters is commit Unix-style line endings, don't commit as-is.
- When installation is done run git BASH and navigate to the parent directory of where you want to clone the repo ( e.g. if you want the repo in /c/GP you navigate to /c ).
- Run the command `git clone https://github.com/cogilv25/GroupProject.git`
- You can now close Git BASH.

### XAMPP
- Goto [XAMPP](https://www.apachefriends.org/) and download the latest version for Windows (currently 8.2.12).
- Run the installer, if you get a warning about UAC don't install in C:/Program Files/ or any other Windows folders that have write protection, or, alternatively disable uac, but this has some security implications..
- We need Apache, MySQL and php. You can untick everything else if you don't want them. Otherwise it's a straightforward install.
- Run XAMPP Control panel and click on the config button for apache, then Apache (httpd.conf).
- Scroll down to DocumentRoot and change the 2 instances of "../../htdocs" to the path to Website/public inside the cloned repo.
- Ensure this line is not commented: `LoadModule rewrite_module modules/mod_rewrite.so`

### Composer
- Goto [Composer](https://getcomposer.org/download/), download and run Composer-Setup.exe.
- When asked for a command-line PHP browse to your XAMPP installation directory and select php/php.exe and tick add this PHP to your PATH. The rest of the installer is just next->finish.
- Open a **NEW** instance of git BASH and navigate to the Website folder within the group project repo.
- Run the command `composer update`

### Start the Server
- Open XAMPP Control Panel.
- Click start on Apache and MySQL.

### MySQL Workbench
- I'm sure we all know how to use MySQL Workbench
- Install it if it's not installed and connect to the database the username will be root, their is no password and the hostname is localhost or 127.0.0.1. The app uses these login credentials so don't change them.
- Run db_init_script.sql on the database ( script is located in Project repo/dev/bin ).

### Check the App Out
- Open a web browser and navigate to localhost or 127.0.0.1