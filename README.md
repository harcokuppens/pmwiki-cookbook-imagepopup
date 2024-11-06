# ImagePopup cookbook for PmWiki 

## Description

The ImagePopup cookbook is a plugin for PmWiki which after including an (:imagepopup:) directive in a wiki page gives you automatically image popups for images on a wiki page. That is when you click on an image it will open a larger popup window with the image allowing you to take a better look at the image. The popup of the image is intelligently placed such that it doesn't overlay the original image or doesn't overlap a specified surrounding box around the image so that info around the image is kept visible. Eg. see the example below where the popup does not overlap the text beside the image. If you click on another image the old popup window is removed and a new one is opened. You can also close the popup window by clicking on the image again or by clicking on the popup window.

The details of the ImagePopup cookbook are described on the [ImagePopup cookbook's description page](https://www.pmwiki.org/wiki/Cookbook/ImagePopup). 
The source code of the ImagePopup plugin is at https://github.com/harcokuppens/pmwiki-cookbook-imagepopup/, which also provides
a devcontainer in which you can see the plugin in action and further develop it. The devcontainer is based on the basic pmwiki devcontainer at https://github.com/harcokuppens/pmwiki-basic.

## Quickstart to view cookbook in action

### Get cookbook in PmWiki running in devcontainer

This repository defines a devcontainer with a basic pmwiki installation with the
MyCookbook cookbook installed. You can use this repository to see the cookbook in
action in the devcontainer by doing:

1.  Run the container with docker using the command:

        docker compose -f .devcontainer/docker-compose.yml up -d --build

2.  Then view the wiki in the browser at:

        http://localhost:8080
             or
        https://localhost:8443

Then at the wiki's HomePage the cookbook should already be shown in action.

### Credentials

To play with the cookbook's code in the page you can login and edit the HomePage.

By default we already configured two accounts in `data/local/config.php`:

- a test user which can edit pages:
  - username: testuser
  - password: testuser
- an administrator account which has all rights:
  - username: admin
  - password: admin

## Develop cookbook in devcontainer using vscode

You can use this repository to see the cookbook in action in the devcontainer, but
you can also use the devcontainer the further develop this cookbook in vscode.

In vscode you have `php` debugging support. Convenient if you want to develop a
cookbook `php` script.

### Open the devcontainer in vscode

To open the devcontainer in vscode run the command

To open the devcontainer in vscode first open the project folder in vscode, and then
inside vscode run the command

    'Dev Containers: Open folder in Container...'

and then select the project folder.

You can also open the devcontainer from the command line by going to the project
folder and then run:

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

    docker exec -it pmwiki-imagepopup-ctr bash

However, the apache server and php code engine will operate using the `www-data`
user. Hence, when editing via a bash shell one can better open the shell with the
`www-data` user:

    docker exec -it -u www-data pmwiki-imagepopup-ctr bash

### Helper scripts

Helper scripts available in the `bin/` directory of this repository, but also builtin
to the container for direct usage within the container.

- `pmwiki_exportfile` `INPUTFILE` `OUTPUTFILE`

  Exports latest source of wikipage `INPUTFILE` as text in `OUTPUTFILE`.

- `pmwiki_importfile` `INPUTFILE` `OUTPUTFILE`

  Imports text content of `INPUTFILE` as a wikipage in `OUTPUTFILE`.

- `pmwiki_mirror_remote_site` `USER@REMOTEHOST:REMOTEPMWIKIDIR`

  Mirror a remote site without overwriting the new cookbook we are locally
  developing. **Run this script from a shell in your container.** This can be useful
  to test a new cookbook within an existing wiki site.

  The full documentation of `pmwiki_mirror_remote_site` is given when run without
  arguments:

      $ pmwiki_mirror_remote_site
      USAGE

        pmwiki_mirror_remote_site [-d SUBDIR]* [-c COOKBOOKNAME]* [-s MAXSIZE] [-l LOCALPMWIKIDIR]  USER@REMOTEHOST:REMOTEPMWIKIDIR

      DESCRIPTION

        Mirror a remote site without overwriting the new cookbook we are locally developing.
        In that way we can test our cookbook within the remote setup and data.

        The local site could have a different pmwiki install as the remote site.
        We can use this to test the remote site in a new pmwiki version.
        Only added items to an original pmwiki installation are mirrored. That is only the
        configuration, cookbook extensions,  the wiki pages and its uploads are
        mirrored from the remote site. The means we mirror only the subfolders local/, cookbook/,
        wiki.d/, uploads/ and pub/.

        The name of the new cookbook is determined from the COOKBOOK environment variable.
        Your cookbook X can consist of directories pmwiki/cookbook/X/ and pmwiki/pub/X/. When
        the remote site is mirrored we make sure that we keep these folders of your cookbook X,
        because when mirroring from a remote side not having these folders they would get removed!
        So what you finally get is the remote cookbook/ and pub/ folder with your cookbook folders
        added.

        The argument USER@REMOTEHOST:REMOTEPMWIKIDIR is an rsync remote location using the SSH protocol
        to mirror the files.

        This script's behavior:
          - the name of your cookbook is taken from the COOKBOOK environment variable.
          - the folder /var/www/html/pmwiki is taken as the local pmwiki folder into which data gets mirrored.
          - files bigger then 0.5MB are skipped from mirroring

        Options:

         -d SUBDIR
            Add extra sub directory in remote location to be mirrored. Multiple -d options may be specified.
         -c COOKBOOKNAME
            Specify a cookbook to excluded from mirroring. By default the value from the COOKBOOK environment
            variable is taken, but is ignored if this option is given. Multiple -c options may be specified.
         -s MAXSIZE
            Files with this size or larger are not mirrored. Default MAXSIZE=0.5m (half megabyte).
            With MAXSIZE=0 then all files are mirrored.
         -l LOCALPMWIKIDIR
            Specifiy a different location for the local PmWiki directory. Default is /var/www/html/pmwiki.

  By default the name of the cookbook is determined from the `COOKBOOK` environment
  variable, which by default is already set inside the containers environment.

