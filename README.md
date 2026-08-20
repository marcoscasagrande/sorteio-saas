# Sorteio SaaS — Laravel

Código-fonte do SaaS de sorteios: Home, Login/Registro, Painel do usuário,
Admin completo e liberação automática via Pix (Mercado Pago) para sorteios
com mais de 100 comentários. Visual próprio (tema "bilhete premiado"),
com CSS já compilado em `public/build/` — não depende de CDN.

⚠️ **Uma coisa não veio pronta:** meu ambiente não tem acesso ao Packagist
(repositório de pacotes PHP), só ao npm — então a pasta `vendor/` do Laravel
(dependências PHP) não está incluída. Você precisa rodar `composer install`
no servidor (é um comando só, coberto no guia de deploy).

O CSS/JS de produção **já vêm compilados** em `public/build/` — não é
necessário rodar `npm run build`, a menos que você altere o visual.

👉 **Para instalar num servidor AWS com aaPanel, siga `DEPLOY-AAPANEL.md`** —
guia passo a passo completo (Nginx, PHP-FPM, SSL, fila em background, cron).

## Resumo rápido (qualquer servidor Linux com PHP 8.2+, MySQL, Nginx/Apache)

```bash
composer install --no-dev --optimize-autoloader
cp deploy/.env.production.example .env
php artisan key:generate
# edite o .env com as credenciais do banco, Mercado Pago e Instagram
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Login padrão do admin: `admin@sorteiosaas.com` / `troque-esta-senha`
— **troque a senha imediatamente**.

## Estrutura do que está incluído

- `app/` — Models, Controllers, Middleware, Services (Mercado Pago)
- `database/migrations` + `database/seeders/AdminSeeder.php`
- `resources/views` — todas as telas, já estilizadas
- `resources/css/app.css` + `tailwind.config.js` — design system próprio
- `public/build/` — CSS/JS **já compilados**
- `deploy/` — Nginx, Supervisor, `.env` de exemplo, script de deploy
- `DEPLOY-AAPANEL.md` — guia completo passo a passo pro aaPanel

## O que falta implementar (pontos com TODO no código)

- `GiveawayController::buscarQuantidadeDeComentarios()` — chamar de fato
  a Instagram Graph API (`GET /{media-id}/comments`) usando o token salvo
  em `InstagramToken`.
- Lógica real de sorteio (hoje está mockada) — puxar a lista de comentários,
  aplicar filtros (menção obrigatória, remover duplicados/bots) e sortear.
- Tela de "resetar senha" e verificação de e-mail.
- Job agendado para renovar tokens do Instagram antes de expirar (o cron
  já está configurado no guia de deploy — falta o Job em si).
- Você ainda precisa passar pelo **App Review da Meta** antes de liberar a
  conexão de Instagram para qualquer cliente sem cadastro manual como tester.
