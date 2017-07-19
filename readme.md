![SUAP BOT](http://i.imgur.com/1bXOo0H.jpg)

[![StyleCI](https://styleci.io/repos/65643183/shield?branch=master)](https://styleci.io/repos/65643183)

Bot do SUAP para telegram. Mostra boletim (notas, faltas, frequência), horários, locais de aula, turmas virtuais (materiais de aula, colegas de classe, detalhes de aulas) e calendário acadêmico. Também envia notificações quando novas informações são inseridas no boletim. Funciona apenas para o SUAP do IFRN, mas pode ser modificado para funcionar com outras instituições.

Utiliza a [SUAP API PHP](https://github.com/ivmelo/suap-api-php) para acessar os dados do SUAP.

O projeto é open-source e contribuições de alunos do IFRN são muito bem-vindas.

## Vantagens do SUAP Bot
- Desenvolvido para ser compatível com todos os cursos tecnicos e superiores do IFRN;
- Sua privacidade é mantida e suas conversas com o bot não são armazenadas nem visualizadas;
- O código é open source, qualquer pessoa pode utilizar, modificar, distribuir, inspecionar, basta que siga os termos da licença (GNU Affero).

## Tecnologias utilizadas
- PHP7
- Laravel
- MySQL/MariaDB

## Instalação (Desenvolvimento ou Produção)
É altamente recomendado o uso de [Laravel Homestead](https://laravel.com/docs/5.4/homestead) para o desenvolvimento do projeto.

Primeiro, clone o repositório:
```
git clone git@github.com:ivmelo/suapbot.git
cd suapbot
```

Instale as dependências da aplicação através do composer:
```
composer install
```

### Permissões
Dependendo do seu ambiente de desenvolvimento/produção, é necessário setar permissões em algumas pastas da sua aplicação.
```
sudo chown -R ubuntu:www-data storage
sudo chown -R ubuntu:www-data vendor
sudo chmod -R g+s storage
sudo chmod -R g+s vendor
sudo chmod -R 775 storage
sudo chmod -R 775 vendor
```

### Criação do Bot e Arquivo de Configuração
Em seguida, é necessário criar um bot no telegram. Para isso, contate o [@botfather](https://telegram.me/botfather). Para mais informações sobre a API de Bots do telegram, acesse o seguinte link: [telegram.org/bots](https://core.telegram.org/bots).

Após criar o seu bot, copie o arquivo `.env.example` e renomeie-o para `.env`.

Abra o arquivo `.env` e:
- Adicione a chave de acesso do seu bot no campo `TELEGRAM_BOT_TOKEN`;
- Adicione o username do seu bot no campo `TELEGRAM_BOT_HANDLE`;
- Adicione uma string aleatória no campo `TELEGRAM_WEBHOOK_SECRET`;
- (Opcional) Criar uma conta no [Bugsnag](https://bugsnag.com) e adicionar a API Key no campo `BUGSNAG_API_KEY` para receber relatórios completos sobre os erros durante o desenvolvimento.

### Cron
O SUAPBot utiliza o [Cron](https://en.wikipedia.org/wiki/Cron) para tarefas agendadas.

Para que as tarefas agendadas funcionem corretamente, é necessário adicionar uma linha no Cron da sua máquina. Para isso, digite:

```
$ crontab -e
```

Em seguida, insira o seguinte comando no seu Cron, ajustando o `path/to/artisan` para apontar para o artisan da sua aplicação.

```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

### Configurando Webhook do Telegram
Os requests são passados para a aplicação através de um webhook entre o telegram e o webserver no qual o bot está sendo executado. O Telegram deve saber para qual URL deve enviar os requests quando mensagens são enviadas para o bot. Para configurar o webhook, envia-se um request para o Telegram com a url de acesso ao bot.
```
curl -X POST --data "url=https://url.da.sua.aplicacao/webhooks/TELEGRAM_WEBHOOK_SECRET/telegram" https://api.telegram.org/botSEU_BOT_TOKEN/setWebhook
```
Não esqueça de substituir `TELEGRAM_WEBHOOK_SECRET` pelo segredo inserido no arquivo `.env` da sua aplicação e `SEU_BOT_TOKEN` pelo token do seu bot, _obvio_.

OBS: Durante o desenvolvimento, você não pode simplesmente mandar o telegram enviar os requests para localhost da sua máquina. É necessário o uso de uma ferramenta de tunelamento que possa criar um túnel entre a sua máquina e os servidores do Telegram. Para isso, recomendo a ferramenta [ngrok](https://ngrok.com).


## Contribuições
Antes de enviar contribuições com novas funcionalidades ou comandos, recomendo abrir um issue para discutir-mos a utilidade da funcionalidade proposta. Para o conserto de bugs ou alterações menores, favor enviar o pull request diretamente.

## Aviso
Este é o meu último ano no curso de TADS e consequentemente no IFRN; portanto, em breve não terei mais tempo (nem interesse) em manter este projeto. Por isso, estou deixando o código open-source para aqueles que como eu, não utilizam Facebook ou simplesmente gostam mais do SUAP Bot do que das alternativas.

**Continuarei mantendo o bot ativo enquanto houver uma quantidade significativa de usuários ativos e alunos interessados em manter o projeto.**

## Licença
Copyright (C) 2016-2017  Ivanilson Melo

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published
by the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
