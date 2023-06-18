#! /bin/sh

export LANG=C
export TZ=JST-9

rsyslogd -f /etc/rsyslog.conf &

/usr/bin/ssh-keygen -A
/usr/sbin/sshd

/usr/bin/redis-server /etc/redis.conf --protected-mode no &

/usr/sbin/php-fpm81 --allow-to-run-as-root --nodaemonize --force-stderr &

while test true
do
    date
    sleep 60
done



