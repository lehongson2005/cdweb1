# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/focal64"
  config.vm.box_version = "20240821.0.1"

  # Forward port cho web và phpMyAdmin
  config.vm.network "forwarded_port", guest: 8080, host: 8080
  config.vm.network "forwarded_port", guest: 8081, host: 8081
  config.vm.network "forwarded_port", guest: 3306, host: 3306

  # Private & public network
  config.vm.network "private_network", ip: "192.168.33.10"
  config.vm.network "public_network"

  # Đồng bộ thư mục sources
  config.vm.synced_folder "./training-php", "/vagrant"

  config.vm.provider "virtualbox" do |vb|
    vb.gui = true
    vb.memory = "4096"
    vb.cpus = 2
  end

  # Cài Apache, Docker, Docker Compose, Git
  config.vm.provision "shell", inline: <<-SHELL
    apt-get update
    apt-get install -y apache2
    apt-get install -y docker.io docker-compose
    usermod -aG docker vagrant
    apt-get install -y make git net-tools
  SHELL

  config.vm.boot_timeout = 600
end
