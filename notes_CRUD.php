Method URI Purpose
index /patients Show all patients
create /patients/create Show form to create a new patient
store /patients (POST) Save new patient
show /patients/{id} Show one patient
edit /patients/{id}/edit Show form to edit patient
update /patients/{id} (PUT/PATCH) Update patient
destroy /patients/{id} (DELETE) Delete patient



npm install sweetalert2
npm install bootstrap@5.3


php artisan make:controller AdminController --resource

php artisan make:migration add_prefix_to_admin_table --table=admin

php artisan make:migration create_patient_uploads_table
php artisan make:model PatientUpload


php artisan make:model Admin -mcr
model / controller / CRUD