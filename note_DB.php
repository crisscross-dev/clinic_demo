add column to table

php artisan make:migration add_signature_to_patient_infos_table --table=patient_infos

php artisan make:migration create_patient_uploads_table

check the tables pending /ran
php artisan migrate:status

// php artisan migrate
Only runs the migrations that haven’t been run yet.
It won’t touch existing tables that were already migrated.
Safe if your tables already exist and you just want to add new ones.

// php artisan migrate:refresh
Rolls back all migrations and then runs them again from scratch.
Deletes all data in your tables!
Useful if you want a clean slate for development, but dangerous in production.

// php artisan migrate:rollback
Rolls back the last batch of migrations only.
Doesn’t touch older migrations that ran in previous batches.


safe rollback // php artisan migrate:rollback --path=/database/migrations/2025_08_16_123457_create_inventory_table.php