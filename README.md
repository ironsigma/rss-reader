# RSS Reader

Simple, Lightweight RSS Reader.

This is an attepmt to make an RSS reader without all the overhead and bloat of a full fledge framework.
Most of the components used have been chosen for their simplicity and other have been manually created.

## Working

- Feed parsing
- Feed auto update
- Feed display
- Folder structure
- Marking posts read, stared
- Auto remove old read posts
- Primitive feed stats

## To Do

- Feed management UI
- Multi-user support
- Change number of post viewed
- User account and settings

## Project Structure

### Root Folders

- *Config*      Runtime configuration files
- *lib*         Generalized and third-party libraries
- *model*       Database model classes, entities and DAOs
- *modules*     Application controllers
- *templates*   View files
- *test*        Test suite
- *web*         Pubilc files: images, css, js, etc...
- *index.php*   Bootstrap and Front controller

### Libraries
- *config*          Read/Manage global configuration
- *database*        Database management
- *elian*           ASCII to Elian class (obfuscation)
- *feed-parser*     Parse RSS/ATOM feeds
- *logger*          Logging
- *router*          Routing
- *session*         User session
- *spyc*            YAML parsing
- *template*        Template view management
- *AutoLoader.php*  Autoloading class
