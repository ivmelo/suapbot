# suapbot
SUAP Bot for telegram.


#### Deploy instructions:


1. git clone
```
$ git clone git@github.com:ivmelo/suapbot.git
```

1. composer install
```
$ composer install
```

1. directory permissions
```
$ sudo chown -R ubuntu:www-data storage
$ sudo chown -R ubuntu:www-data vendor
$ sudo chmod -R g+s storage
$ sudo chmod -R g+s vendor
$ sudo chmod -R 775 storage
$ sudo chmod -R 775 vendor
```


1. set up telegram key

4. open .env
4. add telegram key
4. set APP_QUEUE to beanstalkd
4. set database credentials


1. run migrations
```
$ php artisan migrate
```


1. set up queue (beanstalkd preferred) (default port and stuff is just fine for small environments)
```
$ sudo apt-get install beanstalkd
$ sudo service start beanstalkd
```

1. set up cron for scheduled jobs
```
$ crontab -e
```

cron command to be put there

```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

1. set up supervisor to run the task queue
```
$ sudo apt-get install supervisor

$ nano /etc/supervisor/conf.d/suapbot-worker.conf
```

content to be put there adjust accordingly

```
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/app.com/artisan queue:work sqs --sleep=3 --tries=3
autostart=true
autorestart=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/forge/app.com/worker.log
```

after that, start the proccess in supervisor

```
$ sudo supervisorctl reread
$ sudo supervisorctl update
$ sudo supervisorctl start laravel-worker:*
```

set up telegram webhook

1. check that everything is working. figure. it. out. how.
