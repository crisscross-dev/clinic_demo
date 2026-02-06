Subclinic_password123
php artisan make:command DeleteUnapprovedStudents

http://192.168.254.102/samuel_clinic_vite/public
APP_URL=http://localhost/samuel_clinic_vite/public

php artisan serve --host=0.0.0.0 --port=8000




php artisan serve
npm run dev - live server
npm run build - refresh css/js

clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

check the migration status
php artisan migrate:status


public testing
🔹 Step 1: Run your Laravel app
php artisan serve --host=127.0.0.1 --port=8000


🔹 Step 2: Start Ngrok tunnel to cmd
ngrok http 8000

Ngrok will display something like:
Forwarding https://random-name.ngrok-free.app -> http://127.0.0.1:8000


change .env
http://localhost/samuel_clinic_vite/public
APP_URL=http://localhost:8000
APP_URL=https://2923b98e0534.ngrok-free.app


ip address
192.168.1.7

php artisan serve --host=0.0.0.0 --port=8000