VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "tippliga-rebuild"
  config.vm.network :private_network, ip: "10.0.0.200"
  config.vm.provision :shell, path: "vagrant/provision.sh"
  config.vm.synced_folder ".", "/vagrant", type: "nfs"
end