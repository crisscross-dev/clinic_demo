ssh -p 65002 u699529491@145.79.25.23

ssh -p 65002 u321426185@145.79.28.1
Samuel_password123
ls
cd domain
ls
scc-clinic.site
ls
SAMUEL_CLINIC
php artisan


php artisan db:wipe -- Clear database
migrate




php artisan make:command ClearPrivateStorage
php artisan app:clear-private-storage

























# ============================================
# HOSTINGER CRON JOB SETUP
# ============================================
# Full Project Path:
# /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC

# PHP Path (verified):
# /usr/bin/php

# CRON JOB COMMAND (Copy to Hostinger hPanel → Cron Jobs):
# Frequency: * * * * * (Every Minute)
# Command:
cd /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC && /usr/bin/php artisan schedule:run >> /dev/null 2>&1

# WITH LOGGING (for debugging):
cd /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC && /usr/bin/php artisan schedule:run >> /home/u699529491/cron.log 2>&1

# ============================================
# DEBUGGING CONSENT SCHEDULE ISSUES
# ============================================

# QUICK DIAGNOSTIC (No file upload needed!)
# Run these commands directly via SSH:

ssh -p 65002 u699529491@145.79.25.23
cd /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC

# 1. Check current server time
date

# 2. Check if schedules exist
/usr/bin/php artisan tinker
echo "Server time: " . now()->format('Y-m-d H:i:s') . "\n";
$schedules = \App\Models\ConsentSchedule::where('is_active', true)->get();
echo "Active schedules: " . $schedules->count() . "\n";
foreach($schedules as $s) {
echo "ID: {$s->id}, Start: {$s->start_time}, End: {$s->end_time}, Dept: " . ($s->department ?? 'All') . "\n";
}
exit;

# 3. Check consent_form status
/usr/bin/php artisan tinker
$total = \App\Models\PatientInfo::count();
$unlocked = \App\Models\PatientInfo::where('consent_form', 0)->count();
$locked = \App\Models\PatientInfo::where('consent_form', 1)->count();
echo "Total: {$total}, Unlocked: {$unlocked}, Locked: {$locked}\n";
exit;

# 4. Test the command manually NOW
/usr/bin/php artisan consent:process-schedules

# 5. Check Laravel logs
tail -20 storage/logs/laravel.log

# ============================================
# OR: Upload and run debug script
# ============================================

# Step 1: Upload debug script to server
# Upload debug_consent_schedule.php to project root

# Step 2: Run debug script via SSH
ssh -p 65002 u699529491@145.79.25.23
cd /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC
/usr/bin/php debug_consent_schedule.php

# Step 3: Check if schedules exist
/usr/bin/php artisan tinker
\App\Models\ConsentSchedule::where('is_active', true)->get();
exit;

# Step 4: Check current consent_form values
/usr/bin/php artisan tinker
\App\Models\PatientInfo::select('department', 'consent_form')->take(10)->get();
exit;

# Step 5: Create a test schedule (2 minutes from now, ends in 5 minutes)
/usr/bin/php artisan tinker
\App\Models\ConsentSchedule::create([
'department' => null,
'start_time' => now()->addMinutes(2),
'end_time' => now()->addMinutes(7),
'is_active' => true,
'created_by' => 1
]);
echo "Schedule created! Start: " . now()->addMinutes(2)->format('Y-m-d H:i:s') . " End: " . now()->addMinutes(7)->format('Y-m-d H:i:s') . "\n";
exit;

# OR: Create a schedule that's active RIGHT NOW (for immediate testing)
/usr/bin/php artisan tinker
\App\Models\ConsentSchedule::create([
'department' => null,
'start_time' => now()->subMinute(),
'end_time' => now()->addMinutes(3),
'is_active' => true,
'created_by' => 1
]);
echo "Schedule created! Should unlock NOW when you run the command.\n";
exit;

# Step 6: Test the command manually
/usr/bin/php artisan consent:process-schedules

# Step 7: Monitor Laravel logs (real-time)
tail -f /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC/storage/logs/laravel.log

# Step 8: Verify cron is actually running
crontab -l

# Step 9: Check cron log (if you used logging version)
cat /home/u699529491/cron.log
tail -f /home/u699529491/cron.log

# Step 10: Force unlock all forms manually (if needed)
/usr/bin/php artisan tinker
\App\Models\PatientInfo::query()->update(['consent_form' => 0]);
exit;

# Step 11: Force lock all forms manually (if needed)
/usr/bin/php artisan tinker
\App\Models\PatientInfo::query()->update(['consent_form' => 1]);
exit;

# ============================================
# COMMON ISSUES & FIXES
# ============================================

# Issue: Cron not running
# Fix 1: Check cron job exists in hPanel
# Fix 2: Make sure path is correct
# Fix 3: Make sure PHP path is correct (/usr/bin/php)

# Issue: Schedule created but not executing
# Fix 1: Check schedule times (must be future times)
# Fix 2: Check is_active = 1 (true)
# Fix 3: Run manually: php artisan consent:process-schedules

# Issue: Forms not unlocking/locking
# Fix 1: Check consent_form column exists in patient_infos table
# Fix 2: Check department matching (null = all departments)
# Fix 3: Check times are in correct timezone

# Issue: "No active schedules found"
# Fix: Create schedule or set existing schedule is_active = 1

# ============================================
# TESTING COMMANDS
# ============================================
# Test scheduler manually:
/usr/bin/php artisan schedule:run

# Test consent schedule command:
/usr/bin/php artisan consent:process-schedules

# View cron log (if using logging version):
cat /home/u699529491/cron.log
tail -f /home/u699529491/cron.log

# View Laravel logs:
tail -f /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC/storage/logs/laravel.log

# Check active schedules:
/usr/bin/php artisan tinker
\App\Models\ConsentSchedule::where('is_active', true)->get();
exit;