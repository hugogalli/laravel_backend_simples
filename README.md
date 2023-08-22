Como rodar o projeto


1.Use o comando git clone https://github.com/hugogalli/sh3_backend
2.Use o comando composer install
3.Use o comando cp .env.example .env 
4.Arruma tambem o .env.testing (Deixe identico porem mude o banco de dados)
4.Use o comando php artisan key:generate
5.Use o comando php artisan migrate
6.Use o comando php artisan serve
7.A documentacao e uso da API sem integração está em no link localhost:8000/api/documentation
8.Para testes automaticos unitarios use o comando php artisan test --env=testing