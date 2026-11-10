# Madeflix — Instruções para avaliação

## 1. Requisitos

- PHP 8.2 ou superior
- Composer
- SQLite ou outro banco compatível com Laravel
- Acesso à internet para a integração com o TMDB

## 2. Instalação

Na raiz do projeto, execute:

```bash
composer install
```

Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

No Windows PowerShell, o equivalente é:

```powershell
Copy-Item .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

## 3. Banco SQLite

Crie o arquivo vazio do banco dentro da pasta `database`.

Linux/macOS:

```bash
touch database/database.sqlite
```

Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

No `.env`, confirme:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Depois execute as migrations:

```bash
php artisan migrate
```

Se o projeto possuir seeders disponíveis, eles podem ser executados com:

```bash
php artisan db:seed
```

## 4. Configuração da integração TMDB

A aplicação utiliza o Read Access Token do TMDB.

No arquivo `.env`, preencha:

```env
TMDB_ACCESS_TOKEN=COLE_O_READ_ACCESS_TOKEN_AQUI
TMDB_BASE_URL=https://api.themoviedb.org/3
```

Depois limpe o cache de configuração:

```bash
php artisan config:clear
```

## 5. Arquivos enviados manualmente

A integração com o TMDB é opcional para o cadastro.

Também é possível cadastrar um filme manualmente informando:

- Título;
- Sinopse;
- Imagem de capa.

Nesse caso, o filme é salvo utilizando os dados enviados pelo formulário, sem depender da API.

Para que as imagens enviadas sejam exibidas, execute:

```bash
php artisan storage:link
```

## 6. Inicialização

Para iniciar o servidor local:

```bash
php artisan serve
```

Acesse:

```text
http://127.0.0.1:8000
```

## 7. Páginas da aplicação

- `/` — listagem, hero, busca, favoritos, filtro por gênero e carrosséis;
- `/cadastrar` — cadastro e gerenciamento dos filmes adicionados;
- `/ver/{filme}` — detalhes, player, favorito e exclusão.

## 8. Como testar

### Cadastro usando TMDB

1. Acesse `/cadastrar`.
2. Informe apenas o título de um filme existente no TMDB.
3. Clique em “Salvar filme”.
4. A aplicação buscará capa, banner, sinopse, gênero, logo e trailer.

### Cadastro manual

1. Acesse `/cadastrar`.
2. Informe o título.
3. Preencha a sinopse.
4. Selecione uma imagem.
5. Salve o filme.

### Funcionalidades adicionais

Também devem ser testados:

- Busca por título;
- Filtro por gênero;
- Carrossel com drag to scroll;
- Carregamento progressivo de filmes;
- Preview após 1 segundo de hover;
- Popup de informações;
- Player completo;
- Favoritos;
- Exclusão de filmes;
- Responsividade em celular e desktop.

