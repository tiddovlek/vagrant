# Installation

After Vagrant installation PHP cli is pointed to PHP v 7. To fix this run:
`alias php='/usr/bin/php5.6'`

To prevent Composer from crashing, run:
`sudo /bin/dd if=/dev/zero of=/var/swap.1 bs=10M count=1024 && sudo /sbin/mkswap /var/swap.1 && sudo /sbin/swapon /var/swap.1`