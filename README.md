# ImagePopup cookbook for PmWiki 

## Description

The ImagePopup cookbook is a plugin for PmWiki which after including an (:imagepopup:) directive in a wiki page gives you automatically image popups for images on a wiki page. That is when you click on an image it will open a larger popup window with the image allowing you to take a better look at the image. The popup of the image is intelligently placed such that it doesn't overlay the original image or doesn't overlap a specified surrounding box around the image so that info around the image is kept visible. Eg. see the example below where the popup does not overlap the text beside the image. If you click on another image the old popup window is removed and a new one is opened. You can also close the popup window by clicking on the image again or by clicking on the popup window.

The details of the ImagePopup cookbook are described on the [ImagePopup cookbook's description page](https://www.pmwiki.org/wiki/Cookbook/ImagePopup). 
The source code of the ImagePopup plugin is at https://github.com/harcokuppens/pmwiki-cookbook-imagepopup/, which also provides
a devcontainer in which you can see the plugin in action and further develop it. The devcontainer is based on the basic pmwiki devcontainer at https://github.com/harcokuppens/pmwiki-basic.

## Devcontainer

This repository defines a devcontainer with a basic pmwiki installation with the ImagePopup cookbook installed. You can use this repository to see the cookbook in action in the devcontainer. But you can also use the devcontainer the further develop this cookbook in vscode.

There are 2 ways  to start the devcontainer:

### Run with docker compose (without vscode):

To just run the container do:

     docker compose  -f .devcontainer/docker-compose.yml  up -d --build

You can view the logs with:

     docker compose  -f .devcontainer/docker-compose.yml  logs -f

### Run by opening devcontainer with vscode

In vscode you have `php` debugging support. Convenient if you want to develop a
cookbook `php` script.

Open project from folder in vscode with command

    'Dev Containers: Open folder in Container...'

or from command line when in folder:

      code .

The vscode editor will opening the folder detect the `.devcontainer/` folder and ask
you to "Reopen in Container". When you confirm then vscode will be opened in the
container. Vscode will use as workspace folder `/var/www/html/pmwiki` inside the
container.

The devcontainer is setup to use the user `www-data` used by apache and php as the
`remoteUser`. This setting makes the vscode editor to log in the container using the
`www-data` user. Which means that the vscode editor uses the `www-data` when editing
files making sure they can always be read by apache and php.

Within vscode you can then easily edit and debug php code. The `Dockerfile` for the
container has already buildin a `launch.json` for debugging with xdebug within
vscode. So everything is already setup to directly debug php code.

## Usage

Open in browser:

     http://localhost:8080
              or
     https://localhost:8443

### Credentials

PmWiki allows you to login as an user to edit pages or do any online configuration.

By default we already configure two accounts in `data/local/config.php`:

- a test user which can edit pages:
  - username: testuser
  - password: testuser
- an administrator account which has all rights:
  - username: admin
  - password: admin

### Local folders are binded into container

Mount binding lets you conveniently edit files locally on your docker host, and lets
you persist these folders when the container is restarted from scratch.

The following folders will be binded into the container:

- `wiki.d` for wiki pages
- `uploads`: for attachments/pictures in pages
- `local`: for the `local.php` configuration file
- `cookbook/imagepopup`: for binding only my cookbook's `php`
  script(s)
- `pub/imagepopup`: for binding only my cookbook's `pub` file(s)

Only the cookbook subdirectory is mounted, because as developer you are only
interested in that specific cookbook. Next to that the `pub/` directory also
containers files installed by PmWiki which you do not want to mess with.

### Open bash shell in container

Because the `docker exec` command by default uses the user defined in `Dockerfile` or
`docker-compose.yml` the following command will open a bash shell with the `root`
user:

    docker exec -it pmwiki-imagepopup-1 bash

However, the apache server and php code engine will operate using the `www-data`
user. Hence, when editing via a bash shell one can better open the shell with the
`www-data` user:

    docker exec -it -u www-data pmwiki-imagepopup-1 bash
