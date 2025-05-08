# Setting Up WAMP to Use Absolute File Paths (Required for correct functionality of website)

To ensure your website runs correctly in WAMP, you’ll need to configure Apache to use a **Virtual Host** pointing directly to your project folder. This means all file paths will be absolute, so you don't have to worry about 'relative' linking in your website files. Follow the steps below:

---

## Step 1: Navigate to Your Apache Configuration Folder

Find your WAMP installation directory (the default path is shown below):

**`C:\wamp64\bin\apache\apache2.4.54.2\conf\extra`**

*Don’t worry if your Apache version is different; just navigate accordingly.*

---

## Step 2: Edit `httpd-vhosts.conf`

1. Open the file called **`httpd-vhosts.conf`** in a text editor (Notepad++, VSCode, etc).

2. **Add a new "Virtual Host" entry:**  
   Copy and paste the following code, **modifying the directory paths** if your site is in a different location.

    ```apache
    <VirtualHost *:80>
        ServerName view-localhost
        DocumentRoot "C:/wamp64/www/Earthquake-Monitoring-CW"
        <Directory "C:/wamp64/www/Earthquake-Monitoring-CW">
            AllowOverride All
            Require all granted
        </Directory>
    </VirtualHost>
    ```

> **Note:**  
> Make sure the `DocumentRoot` and `<Directory>` paths match the folder where you've saved your website files.

---

## Step 3: Access Your Site in the Browser

1. Restart WAMP or just Apache (for config changes to take effect).
2. In your browser, go to:

    ```
    http://view-localhost/
    ```

You should now see the website!
