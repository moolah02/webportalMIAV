@echo off
cd /d c:\xampp4\htdocs\dashboard\Revival_Technologies
npm run build
git add resources/views/reports/history.blade.php resources/views/reports/builder.blade.php
git commit -m "feat: history re-run, export, save-template actions"
git push origin main
echo DONE
