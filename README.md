==========\
.env.example (rename to .env):\
==========\
https://mailtrap.io/\
MAIL_USERNAME\
MAIL_PASSWORD\
MAIL_FROM_ADDRESS\

MANAGER_EMAIL\
MANAGER_PASSWORD\
==========\
commands:\
==========\
php artisan migrate:fresh --seed\
php artisan storage:link\
php artisan queue:work
