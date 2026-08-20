# Sorteio SaaS — Laravel

Código-fonte do SaaS de sorteios: Home, Login/Registro, Painel do usuário,
Admin completo e liberação automática via Pix (Mercado Pago) para sorteios
com mais de 100 comentários. Visual próprio (tema "bilhete premiado"), com
CSS já compilado em `public/build/`.

Este pacote já vem com **repositório git inicializado** (primeiro commit
feito) e pronto pra instalação e atualização via Git no servidor.

👉 **Guia completo de instalação/atualização no aaPanel: `DEPLOY-AAPANEL.md`**

## Fluxo resumido

**Primeira vez:**
```bash
git remote add origin https://github.com/seu-usuario/sorteio-saas.git
git push -u origin main
# no servidor:
git clone https://github.com/seu-usuario/sorteio-saas.git
bash deploy/install.sh
```

**Toda atualização depois disso:**
```bash
# na sua máquina
git add -A && git commit -m "mudança" && git push
# no servidor
bash deploy/deploy.sh
```

Detalhes completos, incluindo configuração de Nginx, SSL, fila e cron
específicos do aaPanel, estão em `DEPLOY-AAPANEL.md`.

## Estrutura do que está incluído

- `app/` — Models, Controllers, Middleware, Services (Mercado Pago)
- `database/migrations` + `database/seeders/AdminSeeder.php`
- `resources/views` — todas as telas, já estilizadas
- `resources/css/app.css` + `tailwind.config.js` — design system próprio
- `public/build/` — CSS/JS **já compilados** e versionados no git
- `.github/workflows/build-assets.yml` — rebuilda os assets automaticamente
  a cada push que altera `resources/`, sem precisar de Node no servidor
- `deploy/` — Nginx, Supervisor, `.env` de exemplo, `install.sh`, `deploy.sh`
- `DEPLOY-AAPANEL.md` — guia completo passo a passo

⚠️ **Uma coisa não veio pronta:** meu ambiente não tem acesso ao Packagist,
só ao npm — então a pasta `vendor/` do Laravel (dependências PHP) não está
incluída nem versionada (fica no `.gitignore`, como é padrão em qualquer
projeto Laravel). O `deploy/install.sh` roda o `composer install` por você.

## O que falta implementar (pontos com TODO no código)

- `GiveawayController::buscarQuantidadeDeComentarios()` — chamar de fato
  a Instagram Graph API (`GET /{media-id}/comments`) usando o token salvo
  em `InstagramToken`.
- Lógica real de sorteio (hoje está mockada) — puxar a lista de comentários,
  aplicar filtros (menção obrigatória, remover duplicados/bots) e sortear.
- Tela de "resetar senha" e verificação de e-mail.
- Job agendado para renovar tokens do Instagram antes de expirar.
- Você ainda precisa passar pelo **App Review da Meta** antes de liberar a
  conexão de Instagram para qualquer cliente sem cadastro manual como tester.
