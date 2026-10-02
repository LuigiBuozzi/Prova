# Prova — Cadastro de Itens

Aplicação simples em Laravel para cadastrar itens com nome, cor e quantidade. Itens com menos de 5 unidades recebem um aviso de **estoque baixo**.

O projeto tem duas formas de uso:

- **Página web** (`/items`): formulário para adicionar itens e lista dos itens cadastrados.
- **API REST** (`/api/v1/items`): permite listar, criar, ver, editar e apagar itens em JSON.

## Tecnologias

- PHP 8.3+
- Laravel 13
- SQLite
- Pest (testes)

## Como rodar

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

Acesse `http://localhost:8000/items`.

## API

| Método | Rota | Descrição |
|---|---|---|
| GET | `/api/v1/items` | Lista os itens (paginado) |
| POST | `/api/v1/items` | Cria um item |
| GET | `/api/v1/items/{id}` | Mostra um item |
| PUT/PATCH | `/api/v1/items/{id}` | Atualiza um item |
| DELETE | `/api/v1/items/{id}` | Apaga um item |

Exemplo de criação:

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name": "Lápis", "color": "Amarelo", "quantity": 12}'
```

Os campos `name`, `color` e `quantity` são obrigatórios. Se algum estiver inválido, a API responde com status `422` e a lista de erros.

## Testes

```bash
php artisan test
```
