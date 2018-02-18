1. Create repository on github.

2. Clone it to project folder.

3. Create laravel project according laravel site:

composer create-project --prefer-dist laravel/laravel blog

4. Create database.

5. Edit .env file. Replace database settings with:

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

Setup log:

LOG_CHANNEL=daily

Use redis:

CACHE_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

Setup smtp:

MAIL_DRIVER=smtp
MAIL_HOST=
MAIL_PORT=25
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

6. Replace .gitignore content with:

/vendor
composer.lock
.env

