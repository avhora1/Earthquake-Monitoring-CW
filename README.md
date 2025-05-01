For this website to work correctly, you MUST change a config file in wamp so that it no longer relies on relative file paths in the 'www' folder. Heres how you do it:

Step 1:
Go to this directory (or wherever you saved your wamp application)
C:\wamp64\bin\apache\apache2.4.54.2\conf\extra
Don't worry too much about the apache version.

Step2:
Edit the file called 'httpd-vhosts.conf'

Copy and paste the text below, adding another 'virtual host':

<VirtualHost *:80>
    ServerName view-localhost
    DocumentRoot "C:/wamp64/www/Earthquake-Monitoring-CW"
    <Directory "C:/wamp64/www/Earthquake-Monitoring-CW">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
The directory here needs to point to wherever you have the website files saved. 

Step 3:
Load up 'view-localhost' on your browser and you should see the website appear. 

If you ever change the location of the website, just change this directory and it should work. 
All the 'href' instances in the website all just look in this directory, there is no longer relative file paths. 
