# Sorteio SaaS — Laravel

SaaS completo de sorteios: Home com SEO e CTA, Login/Registro, Painel do
usuário, Admin completo (usuários, relatório de vendas, configurações
gerais), sorteio com hash SHA-256 de auditoria e página pública de
comprovação, revelação do vencedor com confete, e liberação automática via
Pix (Mercado Pago) para sorteios com mais de 100 comentários.

Este pacote já vem com **repositório git inicializado** e pronto pra
instalação e atualização via Git no servidor.

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

## O que tem no admin

- **Painel** — métricas gerais (usuários, cadastros do dia, sorteios
  aguardando Pix, faturamento do mês)
- **Usuários** — lista com busca, detalhe por usuário (sorteios + pagamentos)
- **Vendas** (`/admin/relatorio-de-vendas`) — filtro por período, faturamento
  total, ticket médio, gráfico de faturamento por dia, lista de pagamentos
- **Configurações** (`/admin/configuracoes`) — nome do site, tagline, logo,
  título/descrição de SEO, e as **chaves do Mercado Pago** (Access Token e
  Public Key) — tudo editável sem precisar mexer no `.env` ou reiniciar
  o servidor

As chaves do Mercado Pago cadastradas no admin têm prioridade sobre o
`.env`; o `.env` funciona como fallback se você preferir configurar por lá.

## Sorteio: hash de auditoria e revelação

- Cada sorteio realizado gera um hash **SHA-256** único, calculado a partir
  do sorteio, do vencedor, do comentário sorteado e do instante exato —
  qualquer alteração no resultado depois do fato muda o hash
- Link público de comprovação em `/verificar/{hash}` — não exige login,
  pode ser compartilhado com a audiência
- Ao clicar em "Sortear", o vencedor é revelado com **confete** e o nome +
  comentário sorteado aparecem no card de resultado

## Estrutura do que está incluído

- `app/` — Models, Controllers, Middleware, Services (Mercado Pago)
- `database/migrations` + `database/seeders/AdminSeeder.php`
- `resources/views` — todas as telas, já estilizadas
- `resources/js/app.js` — confete (canvas-confetti) e gráfico (Chart.js)
- `public/build/` — CSS/JS **já compilados** e versionados no git
- `.github/workflows/build-assets.yml` — rebuilda os assets automaticamente
  a cada push que altera `resources/`
- `deploy/` — Nginx, Supervisor, `.env` de exemplo, `install.sh`, `deploy.sh`
- `DEPLOY-AAPANEL.md` — guia completo passo a passo

⚠️ **Uma coisa não veio pronta:** meu ambiente não tem acesso ao Packagist,
só ao npm — então a pasta `vendor/` do Laravel não está incluída/versionada
(fica no `.gitignore`, como é padrão em qualquer projeto Laravel). O
`deploy/install.sh` roda o `composer install` por você.

## O que falta implementar (pontos com TODO no código)

- `GiveawayController::buscarComentarios()` — chamar de fato a Instagram
  Graph API (`GET /{media-id}/comments`) usando o token salvo em
  `InstagramToken`, com paginação até esgotar os comentários.
- Filtros do sorteio (menção obrigatória, remover duplicados/bots) — hoje
  o sorteio pega qualquer comentário retornado pela função acima.
- Tela de "resetar senha" e verificação de e-mail.
- Job agendado para renovar tokens do Instagram antes de expirar.
- App Review da Meta, antes de liberar a conexão de Instagram pra qualquer
  cliente sem cadastro manual como tester.
