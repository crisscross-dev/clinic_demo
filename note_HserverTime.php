nano run-schedule.sh

#!/bin/bash
cd /home/u699529491/domains/scc-clinic.site/SAMUEL_CLINIC
export TZ=Asia/Manila
/usr/bin/php artisan schedule:run >> /home/u699529491/cron.log 2>&1

ctr - O Save
ctr - X Exit

chmod +x run-schedule.sh