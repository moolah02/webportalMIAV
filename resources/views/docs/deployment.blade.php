@extends('docs.layout')
@section('content')

<div class="breadcrumb">
    <a href="{{ url('/docs') }}">Docs Hub</a>
    <span>›</span> Deployment Guide
</div>

<h1>Deployment Guide</h1>
<p class="subtitle">
    Dev &amp; production server setup, SSH access, git workflow, artisan commands, and branch strategy.
    <span style="float:right;" class="badge badge-red">DevOps / Admin</span>
</p>

<div class="callout danger">
    <strong>Production Warning:</strong> Always test changes on Development first.
    Never push directly to <code>main</code> without peer review.
</div>

<h2>Server Overview</h2>

<table>
    <thead>
        <tr><th>Environment</th><th>URL</th><th>Server Path</th><th>Database</th><th>Branch</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="badge badge-red">Production</span></td>
            <td><code>http://51.21.252.67</code></td>
            <td><code>/var/www/html/revival_production</code></td>
            <td><code>miav_system</code></td>
            <td><code>main</code></td>
        </tr>
        <tr>
            <td><span class="badge badge-blue">Development</span></td>
            <td><code>http://51.21.252.67:8080</code></td>
            <td><code>/var/www/html/revival_dev</code></td>
            <td><code>miav_system_dev</code></td>
            <td><code>fix/*</code> / feature branches</td>
        </tr>
    </tbody>
</table>

<h2>SSH Access</h2>

<h3>Connect to the Server</h3>
<pre><code>ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67</code></pre>

<div class="callout">
    <strong>Key File:</strong> Keep <code>RTLDEV (1).pem</code> secure. Never commit it to git. It grants full server access.
</div>

<h2>Updating Development</h2>

<pre><code># 1. Connect via SSH
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67

# 2. Navigate to dev directory
cd /var/www/html/revival_dev

# 3. Pull latest changes from your feature branch
sudo -u www-data git pull origin fix/sessions-fallback

# 4. Run database migrations
sudo -u www-data php artisan migrate

# 5. Clear application cache
sudo -u www-data php artisan cache:clear

# 6. (Optional) Clear route & view cache
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear</code></pre>

<h2>Deploying to Production</h2>

<div class="callout warning">
    <strong>Before deploying:</strong> Merge your feature/fix branch into <code>main</code> on GitHub
    and have it reviewed. Do not deploy untested code.
</div>

<pre><code># 1. Merge branch to main on GitHub (via Pull Request)

# 2. Connect to server via SSH
ssh -i "C:\Users\hp\Downloads\RTLDEV (1).pem" ubuntu@51.21.252.67

# 3. Navigate to production directory
cd /var/www/html/revival_production

# 4. Pull latest from main
sudo -u www-data git pull origin main

# 5. Run database migrations
sudo -u www-data php artisan migrate

# 6. Clear all caches
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan route:clear
sudo -u www-data php artisan view:clear

# 7. (Optional) Re-cache for performance boost
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache</code></pre>

<h2>Git Branch Strategy</h2>

<table>
    <thead>
        <tr><th>Branch Pattern</th><th>Purpose</th><th>Deploys To</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><code>main</code></td>
            <td>Stable, production-ready code</td>
            <td>Production</td>
        </tr>
        <tr>
            <td><code>fix/issue-name</code></td>
            <td>Bug fixes</td>
            <td>Development → PR to main</td>
        </tr>
        <tr>
            <td><code>feature/feature-name</code></td>
            <td>New features</td>
            <td>Development → PR to main</td>
        </tr>
        <tr>
            <td><code>hotfix/issue-name</code></td>
            <td>Urgent production fixes</td>
            <td>Direct to main after review</td>
        </tr>
    </tbody>
</table>

<h2>Common Artisan Commands</h2>

<table>
    <thead>
        <tr><th>Command</th><th>What it Does</th><th>When to Run</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><code>php artisan migrate</code></td>
            <td>Run pending migrations</td>
            <td>After every deploy with DB changes</td>
        </tr>
        <tr>
            <td><code>php artisan migrate:rollback</code></td>
            <td>Reverse last migration</td>
            <td>Emergency rollback only</td>
        </tr>
        <tr>
            <td><code>php artisan cache:clear</code></td>
            <td>Clear application cache</td>
            <td>After every deploy</td>
        </tr>
        <tr>
            <td><code>php artisan config:clear</code></td>
            <td>Clear config cache</td>
            <td>After .env changes</td>
        </tr>
        <tr>
            <td><code>php artisan route:clear</code></td>
            <td>Clear route cache</td>
            <td>After adding new routes</td>
        </tr>
        <tr>
            <td><code>php artisan view:clear</code></td>
            <td>Clear Blade view cache</td>
            <td>After view changes</td>
        </tr>
        <tr>
            <td><code>php artisan db:seed</code></td>
            <td>Run database seeders</td>
            <td>Initial setup or fresh install</td>
        </tr>
        <tr>
            <td><code>php artisan storage:link</code></td>
            <td>Create storage symlink</td>
            <td>First-time setup</td>
        </tr>
        <tr>
            <td><code>php artisan tinker</code></td>
            <td>Interactive PHP REPL</td>
            <td>Debugging / quick queries</td>
        </tr>
        <tr>
            <td><code>php artisan queue:work</code></td>
            <td>Process queued jobs</td>
            <td>When using job queues</td>
        </tr>
    </tbody>
</table>

<h2>Environment Variables (.env)</h2>

<p>Key <code>.env</code> values to verify on each server:</p>
<pre><code>APP_ENV=production          # or local for dev
APP_DEBUG=false             # MUST be false in production
APP_URL=http://51.21.252.67

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=miav_system     # or miav_system_dev for dev
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_pass

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync</code></pre>

<div class="callout danger">
    <strong>Security:</strong> Never commit your <code>.env</code> file to git.
    It is listed in <code>.gitignore</code> — keep it that way.
</div>

<h2>Troubleshooting</h2>

<h3>500 Server Error After Deploy</h3>
<ol>
    <li>Check storage permissions: <code>sudo chmod -R 775 storage bootstrap/cache</code></li>
    <li>Run: <code>php artisan config:clear &amp;&amp; php artisan cache:clear</code></li>
    <li>Check logs: <code>tail -f storage/logs/laravel.log</code></li>
</ol>

<h3>Migrations Failing</h3>
<ol>
    <li>Check migration status: <code>php artisan migrate:status</code></li>
    <li>Check DB credentials in <code>.env</code></li>
    <li>Run with verbose output: <code>php artisan migrate --verbose</code></li>
</ol>

<h3>Assets Not Loading (CSS/JS)</h3>
<ol>
    <li>Ensure <code>public/build</code> directory is present and committed.</li>
    <li>Re-run Vite build locally and push: <code>npm run build</code></li>
    <li>Clear view cache: <code>php artisan view:clear</code></li>
</ol>

@endsection
