# Como Rodar o Projeto

Siga estas etapas para configurar e executar o projeto localmente:


1. Clone o repositório: 
git clone https://github.com/hugogalli/laravel_backend_simples

2. Instale as dependências do Composer: 
composer install


3. Crie um arquivo `.env` baseado no `.env.example`: 
cp .env.example .env


4. Configure o arquivo `.env` e também o `.env.testing` (mantenha similar, mas ajuste o banco de dados). 


5. Gere a chave de criptografia: 
php artisan key:generate


6. Execute as migrações do banco de dados para criar as tabelas: 
php artisan migrate


7. Inicie o servidor local: 
php artisan serve


8. Acesse a documentação e uso da API sem integração em: [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)


9. Para executar testes unitários automatizados, use o comando:
php artisan test --env=testing
