# Icebreaker Project

Icebreaker is a minimalistic approach developed by Isovera for setting up a Drupal 8 project. This work
was inspired by the [Drupal project](https://github.com/drupal-composer/drupal-project).

## Usage

### Create an Icebreaker project

```sh
composer create-project isovera/icebreaker:dev-master MY_PROJECT
```

For available options, see [the composer documentation](https://getcomposer.org/doc/03-cli.md#create-project) for the `create-project` command.

To complete, Icebreaker will prompt you with the following questions:

### Cleanup Icebreaker repository

```sh
Downloading (100%)Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? 
```

This removes the icebreaker git repository from the project. Press enter key to accept the default, 'Y'.

### Project Type

Press enter key or 0 to accept the standard installation of contrib modules. 

```sh
Project Type [default]:
  [0] default
  [1] commerce
```
The composer.json for each project type can be found in `scripts/composer/templates/`.

### Install contrib modules

Next, change the working directory and execute the `composer-update` command.

```sh
cd MY_PROJECT
composer update 
```

