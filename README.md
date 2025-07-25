## Setup

To setup the project, copy the `.env.example` to `.env` and set up your local development environment.

Some configuration regarding the restaurant is available in `config/app.php` at the end of the file.

### Local development

`composer install`

`npm install`

`php artisan key:generate`

`php artisan migrate`

`composer run dev`

### Testing user
Seed the database for a testing user.

`php artisan db:seed`
