# Tasks API

Tasks API é uma aplicação desenvolvida em Laravel para gerenciar tarefas. Este README fornece instruções detalhadas para configurar o ambiente, executar o projeto e realizar os testes automatizados.

---

## Requisitos

Certifique-se de que você possui os seguintes requisitos instalados:

- **PHP** ≥ 8.0
- **Composer**
- **MySQL** ou outro banco de dados compatível
- **Laravel** (gerenciado via Composer)

---

## Configuração do Projeto

### 1. Clone o Repositório
```bash
git clone https://github.com/ricardo006/tasks-api.git
cd tasks-api
```

### 2. Instale as Dependências
```bash
composer install
```

### 3. Configure o Arquivo `.env`
Copie o arquivo de exemplo e ajuste as variáveis conforme o ambiente:
```bash
cp .env.example .env
```

Edite o arquivo `.env` para incluir as credenciais do banco de dados:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tasks
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 4. Gere a Chave da Aplicação
```bash
php artisan key:generate
```

### 5. Execute as Migrações e Seeds (se aplicável)
```bash
php artisan migrate --seed
```

---

## Executando o Projeto

### Servidor de Desenvolvimento
Inicie o servidor de desenvolvimento com o comando abaixo:
```bash
php artisan serve
```

Acesse a aplicação em: [http://localhost:8000](http://localhost:8000)

---
## Rotas Disponíveis

### Autenticação
- `POST /login` - Realiza o login de um usuário.
- `POST /register` - Registra um novo usuário.

### Usuários (Protegidas por autenticação)
- `GET /users` - Lista todos os usuários.
- `GET /users/{id}` - Exibe os detalhes de um usuário.
- `POST /users` - Cria um novo usuário.
- `PUT /users/{id}` - Atualiza um usuário existente.
- `DELETE /users/{id}` - Exclui um usuário.

### Tarefas (Protegidas por autenticação)
- `GET /tasks` - Lista todas as tarefas.
- `GET /tasks/{id}` - Exibe os detalhes de uma tarefa.
- `GET /tasks/status` - Obtém tarefas por status.
- `POST /tasks` - Cria uma nova tarefa.
- `PUT /tasks/{id}` - Atualiza uma tarefa existente.
- `PUT /tasks/{taskId}/status/{status}` - Atualiza o status de uma tarefa.
- `DELETE /tasks/{id}` - Exclui uma tarefa.

### Perfil do Usuário
- `GET /user` - Retorna os dados do usuário autenticado.
- `POST /logout` - Realiza o logout do usuário.

---

## Documentação e Collection do Postman

Para facilitar a utilização das rotas, foi incluída uma collection do Postman no projeto.

### Localização da Collection
A collection pode ser encontrada em:

`public/collection/Tasks.postman_collection.json`

### Importando a Collection

1. Abra o Postman.
2. Clique em **Import**.
3. Selecione o arquivo `TasksAPI.postman_collection.json`.
4. As rotas estarão disponíveis para uso.

### Utilizando o Token de Autenticação
Para acessar as rotas protegidas:
1. Realize o login utilizando a rota `POST /login`.
2. Copie o token gerado na resposta.
3. No Postman, acesse **Authorization**, escolha o tipo **Bearer Token** e insira o token copiado no campo correspondente.

---

## Executando Testes

### Rodando os Testes
Os testes são escritos para garantir o funcionamento correto da aplicação.

Execute todos os testes com:
```bash
php artisan test
```

### Estrutura dos Testes
- **Unit Tests**: Testam funcionalidades específicas de serviços e métodos isolados.
- **Feature Tests**: Validam rotas e comportamentos da aplicação.

### Cobertura de Testes
Os testes cobrem as seguintes funcionalidades:
- Criação de tarefas
- Atualização de status de tarefas
- Exibição de detalhes de uma tarefa
- Testes de comportamento para casos não encontrados ou erros de entrada

---

## Comandos Úteis

### Atualizar o Banco de Dados
Caso precise resetar o banco de dados:
```bash
php artisan migrate:fresh --seed
```

### Listar Rotas Disponíveis
```bash
php artisan route:list
```

---

## Possíveis Erros
- **Erro: Route [login] not defined**:
  - Certifique-se de que a rota de login está configurada ou desabilite a middleware `auth` para testes locais.

- **Erro: The GET method is not supported**:
  - Verifique o método HTTP na requisição. Use `POST` em rotas de criação de recursos.

- **Banco vazio**:
  - Rode o comando de seeds: `php artisan db:seed`.

---

## Desenvolvedor

Ricardo Oliveira 