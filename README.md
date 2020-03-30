.env:\
MANAGER_EMAIL\
MANAGER_PASSWORD

commands:\
php artisan migrate:fresh --seed\
php artisan storage:link
