<?xml version='1.0' encoding='UTF-8'?><topic xsi:noNamespaceSchemaLocation="https://resources.jetbrains.com/writerside/1.0/topic.v2.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" id="Installation" title="Installation"> <title id="ejabe2_2">
Installation
</title>
<chapter id="installing-a-laravel-application-on-ubuntu-with-apache-nginx" title="Installing a Laravel Application on Ubuntu with Apache/Nginx">
<p id="ejabe2_10">Below is an updated step-by-step guide to installing a Laravel application on an Ubuntu system that includes both Apache and Nginx setup options. This guide assumes PHP and MySQL are already installed.</p>
<chapter id="step-1-update-system-packages" title="Step 1: Update System Packages">
<p id="ejabe2_19">First, ensure your system packages are up-to-date.</p> <code-block id="ejabe2_20">
bash
sudo apt update
sudo apt upgrade -y
</code-block>
</chapter>
<chapter id="step-2-install-composer" title="Step 2: Install Composer">
<p id="ejabe2_21">Composer is a dependency manager for PHP, necessary for installing Laravel.</p> <code-block id="ejabe2_22">
bash
sudo apt install curl -y
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
</code-block>
<p id="ejabe2_23">Verify the installation:</p> <code-block id="ejabe2_24" lang="bash">
composer --version
</code-block>
</chapter>
<chapter id="step-3-install-laravel" title="Step 3: Install Laravel">
<p id="ejabe2_25">Navigate to the directory where you want to install your Laravel application and create a new Laravel project.</p> <code-block id="ejabe2_26" lang="bash">
cd /var/www/html
composer create-project --prefer-dist laravel/laravel xeroInput
</code-block>
</chapter>
<chapter id="step-4-set-directory-permissions" title="Step 4: Set Directory Permissions">
<p id="ejabe2_27">Set the appropriate permissions for the Laravel project.</p> <code-block id="ejabe2_28" lang="bash">
sudo chown -R www-data:www-data /var/www/html/xeroInput
sudo chmod -R 755 /var/www/html/xeroInput/storage
sudo chmod -R 755 /var/www/html/xeroInput/bootstrap/cache
</code-block>
</chapter>
<chapter id="step-5-configure-the-environment" title="Step 5: Configure the Environment">
<p id="ejabe2_29">Navigate to your Laravel project directory and configure the <include from="Installation_auto-include.topic" element-id="ejabe2_35-snippet"/> file.</p> <code-block id="ejabe2_30" lang="bash">
cd /var/www/html/xeroInput
cp .env.example .env
php artisan key:generate
</code-block>
<p id="ejabe2_31">Edit the <include from="Installation_auto-include.topic" element-id="ejabe2_36-snippet"/> file to set your database credentials.</p> <code-block id="ejabe2_32">
bash
nano .env

</code-block>
<p id="ejabe2_33">Modify the following lines with your database configuration:</p> <code-block id="ejabe2_34">
env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xero_input
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
</code-block>
</chapter>
<chapter id="step-6-create-a-database" title="Step 6: Create a Database">
<p id="ejabe2_37">Log in to MySQL and create a database for your Laravel application.</p> <code-block id="ejabe2_38" lang="bash">
sudo mysql -u root -p
</code-block>
<p id="ejabe2_39">Inside the MySQL shell, run:</p> <code-block id="ejabe2_40" lang="sql">
CREATE DATABASE xero_input;
GRANT ALL PRIVILEGES ON xero_input.* TO 'your_database_user'@'localhost' IDENTIFIED BY 'your_database_password';
FLUSH PRIVILEGES;
EXIT;
</code-block>
</chapter>
<chapter id="step-7-set-up-the-web-server" title="Step 7: Set Up the Web Server">
<p id="ejabe2_41">You can choose to set up either Apache or Nginx to serve your Laravel application.</p>
<chapter id="option-1-set-up-apache">
<title id="ejabe2_44">
<include from="Installation_auto-include.topic" element-id="ejabe2_55-snippet"/>
</title>
<p id="ejabe2_45">Install Apache if not already installed.</p> <code-block id="ejabe2_46" lang="bash">
sudo apt install apache2 -y
</code-block>
<p id="ejabe2_47">Enable the necessary Apache modules:</p> <code-block id="ejabe2_48" lang="bash">
sudo a2enmod rewrite
</code-block>
<p id="ejabe2_49">Create a new Apache configuration file for your Laravel project:</p> <code-block id="ejabe2_50" lang="bash">
sudo nano /etc/apache2/sites-available/xeroInput.conf
</code-block>
<p id="ejabe2_51">Add the following configuration:</p> <code-block id="ejabe2_52" lang="apache">
&amp;lt;VirtualHost *:80&amp;gt;
    ServerAdmin admin@example.com
    DocumentRoot /var/www/html/xeroInput/public
    ServerName example.com
    ServerAlias www.example.com

    &amp;lt;Directory /var/www/html/xeroInput&amp;gt;
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    &amp;lt;/Directory&amp;gt;

    &amp;lt;Directory /var/www/html/xeroInput/public&amp;gt;
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    &amp;lt;/Directory&amp;gt;

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
&amp;lt;/VirtualHost&amp;gt;
</code-block>
<p id="ejabe2_53">Enable the new site and reload Apache:</p> <code-block id="ejabe2_54" lang="bash">
sudo a2ensite xeroInput.conf
sudo systemctl reload apache2
</code-block>
</chapter>
<chapter id="option-2-set-up-nginx">
<title id="ejabe2_56">
<include from="Installation_auto-include.topic" element-id="ejabe2_65-snippet"/>
</title>
<p id="ejabe2_57">Install Nginx if not already installed.</p> <code-block id="ejabe2_58" lang="bash">
sudo apt install nginx -y
</code-block>
<p id="ejabe2_59">Create a new Nginx configuration file for your Laravel project:</p> <code-block id="ejabe2_60" lang="bash">
sudo nano /etc/nginx/sites-available/xeroInput
</code-block>
<p id="ejabe2_61">Add the following configuration:</p> <code-block id="ejabe2_62" lang="nginx">
server {
    listen 80;
    server_name example.com www.example.com;
    root /var/www/html/xeroInput/public;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    error_log /var/log/nginx/xeroInput_error.log;
    access_log /var/log/nginx/xeroInput_access.log;
}
</code-block>
<p id="ejabe2_63">Enable the new site and reload Nginx:</p> <code-block id="ejabe2_64" lang="bash">
sudo ln -s /etc/nginx/sites-available/xeroInput /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
</code-block>
</chapter>
</chapter>
<chapter id="step-8-access-your-application" title="Step 8: Access Your Application">
<p id="ejabe2_66">Open your web browser and navigate to <include from="Installation_auto-include.topic" element-id="ejabe2_68-snippet"/> (replace <include from="Installation_auto-include.topic" element-id="ejabe2_69-snippet"/> with your server's IP address or domain name). You should see the Laravel welcome page.</p>
<p id="ejabe2_67">Congratulations! You have successfully installed a Laravel application on Ubuntu with either Apache or Nginx.</p>
</chapter>
</chapter>
<chapter id="installing-a-laravel-application-on-cpanel" title="Installing a Laravel Application on cPanel">
<p id="ejabe2_70">Below is a step-by-step guide to installing a Laravel application on a cPanel hosting environment.</p>
<chapter id="step-1-log-in-to-cpanel" title="Step 1: Log in to cPanel">
<p id="ejabe2_80">Access your cPanel account through your hosting provider. Usually, this can be done by navigating to <include from="Installation_auto-include.topic" element-id="ejabe2_81-snippet"/> and logging in with your cPanel credentials.</p>
</chapter>
<chapter id="step-2-set-up-the-environment" title="Step 2: Set Up the Environment">
<chapter id="create-a-subdomain-or-addon-domain" title="Create a Subdomain or Addon Domain">
<list id="ejabe2_84" type="decimal">
<li id="ejabe2_85">
<p id="ejabe2_87"><include from="Installation_auto-include.topic" element-id="ejabe2_89-snippet"/></p>
<list id="ejabe2_88">
<li id="ejabe2_90">Go to the <include from="Installation_auto-include.topic" element-id="ejabe2_92-snippet"/> section in cPanel.</li>
<li id="ejabe2_91">Create a subdomain (e.g., <include from="Installation_auto-include.topic" element-id="ejabe2_93-snippet"/>).</li>
</list>
</li>
<li id="ejabe2_86">
<p id="ejabe2_94"><include from="Installation_auto-include.topic" element-id="ejabe2_96-snippet"/></p>
<list id="ejabe2_95">
<li id="ejabe2_97">Go to the <include from="Installation_auto-include.topic" element-id="ejabe2_99-snippet"/> section.</li>
<li id="ejabe2_98">Create an addon domain and set the document root.</li>
</list>
</li>
</list>
</chapter>
<chapter id="create-a-database" title="Create a Database">
<list id="ejabe2_100" type="decimal">
<li id="ejabe2_101">Navigate to <include from="Installation_auto-include.topic" element-id="ejabe2_104-snippet"/>.</li>
<li id="ejabe2_102">Create a new database.</li>
<li id="ejabe2_103">Create a new database user and assign it to the database with <include from="Installation_auto-include.topic" element-id="ejabe2_105-snippet"/>.</li>
</list>
</chapter>
</chapter>
<chapter id="step-3-install-composer" title="Step 3: Install Composer">
<p id="ejabe2_106">cPanel often includes Composer by default. If not, you can install it manually.</p>
<list id="ejabe2_107" type="decimal">
<li id="ejabe2_110">
<p id="ejabe2_112">Go to the <include from="Installation_auto-include.topic" element-id="ejabe2_113-snippet"/> section in cPanel.</p>
</li>
<li id="ejabe2_111">
<p id="ejabe2_114">Run the following command to ensure Composer is installed:</p> <code-block id="ejabe2_115" lang="bash">
composer --version
</code-block>
</li>
</list>
<p id="ejabe2_108">If Composer is not installed, you can install it by running:</p> <code-block id="ejabe2_109" lang="bash">
cd ~
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
</code-block>
</chapter>
<chapter id="step-4-upload-laravel-files" title="Step 4: Upload Laravel Files">
<list id="ejabe2_116" type="decimal">
<li id="ejabe2_117">
<p id="ejabe2_119"><include from="Installation_auto-include.topic" element-id="ejabe2_121-snippet"/></p>
<list id="ejabe2_120">
<li id="ejabe2_122">Navigate to <include from="Installation_auto-include.topic" element-id="ejabe2_126-snippet"/> in cPanel.</li>
<li id="ejabe2_123">Go to the root directory of your subdomain or addon domain.</li>
<li id="ejabe2_124">Upload the Laravel project ZIP file.</li>
<li id="ejabe2_125">Extract the ZIP file.</li>
</list>
</li>
<li id="ejabe2_118">
<p id="ejabe2_127"><include from="Installation_auto-include.topic" element-id="ejabe2_129-snippet"/></p>
<list id="ejabe2_128">
<li id="ejabe2_130">Use an FTP client like FileZilla.</li>
<li id="ejabe2_131">Connect to your server using your FTP credentials.</li>
<li id="ejabe2_132">Upload your Laravel project files to the appropriate directory.</li>
</list>
</li>
</list>
</chapter>
<chapter id="step-5-configure-environment-variables" title="Step 5: Configure Environment Variables">
<list id="ejabe2_133" type="decimal">
<li id="ejabe2_134">
<p id="ejabe2_137">Rename <include from="Installation_auto-include.topic" element-id="ejabe2_138-snippet"/> to <include from="Installation_auto-include.topic" element-id="ejabe2_139-snippet"/>.</p>
</li>
<li id="ejabe2_135">
<p id="ejabe2_140">Edit the <include from="Installation_auto-include.topic" element-id="ejabe2_142-snippet"/> file with your database credentials:</p> <code-block id="ejabe2_141" lang="bash">
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xero_input
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
</code-block>
</li>
<li id="ejabe2_136">
<p id="ejabe2_143">Generate an application key:</p>
<list id="ejabe2_144">
<li id="ejabe2_145">
<p id="ejabe2_148">Go to <include from="Installation_auto-include.topic" element-id="ejabe2_149-snippet"/> in cPanel.</p>
</li>
<li id="ejabe2_146">
<p id="ejabe2_150">Navigate to the Laravel project directory.</p>
</li>
<li id="ejabe2_147">
<p id="ejabe2_151">Run:</p> <code-block id="ejabe2_152" lang="bash">
php artisan key:generate
</code-block>
</li>
</list>
</li>
</list>
</chapter>
<chapter id="step-6-set-directory-permissions" title="Step 6: Set Directory Permissions">
<p id="ejabe2_153">Set the necessary permissions for the <include from="Installation_auto-include.topic" element-id="ejabe2_155-snippet"/> and <include from="Installation_auto-include.topic" element-id="ejabe2_156-snippet"/> directories:</p>
<list id="ejabe2_154" type="decimal">
<li id="ejabe2_157">Go to <include from="Installation_auto-include.topic" element-id="ejabe2_160-snippet"/> in cPanel.</li>
<li id="ejabe2_158">Navigate to the Laravel project directory.</li>
<li id="ejabe2_159">Set permissions to <include from="Installation_auto-include.topic" element-id="ejabe2_161-snippet"/> for the <include from="Installation_auto-include.topic" element-id="ejabe2_162-snippet"/> and <include from="Installation_auto-include.topic" element-id="ejabe2_163-snippet"/> directories.</li>
</list>
</chapter>
<chapter id="step-7-configure-apache" title="Step 7: Configure Apache">
<p id="ejabe2_164">If your cPanel uses Apache, you need to update the <include from="Installation_auto-include.topic" element-id="ejabe2_166-snippet"/> file in your Laravel project’s root directory to ensure the correct rewriting of URLs.</p>
<list id="ejabe2_165" type="decimal">
<li id="ejabe2_167">
<p id="ejabe2_170">Open the <include from="Installation_auto-include.topic" element-id="ejabe2_171-snippet"/> in cPanel.</p>
</li>
<li id="ejabe2_168">
<p id="ejabe2_172">Navigate to the Laravel project directory and open the <include from="Installation_auto-include.topic" element-id="ejabe2_173-snippet"/> file.</p>
</li>
<li id="ejabe2_169">
<p id="ejabe2_174">Ensure it contains the following:</p> <code-block id="ejabe2_175" lang="apache">
&amp;lt;IfModule mod_rewrite.c&amp;gt;
    &amp;lt;IfModule mod_negotiation.c&amp;gt;
        Options -MultiViews -Indexes
    &amp;lt;/IfModule&amp;gt;

    RewriteEngine On

    # Redirect Trailing Slashes...
    RewriteRule ^(.*)/$ /$1 [L,R=301]

    # Handle Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
&amp;lt;/IfModule&amp;gt;
</code-block>
</li>
</list>
</chapter>
<chapter id="step-8-set-up-cron-jobs" title="Step 8: Set Up Cron Jobs">
<p id="ejabe2_176">To ensure that Laravel's scheduled tasks run correctly, set up a cron job in cPanel:</p>
<list id="ejabe2_177" type="decimal">
<li id="ejabe2_179">
<p id="ejabe2_181">Go to the <include from="Installation_auto-include.topic" element-id="ejabe2_182-snippet"/> section in cPanel.</p>
</li>
<li id="ejabe2_180">
<p id="ejabe2_183">Add a new cron job with the following command, set to run every minute:</p> <code-block id="ejabe2_184" lang="bash">
* * * * * php /home/username/path_to_your_laravel_project/artisan schedule:run &amp;gt;&amp;gt; /dev/null 2&amp;gt;&amp;amp;1
</code-block>
</li>
</list>
<p id="ejabe2_178">Replace <include from="Installation_auto-include.topic" element-id="ejabe2_185-snippet"/> with the actual path to your Laravel project.</p>
</chapter>
<chapter id="step-9-access-your-application" title="Step 9: Access Your Application">
<p id="ejabe2_186">Open your web browser and navigate to the subdomain or addon domain you set up earlier. You should see the Laravel welcome page.</p>
<p id="ejabe2_187">Congratulations! You have successfully installed a Laravel application on cPanel.</p>
<p id="ejabe2_188">Congratulations! You have successfully installed a Laravel application on Ubuntu.</p>
</chapter>
</chapter>
<chapter id="final-step-database-configure" title="Final Step: Database Configure">
<p id="ejabe2_189">After setting up your Laravel application and web server, you can now input your database SQL or run database migrations and seeders.</p>
<chapter id="option-1-import-xeroinput-sql">
<title id="ejabe2_193">
Option 1: Import <include from="Installation_auto-include.topic" element-id="ejabe2_195-snippet"/>
</title>
<list id="ejabe2_194" type="decimal">
<li id="ejabe2_196">
<p id="ejabe2_198"><include from="Installation_auto-include.topic" element-id="ejabe2_200-snippet"/></p>
<list id="ejabe2_199">
<li id="ejabe2_202">Transfer the <include from="Installation_auto-include.topic" element-id="ejabe2_203-snippet"/> file to your server using SCP, FTP, or any other preferred method.</li>
</list>
</li>
<li id="ejabe2_197">
<p id="ejabe2_204"><include from="Installation_auto-include.topic" element-id="ejabe2_207-snippet"/></p>
<list id="ejabe2_205">
<li id="ejabe2_208">
<p id="ejabe2_211">Log in to MySQL:</p> <code-block id="ejabe2_212" lang="bash">
sudo mysql -u root -p
</code-block>
</li>
<li id="ejabe2_209">
<p id="ejabe2_213">Switch to your Laravel database:</p> <code-block id="ejabe2_214" lang="sql">
USE xero_input;
</code-block>
</li>
<li id="ejabe2_210">
<p id="ejabe2_215">Import the SQL file:</p> <code-block id="ejabe2_216" lang="sql">
SOURCE /path/to/xeroInput.sql;
</code-block>
</li>
</list>
<p id="ejabe2_206">Replace <include from="Installation_auto-include.topic" element-id="ejabe2_217-snippet"/> with the actual path to your SQL file.</p>
</li>
</list>
</chapter>
<chapter id="option-2-run-laravel-migrations-and-seeders" title="Option 2: Run Laravel Migrations and Seeders">
<p id="ejabe2_218">If you prefer to set up your database using Laravel's migration and seeding system, follow these steps:</p>
<list id="ejabe2_219" type="decimal">
<li id="ejabe2_222">
<p id="ejabe2_224"><include from="Installation_auto-include.topic" element-id="ejabe2_227-snippet"/></p>
<list id="ejabe2_225">
<li id="ejabe2_229">Make sure the database credentials in your <include from="Installation_auto-include.topic" element-id="ejabe2_230-snippet"/> file are set correctly.</li>
</list> <code-block id="ejabe2_226">
env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xero_input
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
</code-block>
</li>
<li id="ejabe2_223">
<p id="ejabe2_231"><include from="Installation_auto-include.topic" element-id="ejabe2_233-snippet"/></p>
<list id="ejabe2_232">
<li id="ejabe2_234">
<p id="ejabe2_236">Open a terminal and navigate to your Laravel project directory.</p>
</li>
<li id="ejabe2_235">
<p id="ejabe2_237">Run the following command to refresh the database and seed it with initial data:</p> <code-block id="ejabe2_238" lang="bash">
php artisan migrate:fresh --seed
</code-block>
</li>
</list>
</li>
</list>
<p id="ejabe2_220">This command will:</p>
<list id="ejabe2_221">
<li id="ejabe2_239">Drop all existing tables.</li>
<li id="ejabe2_240">Recreate the tables based on your migrations.</li>
<li id="ejabe2_241">Seed the database with the data defined in your seeders.</li>
</list>
</chapter>
<chapter id="final-steps" title="Final Steps">
<p id="ejabe2_242">After importing the SQL file or running the migrations and seeders, you should be able to access your Laravel application with a fully set up database.</p>
</chapter>
</chapter>
<chapter id="access-your-application" title="Access Your Application">
<p id="ejabe2_243">Open your web browser and navigate to <include from="Installation_auto-include.topic" element-id="ejabe2_244-snippet"/> (replace <include from="Installation_auto-include.topic" element-id="ejabe2_245-snippet"/> with your server's IP address or domain name). You should see the Laravel welcome page or your application’s homepage if the setup includes routes and views.</p>
</chapter>
<chapter id="troubleshooting" title="Troubleshooting">
<list id="ejabe2_246">
<li id="ejabe2_248"><include from="Installation_auto-include.topic" element-id="ejabe2_250-snippet"/> Ensure the database credentials in your <include from="Installation_auto-include.topic" element-id="ejabe2_251-snippet"/> file are correct.</li>
<li id="ejabe2_249"><include from="Installation_auto-include.topic" element-id="ejabe2_252-snippet"/> Ensure the correct permissions are set on your Laravel directories (<include from="Installation_auto-include.topic" element-id="ejabe2_253-snippet"/> and <include from="Installation_auto-include.topic" element-id="ejabe2_254-snippet"/>).</li>
</list>
<p id="ejabe2_247">Congratulations! You have successfully set up your Laravel application database either by importing <include from="Installation_auto-include.topic" element-id="ejabe2_255-snippet"/> or running <include from="Installation_auto-include.topic" element-id="ejabe2_256-snippet"/>.</p>
</chapter>
<chapter id="for-the-installation-service" title="For the installation service">
<p id="ejabe2_257">we offer assistance at a fee of $20. To initiate the installation process, please follow these steps:</p>
<list id="ejabe2_258" type="decimal">
<li id="ejabe2_260">Create an Account: Visit our support portal at https://www.microdreamit.com/my-account.</li>
<li id="ejabe2_261">Top Up $20.</li>
<li id="ejabe2_262">Open Installation Ticket: Once your account is funded, you can open an installation ticket from your user menu.</li>
</list>
<p id="ejabe2_259">Thank you for choosing our services. We look forward to assisting you with the installation process.</p>
</chapter>
<seealso id="ejabe2_9"> <category ref="external" id="ejabe2_263"> <a href="https://laravel.com/docs/11.x/installation" id="ejabe2_264">Laravel Installation</a> <a href="https://laravel.com/docs/11.x/configuration" id="ejabe2_265">Configuration</a> <a href="https://laravel.com/docs/11.x/structure" id="ejabe2_266">Directory Structure</a> <a href="https://laravel.com/docs/11.x/deployment" id="ejabe2_267">Deployment</a> <a href="https://www.microdreamit.com/contact-us" id="ejabe2_268">Installation Support</a> </category>
</seealso> </topic>