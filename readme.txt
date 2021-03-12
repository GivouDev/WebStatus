__      __      ___.     _________ __          __
/  \    /  \ ____\_ |__  /   _____//  |______ _/  |_ __ __  ______
\   \/\/   // __ \| __ \ \_____  \\   __\__  \\   __\  |  \/  ___/
\        /\  ___/| \_\ \/        \|  |  / __ \|  | |  |  /\___ \
 \__/\  /  \___  >___  /_______  /|__| (____  /__| |____//____  >
      \/       \/    \/        \/           \/                \/

Made with love by Givou


#Demo
https://status.nerdcity.at

#License
This Project is Licensed under the Attribution-ShareAlike 4.0 International (CC BY-SA 4.0) License
View your rights here: https://creativecommons.org/licenses/by-sa/4.0/

#setup
This is a sample setup! You can modify folder names as you wish.

#1:
cd /var/www

#2:
git clone PROJECTLINKISNICHTDA

#3:
chown -R www-data:www-data

#4:
cd /etc/nginx/sites-available

#5:
nano status.conf

6#
You can now paste this Nginx Sample Configuration.
Its made very simple, and should work for php7.4 nginx webservers.

server {
  listen 80;
  server_name status.example.com;

  location / {
     rewrite ^/?(.*) https://$server_name/$1 redirect;
  }
}


server {
     listen 443;

     root /var/www/webstatus;
     index index.php;

      location / {
        index  index.php;
      }


     server_name status.example.com;


      location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize = 100M \n post_max_size=100M";
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
        fastcgi_intercept_errors off;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
        include /etc/nginx/fastcgi_params;
    }
}

#7:
ln -s /etc/nginx/sites-available/status.conf /etc/nginx/sites-enabled

#8:
(If you have installed cerbot! The Configuration above needs a SSL Certificate to work properly)
cerbot --nginx

#9:
Select "status.example.com" and follow the introductions as you wish

#10:
Open "status.example.com" in your browser and fill out the Setup field

#11:
ENJOY Webstatus, if you have any questions, requests or something write me at thiemo.tiziani@nerdcity.at or make a issue at git.thiemoo.at
