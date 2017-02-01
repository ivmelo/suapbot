# SUAPBot
Bot do SUAP para telegram. Mostra notas, presença, horários e locais de aula e o calendário acadêmico.

## Tecnologias utilizadas
- PHP7
- MySQL/MariaDB
- Beanstalkd
- Supervisor

## Instruções de Deploy/Configuração de Ambiente

1. Clone o repositório do projeto.
```
$ git clone git@github.com:ivmelo/suapbot.git
```

1. Instale as dependências através do composer.
```
$ composer install
```

1. Dependendo do seu ambiente de desenvolvimento/deployment, é necessário setar permissões em algumas pastas.
```
$ sudo chown -R ubuntu:www-data storage
$ sudo chown -R ubuntu:www-data vendor
$ sudo chmod -R g+s storage
$ sudo chmod -R g+s vendor
$ sudo chmod -R 775 storage
$ sudo chmod -R 775 vendor
```


1. Agora você precisará criar um bot no telegram. Para isso, contate o @botfather. Para mais informações, visite o link a seguir: https://core.telegram.org/bots 

1. Copie o arquivo .env para .env.example e 

1. Adicione a chave do seu bot no campo TELEGRAM_BOT_TOKEN.

1. Altere o campo APP_QUEUE to ```beanstalkd```.

1. Preencha as credenciais do seu banco de dados.


1. Execute as migrações.
```
$ php artisan migrate
```


1. Configure o seu sistema de queues. O recomendado para o SUAPBot e que está sendo utilizado em produção é o beanstalkd. 
```
$ sudo apt-get install beanstalkd
$ sudo service start beanstalkd
```

1. Configure o cron para as tarefas agendadas.
```
$ crontab -e
```

1. Insira o seguinte comando no seu cron, ajustando conforme necessário.

```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

1. Configure o ```supervisor``` para executar as tarefas enfileiradas.
```
$ sudo apt-get install supervisor

$ nano /etc/supervisor/conf.d/suapbot-worker.conf
```

Abaixo segue um exemplo de arquivo que pode ser ajustado para funcionar com o ```supervisor```.

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

Depois disso, inicie o supervisor.

```
$ sudo supervisorctl reread
$ sudo supervisorctl update
$ sudo supervisorctl start laravel-worker:*
```

1. Envie um request para o telegram para configurar o webhook da sua aplicação.
```
curl -X POST --data "url=https://url.da.sua.aplicacao/webhooks/telegram" https://api.telegram.org/botSEU_BOT_TOKEN/setWebhook
```

Caso tenha d

Se tudo ocorreu bem, o bot deve estar funcionando corretamente. Caso tenha dúvidas, use o pai google.

Peace,
Ivanilson.


### Licença

Copyright (c) 2017 Ivanilson Melo
