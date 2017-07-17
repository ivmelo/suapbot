# SUAP Bot
Bot do SUAP para telegram. Mostra notas, frequência, horários, locais de aula, turmas virtuais, materiais de aula, colegas de classe e calendário acadêmico.

Utiliza a [SUAP API PHP](https://github.com/ivmelo/suap-api-php) para acessar os dados do SUAP.

OBS: Este é o meu último ano no curso de TADS e consequentemente no IFRN, portanto, em breve não terei mais tempo (nem interesse) em manter este projeto. Por este motivo, estou deixando o código open-source para aqueles que como eu, não utilizam o Facebook ou simplesmente preferem o Telegram.

O Bot continuará ativo pelo menos enquanto eu for aluno da instituição. :)

## Vantagens do SUAP Bot
- Funciona para todos os cursos tecnicos e superiores do IFRN;
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

Abra o arquivo `.env` e Adicione a chave de acesso do seu bot no campo `TELEGRAM_BOT_TOKEN`.

### Cron
O SUAPBot utiliza o [Cron](https://en.wikipedia.org/wiki/Cron) para tarefas agendadas.

Para editar o seu crontab, execute:
```
$ crontab -e
```

Em seguida, insira o seguinte comando no seu Cron, ajustando conforme necessário.

```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

### Configurando Webhook do Telegram
Os requests são passados para a aplicação através de um webhook entre o telegram e o webserver no qual o bot está sendo executado. O telegram deve saber para qual URL deve enviar os requests conforme as mensagens sejam enviadas para o bot. Para isso, envia-se um request para o telegram com a url da aplicação.
```
curl -X POST --data "url=https://url.da.sua.aplicacao/webhooks/telegram" https://api.telegram.org/botSEU_BOT_TOKEN/setWebhook
```

OBS: Durante o desenvolvimento, você não pode simplesmente mandar o telegram enviar os requests para localhost da sua máquina. É necessário o uso de uma ferramenta de tunelamento para que possa criar um túnel entre a sua máquina e os servidores do telegram. Para isso, recomendo a ferramenta [ngrok](https://ngrok.com).


### Conclusão
Após seguir os passos acima, o Bot deve estar pronto para uso. Seja em desenvolvimento ou produção.

Caso tenha dúvidas sobre o projeto abra um issue. Responderei assim que possível.


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
