turn on XAMPP
cd vue-project
npm install vite --save-dev
npm run dev

cd API-main
php artisan serve
php artisan migrate
php artisan migrate:rollback
php artisan migrate:status

---show img---
Remove-Item -Recurse -Force public\storage
php artisan storage:link
