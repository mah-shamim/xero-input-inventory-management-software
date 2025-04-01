# Installation

### Installing a Laravel Application on Ubuntu with Apache/Nginx

Below is an updated step-by-step guide to installing a Laravel application on an Ubuntu system that includes both Apache and Nginx setup options. This guide assumes PHP and MySQL are already installed.

#### Step 1: Update System Packages
First, ensure your system packages are up-to-date.
```
bash
sudo apt update
sudo apt upgrade -y
```

#### Step 2: Install Composer
Composer is a dependency manager for PHP, necessary for installing Laravel.
```
bash
sudo apt install curl -y
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verify the installation:
```bash
composer --version
```

#### Step 3: Install Laravel
Navigate to the directory where you want to install your Laravel application and create a new Laravel project.
```bash
cd /var/www/html
composer create-project --prefer-dist laravel/laravel xeroInput
```

#### Step 4: Set Directory Permissions
Set the appropriate permissions for the Laravel project.
```bash
sudo chown -R www-data:www-data /var/www/html/xeroInput
sudo chmod -R 755 /var/www/html/xeroInput/storage
sudo chmod -R 755 /var/www/html/xeroInput/bootstrap/cache
```

#### Step 5: Configure the Environment
Navigate to your Laravel project directory and configure the `.env` file.
```bash
cd /var/www/html/xeroInput
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file to set your database credentials.
```
bash
nano .env

```
Modify the following lines with your database configuration:
```
env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xero_input
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

#### Step 6: Create a Database
Log in to MySQL and create a database for your Laravel application.
```bash
sudo mysql -u root -p
```
Inside the MySQL shell, run:
```sql
CREATE DATABASE xero_input;
GRANT ALL PRIVILEGES ON xero_input.* TO 'your_database_user'@'localhost' IDENTIFIED BY 'your_database_password';
FLUSH PRIVILEGES;
EXIT;
```

#### Step 7: Set Up the Web Server
You can choose to set up either Apache or Nginx to serve your Laravel application.

##### **Option 1: Set Up Apache**

Install Apache if not already installed.
```bash
sudo apt install apache2 -y
```

Enable the necessary Apache modules:
```bash
sudo a2enmod rewrite
```

Create a new Apache configuration file for your Laravel project:
```bash
sudo nano /etc/apache2/sites-available/xeroInput.conf
```

Add the following configuration:
```apache
<VirtualHost *:80>
    ServerAdmin admin@example.com
    DocumentRoot /var/www/html/xeroInput/public
    ServerName example.com
    ServerAlias www.example.com

    <Directory /var/www/html/xeroInput>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/xeroInput/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Enable the new site and reload Apache:
```bash
sudo a2ensite xeroInput.conf
sudo systemctl reload apache2
```

##### **Option 2: Set Up Nginx**

Install Nginx if not already installed.
```bash
sudo apt install nginx -y
```

Create a new Nginx configuration file for your Laravel project:
```bash
sudo nano /etc/nginx/sites-available/xeroInput
```

Add the following configuration:
```nginx
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
```

Enable the new site and reload Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/xeroInput /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### Step 8: Access Your Application
Open your web browser and navigate to `http://example.com` (replace `example.com` with your server's IP address or domain name). You should see the Laravel welcome page.

Congratulations! You have successfully installed a Laravel application on Ubuntu with either Apache or Nginx.

### Installing a Laravel Application on cPanel

Below is a step-by-step guide to installing a Laravel application on a cPanel hosting environment.

#### Step 1: Log in to cPanel

Access your cPanel account through your hosting provider. Usually, this can be done by navigating to `http://yourdomain.com/cpanel` and logging in with your cPanel credentials.

#### Step 2: Set Up the Environment

##### Create a Subdomain or Addon Domain
1. **Subdomain:**
    - Go to the **Subdomains** section in cPanel.
    - Create a subdomain (e.g., `laravel.yourdomain.com`).

2. **Addon Domain:**
    - Go to the **Addon Domains** section.
    - Create an addon domain and set the document root.

##### Create a Database
1. Navigate to **MySQL® Databases**.
2. Create a new database.
3. Create a new database user and assign it to the database with **All Privileges**.

#### Step 3: Install Composer

cPanel often includes Composer by default. If not, you can install it manually.

1. Go to the **Terminal** section in cPanel.
2. Run the following command to ensure Composer is installed:
   ```bash
   composer --version
   ```

If Composer is not installed, you can install it by running:
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

#### Step 4: Upload Laravel Files

1. **Via File Manager:**
    - Navigate to **File Manager** in cPanel.
    - Go to the root directory of your subdomain or addon domain.
    - Upload the Laravel project ZIP file.
    - Extract the ZIP file.

2. **Via FTP:**
    - Use an FTP client like FileZilla.
    - Connect to your server using your FTP credentials.
    - Upload your Laravel project files to the appropriate directory.

#### Step 5: Configure Environment Variables

1. Rename `.env.example` to `.env`.
2. Edit the `.env` file with your database credentials:
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=xero_input
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```
3. Generate an application key:
    - Go to **Terminal** in cPanel.
    - Navigate to the Laravel project directory.
    - Run:
      ```bash
      php artisan key:generate
      ```

#### Step 6: Set Directory Permissions

Set the necessary permissions for the `storage` and `bootstrap/cache` directories:
1. Go to **File Manager** in cPanel.
2. Navigate to the Laravel project directory.
3. Set permissions to `755` for the `storage` and `bootstrap/cache` directories.

#### Step 7: Configure Apache

If your cPanel uses Apache, you need to update the `.htaccess` file in your Laravel project’s root directory to ensure the correct rewriting of URLs.

1. Open the **File Manager** in cPanel.
2. Navigate to the Laravel project directory and open the `.htaccess` file.
3. Ensure it contains the following:
   ```apache
   <IfModule mod_rewrite.c>
       <IfModule mod_negotiation.c>
           Options -MultiViews -Indexes
       </IfModule>

       RewriteEngine On

       # Redirect Trailing Slashes...
       RewriteRule ^(.*)/$ /$1 [L,R=301]

       # Handle Front Controller...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```

#### Step 8: Set Up Cron Jobs

To ensure that Laravel's scheduled tasks run correctly, set up a cron job in cPanel:

1. Go to the **Cron Jobs** section in cPanel.
2. Add a new cron job with the following command, set to run every minute:
   ```bash
   * * * * * php /home/username/path_to_your_laravel_project/artisan schedule:run >> /dev/null 2>&1
   ```

Replace `/home/username/path_to_your_laravel_project/` with the actual path to your Laravel project.

#### Step 9: Access Your Application

Open your web browser and navigate to the subdomain or addon domain you set up earlier. You should see the Laravel welcome page.

Congratulations! You have successfully installed a Laravel application on cPanel.

Congratulations! You have successfully installed a Laravel application on Ubuntu.

### Final Step: Database Configure

After setting up your Laravel application and web server, you can now input your database SQL or run database migrations and seeders.

#### Option 1: Import `xeroInput.sql`

1. **Upload `xeroInput.sql` File:**
   - Transfer the `xeroInput.sql` file to your server using SCP, FTP, or any other preferred method.

2. **Import the SQL File into MySQL:**
   - Log in to MySQL:
     ```bash
     sudo mysql -u root -p
     ```
   - Switch to your Laravel database:
     ```sql
     USE xero_input;
     ```
   - Import the SQL file:
     ```sql
     SOURCE /path/to/xeroInput.sql;
     ```
   Replace `/path/to/xeroInput.sql` with the actual path to your SQL file.

#### Option 2: Run Laravel Migrations and Seeders

If you prefer to set up your database using Laravel's migration and seeding system, follow these steps:

1. **Ensure Your `.env` File is Correctly Configured:**
   - Make sure the database credentials in your `.env` file are set correctly.
   ```
   env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=xero_input
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

2. **Run the Migrations and Seeders:**
   - Open a terminal and navigate to your Laravel project directory.
   - Run the following command to refresh the database and seed it with initial data:
     ```bash
     php artisan migrate:fresh --seed
     ```

This command will:
- Drop all existing tables.
- Recreate the tables based on your migrations.
- Seed the database with the data defined in your seeders.

#### Final Steps

After importing the SQL file or running the migrations and seeders, you should be able to access your Laravel application with a fully set up database.

### Access Your Application

Open your web browser and navigate to `http://example.com` (replace `example.com` with your server's IP address or domain name). You should see the Laravel welcome page or your application’s homepage if the setup includes routes and views.

### Troubleshooting

- **Database Connection Issues:** Ensure the database credentials in your `.env` file are correct.
- **Permission Issues:** Ensure the correct permissions are set on your Laravel directories (`storage` and `bootstrap/cache`).

Congratulations! You have successfully set up your Laravel application database either by importing `xero_input.sql` or running `php artisan migrate:fresh --seed`.


### For the installation service
we offer assistance at a fee of $20. To initiate the installation process, please follow these steps:
1. Create an Account: Visit our support portal at https://www.microdreamit.com/my-account.
2. Top Up $20.
3. Open Installation Ticket: Once your account is funded, you can open an installation ticket from your user menu.

Thank you for choosing our services. We look forward to assisting you with the installation process.

<seealso>
    <category ref="external">
        <a href="https://laravel.com/docs/11.x/installation">Laravel Installation</a>
        <a href="https://laravel.com/docs/11.x/configuration">Configuration</a>
        <a href="https://laravel.com/docs/11.x/structure">Directory Structure</a>
        <a href="https://laravel.com/docs/11.x/deployment">Deployment</a>
        <a href="https://www.microdreamit.com/contact-us">Installation Support</a>
    </category>
</seealso>

[//]: # (# About Creating Purchase)

[//]: # ()
[//]: # (<!--Writerside adds this topic when you create a new documentation project.)

[//]: # (You can use it as a sandbox to play with Writerside features, and remove it from the TOC when you don't need it anymore.-->)

[//]: # ()
[//]: # (## Add new topics)

[//]: # (You can create empty topics, or choose a template for different types of content that contains some boilerplate structure to help you get started:)

[//]: # ()
[//]: # (![Create new topic options]&#40;new_topic_options.png&#41;{ width=290 }{border-effect=line})

[//]: # ()
[//]: # (## Write content)

[//]: # (%product% supports two types of markup: Markdown and XML.)

[//]: # (When you create a new help article, you can choose between two topic types, but this doesn't mean you have to stick to a single format.)

[//]: # (You can author content in Markdown and extend it with semantic attributes or inject entire XML elements.)

[//]: # ()
[//]: # (## Inject XML)

[//]: # (For example, this is how you inject a procedure:)

[//]: # ()
[//]: # (<procedure title="Inject a procedure" id="inject-a-procedure">)

[//]: # (    <step>)

[//]: # (        <p>Start typing and select a procedure type from the completion suggestions:</p>)

[//]: # (        <img src="completion_procedure.png" alt="completion suggestions for procedure" border-effect="line"/>)

[//]: # (    </step>)

[//]: # (    <step>)

[//]: # (        <p>Press <shortcut>Tab</shortcut> or <shortcut>Enter</shortcut> to insert the markup.</p>)

[//]: # (    </step>)

[//]: # (</procedure>)

[//]: # ()
[//]: # (## Add interactive elements)

[//]: # ()
[//]: # (### Tabs)

[//]: # (To add switchable content, you can make use of tabs &#40;inject them by starting to type `tab` on a new line&#41;:)

[//]: # ()
[//]: # (<tabs>)

[//]: # (    <tab title="Markdown">)

[//]: # (        <code-block lang="plain text">![Alt Text]&#40;new_topic_options.png&#41;{ width=450 }</code-block>)

[//]: # (    </tab>)

[//]: # (    <tab title="Semantic markup">)

[//]: # (        <code-block lang="xml">)

[//]: # (            <![CDATA[<img src="new_topic_options.png" alt="Alt text" width="450px"/>]]></code-block>)

[//]: # (    </tab>)

[//]: # (</tabs>)

[//]: # ()
[//]: # (### Collapsible blocks)

[//]: # (Apart from injecting entire XML elements, you can use attributes to configure the behavior of certain elements.)

[//]: # (For example, you can collapse a chapter that contains non-essential information:)

[//]: # ()
[//]: # (#### Supplementary info {collapsible="true"})

[//]: # (Content under a collapsible header will be collapsed by default,)

[//]: # (but you can modify the behavior by adding the following attribute:)

[//]: # (`default-state="expanded"`)

[//]: # ()
[//]: # (### Convert selection to XML)

[//]: # (If you need to extend an element with more functions, you can convert selected content from Markdown to semantic markup.)

[//]: # (For example, if you want to merge cells in a table, it's much easier to convert it to XML than do this in Markdown.)

[//]: # (Position the caret anywhere in the table and press <shortcut>Alt+Enter</shortcut>:)

[//]: # ()
[//]: # (<img src="convert_table_to_xml.png" alt="Convert table to XML" width="706" border-effect="line"/>)

[//]: # ()
[//]: # (## Feedback and support)

[//]: # (Please report any issues, usability improvements, or feature requests to our)

[//]: # (<a href="https://youtrack.jetbrains.com/newIssue?project=WRS">YouTrack project</a>)

[//]: # (&#40;you will need to register&#41;.)

[//]: # ()
[//]: # (You are welcome to join our)

[//]: # (<a href="https://jb.gg/WRS_Slack">public Slack workspace</a>.)

[//]: # (Before you do, please read our [Code of conduct]&#40;https://plugins.jetbrains.com/plugin/20158-writerside/docs/writerside-code-of-conduct.html&#41;.)

[//]: # (We assume that you’ve read and acknowledged it before joining.)

[//]: # ()
[//]: # (You can also always email us at [writerside@jetbrains.com]&#40;mailto:writerside@jetbrains.com&#41;.)
[//]: # ()
[//]: # (<seealso>)

[//]: # (    <category ref="wrs">)

[//]: # (        <a href="https://plugins.jetbrains.com/plugin/20158-writerside/docs/markup-reference.html">Markup reference</a>)

[//]: # (        <a href="https://plugins.jetbrains.com/plugin/20158-writerside/docs/manage-table-of-contents.html">Reorder topics in the TOC</a>)

[//]: # (        <a href="https://plugins.jetbrains.com/plugin/20158-writerside/docs/local-build.html">Build and publish</a>)

[//]: # (        <a href="https://plugins.jetbrains.com/plugin/20158-writerside/docs/configure-search.html">Configure Search</a>)

[//]: # (    </category>)

[//]: # (</seealso>)