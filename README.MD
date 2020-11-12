# Configuration 
Application settings are in `app.local.ini` file that located in project root.
For the production change the settings in `app.local.ini` and rename it to `app.ini`.

# Development
For development copy the `app.local.ini` file on your project root and name it `app.dev.ini`

# Production
For production use `app.ini` for settings and remove `app.dev.ini` and `app.local.ini` if it exists.

# Installation
1- Clone repository

2- Follow Configuration and settings are depends the environment that is development or production

3- Run composer install
```bash
composer install
```

4- Run composer update
```bash
composer update
```

# Folders 
```
/uploads/[images, pdf] (its symbolic link on production) 
/logs
/tmp
```

# Uploads/[images, pdf] folder:
It's symbolic link in project folder that the real path synced between portal & admin servers via crontab

# Logs
The logs/*.log files should be removed periodically for size will be high.
The logger need to be removed from production code after the test process complete.

