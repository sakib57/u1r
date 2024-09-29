Installation Instructions
=============================
copy .env.example to .env

Commands
=============================
```
composer i
php artisan migrate:fresh
php artisan db:seed
php artisan jwt:secret
php artisan serve
```

Documentation
=============================
After add or change a swagger documentation run below command to update doc json
```
php artisan l5-swagger:generate
```

Useful docs
========================
```
https://github.com/webdevmatics/laravel8api
```
