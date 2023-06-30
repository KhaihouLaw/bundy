## About LVCC Bundy

This is a Timesheet and Leave Request Management System

## How to install

1. Clone this repository
2. Run `composer install`. composer should be installed in your machine
3. Run `npm install`. npm or node should be installed in your machine
4. Run `npm run dev` (in prod, run `npx mix`)
5. Run `php artisan key:generate`
6. Set the database configurations based on what's in your local machine (database credentials: username and password)
7. Set the Google API keys and secret keys. Get your keys from Google and set it in the configuration
8. Run `php artisan migrate:refresh`
9. Run `php artisan db:seed`
10. Run `php artisan serve`

Note: if you need to reset the database, just run items number 8 and 9
