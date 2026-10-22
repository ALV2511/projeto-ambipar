# Ambipar

Site estático com endpoints PHP de cadastro, login e sessão de usuário.

## Estrutura

- `public/`: raiz pública do servidor; páginas, API e assets.
- `public/config/`: conexão PDO e configuração compartilhada de sessão, carregadas pelas APIs usando `DOCUMENT_ROOT`.
- `database/schema.sql`: estrutura mínima esperada pela autenticação.
- `database/migrate_gamification.sql`: tabela de dias ativos para instalações existentes.
- `.env`: configuração local do banco (não versionar).
- `archive/`: cópia compactada do projeto legado, mantida como referência.
- `design/`: arquivo-fonte de design original.

## Configuração local

1. Instale PHP 8 ou superior com as extensões `pdo` e `pdo_mysql`, além de MySQL/MariaDB.
2. Copie `.env.example` para `.env` na raiz do projeto e preencha `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS`. A conexão também lê a chave legada `DB__NAME`; prefira padronizá-la como `DB_NAME`. `DB_PASS` pode ficar vazio somente se o usuário MySQL realmente não tiver senha. A conexão procura o `.env` um nível acima do document root e, como alternativa, dentro dele.
3. Crie o banco informado em `DB_NAME` e execute `database/schema.sql`. Se a tabela `usuarios` já existir, confirme que ela tem `id`, `username`, `passwords` e `email`, com `email` único e `passwords` dimensionado para hashes (`VARCHAR(255)`); nesse caso, execute também `database/migrate_gamification.sql` para criar o registro de dias ativos.
4. Na raiz do projeto, inicie o servidor apontando para a pasta pública:

   ```powershell
   php -S 127.0.0.1:8000 -t public
   ```

5. Abra `http://127.0.0.1:8000`. Para produção, configure o servidor web com `public/` como document root, HTTPS e `display_errors=Off`.

O cadastro, login e dados do painel carregam `config/database.php` e `config/bootstrap.php` a partir de `$_SERVER['DOCUMENT_ROOT']`; configure o servidor para que `public/` seja o document root ou publique seu conteúdo diretamente no document root. O `.htaccess` em `public/config/` impede acesso HTTP direto aos arquivos de configuração em servidores Apache. O cadastro e o login usam statements preparados e os campos `passwords` armazenam hashes gerados por `password_hash`. A página do painel é HTML estático e obtém seus dados de `public/api/painel_dados.php` em JSON. Os níveis somam 20 XP pela criação da conta e 10 XP por dia diferente de acesso; insígnias são liberadas por marcos de acesso. A página de contato continua informativa até que um canal real seja configurado.
