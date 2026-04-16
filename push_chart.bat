@echo off
cd /d c:\xampp4\htdocs\dashboard\Revival_Technologies
git add resources/views/reports/builder.blade.php
git commit -m "feat: add live Chart.js visualisation panel to Report Builder"
git push origin main
echo PUSH_DONE
