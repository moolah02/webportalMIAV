@echo off
cd c:\xampp4\htdocs\dashboard\Revival_Technologies
git add database/migrations/2026_04_14_000001_enhance_report_runs_table.php app/Models/ReportRun.php app/Http/Controllers/ReportController.php routes/web.php resources/views/reports/history.blade.php resources/views/reports/builder.blade.php public/build
git commit -m "feat: report audit trail - history page, IP/UA logging, action+format tracking"
git push origin main
