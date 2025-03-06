## Passos para execução do projeto

- Ter instalado o Composer;
- Ter instalado o PHP 8.2 ou superior;
- Ter o Docker desktop instalado;
- Na raiz do projeto executar "composer update" no terminal;
- Baixar a lib do Sail: "composer require laravel/sail --dev";
- Instalar a lib do Sail no projeto: "php artisan sail:install";
- Abrir o Docker Desktop;
- Iniciar os contêiner Docker: "./vendor/bin/sail up"
- Apelidando os comandos do Sail: "alias sail='vendor/bin/sail'";
- Executar as migrations: "sail artisan migrate";
- Iniciar o worker das filas de email: "sail artisan queue:work";

## Estou utilizando o MailTrip, um server fake de emails para desenvolvimento, siga os passos a seguir:
- acesse: https://mailtrap.io/
- Crie uma conta;
- Selecione o plano gratuito: "Email testing";
- Em seguida selecione My Inbox;
- Altere as credenciais que aparecem na tala do inbox para o .env;
- Em seguida pode executar os disparos;

## Documentação
- Para gerar a documentação execute na raiz do projeto o seguinte comando: "sail artisan l5-swagger:generate";
- Abra a url no seu navegador: http://localhost:80/api/documentation
