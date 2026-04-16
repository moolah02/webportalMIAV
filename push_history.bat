@echo off
cd /d c:\xampp4\htdocs\dashboard\Revival_Technologies
git add resources/views/reports/history.blade.php
git commit -m "redesign-history-ui"
git push origin main
echo DONE
pause
