# Sorteio SaaS — Laravel

SaaS completo de sorteios: Home com SEO e CTA, Login/Registro (com 2FA por
e-mail para admins), Painel do usuário, Admin completo (usuários, relatório
de vendas, planos, logs de auditoria, configurações gerais), sorteio com
hash SHA-256 de auditoria, filtros de participação, múltiplos vencedores,
re-sorteio, selo embutível, busca de comentários em fila, revelação com
confete, e liberação automática via Pix (Mercado Pago) — tudo com preços
e limites editáveis pelo admin.

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

- **Painel** — métricas gerais
- **Usuários** — lista com busca, detalhe por usuário
- **Vendas** (`/admin/relatorio-de-vendas`) — filtro por período, faturamento,
  ticket médio, gráfico diário, lista de pagamentos
- **Planos** (`/admin/planos`) — CRUD completo de planos de assinatura
  (nome, preço, período, limite de sorteios, destaque, ativo/inativo) —
  exibidos automaticamente na Home quando ativos
- **Logs de auditoria** (`/admin/logs`) — login, sorteios realizados/refeitos,
  pagamentos aprovados, com filtro por ação
- **Configurações** (`/admin/configuracoes`) — nome do site, tagline, logo,
  SEO, **limite grátis de comentários e valor cobrado no sorteio avulso**,
  e as chaves do Mercado Pago — tudo editável sem mexer no servidor

## Sorteio: recursos

- **Fila em background**: buscar os comentários do post roda em fila
  (`FetchGiveawayComments`), a página do sorteio atualiza sozinha até ficar
  pronta — não trava a requisição em posts com muitos comentários
- **Filtros de participação**: exigir menção a N amigos, hashtag obrigatória,
  seguir a conta — configuráveis na criação do sorteio
- **Múltiplos vencedores** por sorteio (1 a 20)
- **Preview dos comentários elegíveis** antes de sortear
- **Hash SHA-256** de auditoria + link público de comprovação
  (`/verificar/{hash}`), sem exigir login
- **Selo HTML embutível** — o organizador copia e cola no próprio site
- **Re-sorteio com 1 clique** — o resultado anterior fica registrado no
  histórico, visível na página de comprovação
- **Revelação com confete** (canvas-confetti) ao sortear

## Segurança

- **2FA por e-mail** para contas admin — código de 6 dígitos enviado no
  login, válido por 10 minutos (se o e-mail ainda não estiver configurado,
  o código cai no log da aplicação em vez de travar o acesso)
- **Logs de auditoria** para ações sensíveis (login, sorteio, pagamento)

## SEO

- Título, descrição e Open Graph configuráveis por página e globalmente
  (via admin)
- `sitemap.xml` e `robots.txt` dinâmicos — sorteios concluídos entram
  automaticamente no sitemap

## Estrutura do que está incluído

- `app/` — Models, Controllers, Middleware, Jobs, Services
- `database/migrations` + `database/seeders/AdminSeeder.php`
- `resources/views` — todas as telas, já estilizadas
- `resources/js/app.js` — confete (canvas-confetti) e gráfico (Chart.js)
- `public/build/` — CSS/JS **já compilados** e versionados no git
- `.github/workflows/build-assets.yml` — rebuilda os assets automaticamente
  a cada push que altera `resources/`
- `deploy/` — Nginx, Supervisor, `.env` de exemplo, `install.sh`, `deploy.sh`
- `DEPLOY-AAPANEL.md` — guia completo passo a passo

⚠️ **Uma coisa não veio pronta:** meu ambiente não tem acesso ao Packagist,
só ao npm — então a pasta `vendor/` do Laravel não está incluída/versionada.
O `deploy/install.sh` roda o `composer install` por você.

⚠️ **A fila precisa estar rodando** (Supervisor, já configurado no guia de
deploy) — sem isso, sorteios ficam presos em "buscando comentários" pra
sempre. Configure o `MAIL_*` no `.env` também, pra 2FA do admin funcionar
por e-mail de verdade (senão o código só aparece no log).

## Fora do escopo por enquanto

Duas sugestões da lista original não entraram porque pedem infraestrutura
própria, maior que o resto do sistema:
- **Vídeo de reveal para Stories** — exige geração de vídeo (ffmpeg ou
  serviço externo), tratamento de codecs, fila dedicada
- **Blog/CMS de conteúdo para SEO** — merece um editor de posts próprio,
  não só uma tabela a mais

Posso montar qualquer um dos dois numa próxima rodada, se fizer sentido.

## O que falta implementar (pontos com TODO no código)

- `FetchGiveawayComments::handle()` — chamar de fato a Instagram Graph API
  (`GET /{media-id}/comments`), com paginação, usando o token salvo em
  `InstagramToken`. Formato esperado por comentário:
  `['username', 'text', 'mentions', 'is_follower']`
- Tela de "resetar senha" e verificação de e-mail
- Job agendado para renovar tokens do Instagram antes de expirar
- App Review da Meta, antes de liberar a conexão de Instagram pra qualquer
  cliente sem cadastro manual como tester
