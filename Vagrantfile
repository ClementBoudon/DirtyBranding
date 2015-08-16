# -*- mode: ruby -*-
# vi: set ft=ruby :

$script = <<SCRIPT
export APP_ENV=dev
cd /var/www/web/api/web/v1 && composer install
cd /var/www/web/api/web/v1/cron && composer install
sudo cp /var/www/vhost/*.conf /etc/apache2/sites-available/
sudo cp /var/www/vhost/*.conf /etc/apache2/sites-enabled/
sudo rm /etc/apache2/sites-available/000-default.conf
sudo rm /etc/apache2/sites-enabled/000-default.conf
echo '127.0.0.1 api.dirtybranding.com' | sudo tee --append /etc/hosts > /dev/null
echo '127.0.0.1 app.dirtybranding.com' | sudo tee --append /etc/hosts > /dev/null
echo '127.0.0.1 www.dirtybranding.com' | sudo tee --append /etc/hosts > /dev/null
sudo /etc/init.d/apache2 restart
mysql -uroot -proot scotchbox < /var/www/Database/DumpStructure_DB_API.sql
cd /var/www/
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
sudo mv phpunit.phar /usr/local/bin/phpunit
sudo chmod +x /usr/local/bin/phpunit
# Elasticsearch Stuff
## Elasticsearch core
sudo apt-get -y update
sudo apt-get -y install openjdk-7-jre-headless
wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/1.7/debian stable main" | sudo tee -a /etc/apt/sources.list.d/elasticsearch-1.7.list
sudo apt-get u-y pdate && sudo apt-get -y install elasticsearch
sudo update-rc.d elasticsearch defaults 95 10
sudo /etc/init.d/elasticsearch start
## Elasticsearch PHP client
cd /var/www/web/api/web/v1/cron/
sudo composer self-update
sudo composer require ruflin/elastica
sudo composer install
## Elasticsearch Frontend
sudo /usr/share/elasticsearch/bin/plugin -i elasticsearch/marvel/latest
sudo /etc/init.d/elasticsearch restart
# End Elasticsearch Stuff
SCRIPT

Vagrant.configure("2") do |config|

  config.vm.box = "scotch/box"
  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.hostname = "scotchbox"
  config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]
  config.vm.provision "shell", inline: $script

end
