# Basic PmWiki for developing cookbook scripts.

## Description

This repository defines a basic pmwiki installation in devcontainer which you can use
to develop cookbook scripts. You can also use this repository as base to publish your
cookbook with example pages. This allows you to directly view the cookbook in action
using docker compose.

The following config files are used in the container

- `.devcontainer/.env`

  Its content is:

      COOKBOOK=mycookbook

  which defines the name of your cookbook used in the
  `.devcontainer/docker-compose.yml` file. Edit in this file your cookbook name. The
  name should match the subfolders in `data/cookbook/` and `data/pub/`. By default we
  set this to `mycookbook` and made the matching subdirs in `data/cookbook/` and
  `data/pub/` in this repository, so that directly can start experimenting with a
  `mycookbook` cookbook.

- `.devcontainer/devcontainer.json`

  Configuration file for vscode the devcontainer which specifies that vscode

  - use as workspace folder `/var/www/html/pmwiki`
  - use docker compose with file `.devcontainer/docker-compose.yml`
  - use `pmwiki` container in vscode as devcontainer
  - use some plugins in vscode for developing `php`

- `.devcontainer/docker-compose.yml`

  Configuration for the containers to run, which can be used within vscode, but also
  without vscode when using `docker compose` directly. This compose has only one
  container, the container `pmwiki` defined in `.devcontainer/pmwiki/`. Because we
  only use one container we could have defined to use a `Dockerfile` directly in
  vscode's `.devcontainer/devcontainer.json` file. However the
  `.devcontainer/docker-compose.yml` lets us easily define extra options in how to
  use the container. Eg. port mappings and bindings.

  To avoid port conflicts we map port 80 to 8080 and port 443 to 8443, where we
  expose them only on the localhost interface. Edit this file if you want to use
  other port mappings. You can also change the used version of PmWiki in the
  `PMWIKI_VERSION` argument to the PmWiki container.

  The docker compose configuration bind mounts the following local folders inside the
  local `./data/` folder into the `/var/www/html/pmwiki/` folder inside the `pmwiki`
  container:

  - `wiki.d` for wiki pages
  - `uploads`: for attachments/pictures in pages
  - `local`: for the `local.php` configuration file
  - `cookbook/${COOKBOOK}`: for binding only my cookbook's `php` script(s)
  - `pub/${COOKBOOK}`: for binding only my cookbook's `pub` file(s)

  The name of your cookbook in the `${COOKBOOK}` variable in the
  `.devcontainer/docker-compose.yml` is read from the `.devcontainer/.env` file which
  gets applied before parsing the `docker-compose.yml` file. Edit the
  `.devcontainer/.env` file if you want to change your cookbook name. The `COOKBOOK`
  environment variable inside your container gets also set to the name of your
  container.

  Note that these bindings could also be defined in the
  `.devcontainer/devcontainer.json` file, but by defining it in the
  `.devcontainer/docker-compose.yml` the bindings also are applied when not using
  vscode.

- `.devcontainer/pmwiki/Dockerfile`

  The `Dockerfile` defines how the PmWiki container is build. It installs the PmWiki
  version `PMWIKI_VERSION` defined in the `docker-compose.yml` file. The webserver
  used is `apache` on which also `SSL` is enabled. The PmWiki website uses the
  configuration set in `local/config.php` in the local repository folder. It also
  uses the local folders `wiki.d`, `uploads`,`local` and `cookbook` by bind mounting
  them in the container. As developer you can then easily place your cookbook script
  and test pages locally and use them in the container.

  The container is run with the `root` user. This is the same as on a normal linux
  distribution where the apache server is initially run as root to be able to open
  the lower privilige ports, after which the apache server switches to the `www-data`
  user.

## Setup your cookbook folder

When starting to develop your cookbook make folders for it. Here an example for a
cookbook named `examplecookbook`.

     COOKBOOK=examplecookbook
     mkdir data/cookbook/$COOKBOOK
     mkdir data/pub/$COOKBOOK

then edit `.devcontainer/.env` to make it look like:

     COOKBOOK=examplecookbook

Then you can start the container to work on your cookbook. This can be done either
with our without vscode explained in the next two sections.

The `COOKBOOK` environment variable inside your container gets also set to the name
of your container.

## Run with docker compose (without vscode):

To just run the container do:

     docker compose  -f .devcontainer/docker-compose.yml  up -d --build

You can view the logs with:

     docker compose  -f .devcontainer/docker-compose.yml  logs -f

Then open in browser:

     https://localhost:8080
              or
     https://localhost:8443

Because the `docker exec` command by default uses the user defined in `Dockerfile` or
`docker-compose.yml` the following command will open a bash shell with the `root`
user:

    docker exec -it pmwiki-pmwiki-1 bash

However, as explained above, the apache server and php code engine will operate using
the `www-data` user. Hence, when editing via a bash shell one can better open the
shell with the `www-data` user:

    docker exec -it -u www-data pmwiki-pmwiki-1 bash

## Run by opening devcontainer with vscode

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

Then open in browser:

     https://localhost:8080
              or
     https://localhost:8443

Within vscode you can then easily edit and debug php code. The `Dockerfile` for the
container has already buildin a `launch.json` for debugging with xdebug within
vscode. So everything is already setup to directly debug php code.

## Credentials

PmWiki allows you to login as an user to edit pages or do any online configuration.

By default we already configure two accounts in `data/local/config.php`:

- a test user which can edit pages:
  - username: testuser
  - password: testuser
- an administrator account which has all rights:
  - username: admin
  - password: admin

## Helper scripts

Helper scripts available in the `bin/` directory of this repository, but also builtin
to the container for direct usage within the container.

- `pmwiki_exportfile` `INPUTFILE` `OUTPUTFILE`

  Exports latest source of wikipage `INPUTFILE` as text in `OUTPUTFILE`.

- `pmwiki_importfile` `INPUTFILE` `OUTPUTFILE`

  Imports text content of `INPUTFILE` as a wikipage in `OUTPUTFILE`.

- `pmwiki_remote_ssh_import` `USER@REMOTEHOST:REMOTEPMWIKIDIR`

  **Run this script from a shell in your container.**

  Mirror a remote site without overwriting the new cookbook we are locally
  developing. The name of the new cookbook is determined from the `COOKBOOK`
  environment variable. Your cookbook `X` can consist of directories
  `pmwiki/cookbook/X/` and `pmwiki/pub/X/`. When the remote site is mirrored we make
  sure that we keep these folders of your cookbook `X`, because when mirroring from a
  remote side not having these folders they would get removed! So what you finally
  get is the remote `cookbook/` and `pub/` folder with your cookbook folders added.

  The argument `USER@REMOTEHOST:REMOTEPMWIKIDIR` is an rsync remote location using
  the SSH protocol to mirror the files.

  This script's behavior:

  - the name of your cookbook is taken from the COOKBOOK environment variable.
  - the folder /var/www/html/pmwiki is taken as the local pmwiki folder into which
    data gets mirrored.
  - files bigger then 0.5MB are skipped from mirroring

  Using this script we can easily check whether your new cookbook also works in an
  existing production site.
