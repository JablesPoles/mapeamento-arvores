# Catálogo de Árvores - Fatec Itapetininga

## 🌳 Introdução

Bem-vindo ao projeto **Catálogo de Árvores**! Esta é uma iniciativa acadêmica desenvolvida por estudantes da Fatec Itapetininga, com o objetivo primordial de **catalogar as espécies arbóreas** encontradas em nosso ambiente urbano e regional. O projeto visa criar um repositório digital abrangente para documentar, partilhar conhecimento, promover a educação ambiental e a preservação da flora local.

Cada registo no catálogo é detalhado, incluindo:
* Informação científica (espécie).
* Nome popular principal e nomes populares adicionais.
* Características físicas e ambientais (diâmetro, estado fitossanitário, tipo de vegetação, etc.).
* Coordenadas geográficas (latitude e longitude).
* Fotografias ilustrativas de diferentes partes da árvore (folha, flor, fruto, casca, hábito), obtidas dinamicamente da API PlantNet e otimizadas com um sistema de cache local.
* Curiosidades e informações relevantes sobre a espécie.

O sistema também conta com um painel administrativo robusto para o gerenciamento completo dos dados do catálogo e dos usuários administradores.

## 🎥 Demonstração

![Demonstração da Aplicação](src/demo/working.gif)


## ✨ Funcionalidades Detalhadas

O projeto é dividido em duas áreas principais: a interface pública do catálogo e o painel de administração.

### Interface Pública (`index.php`, `catalogo.php`)

* **Página Inicial (`index.php`):**
    * Apresentação do projeto, seus objetivos e equipe.
    * Seções informativas sobre a importância da arborização.
    * Galeria de imagens introdutória com navegação (Swiper.js).
    * Informações de contato da Fatec Itapetininga.
* **Catálogo de Árvores (`catalogo.php`):**
    * **Listagem Paginada:** Exibição das árvores cadastradas em "cards" individuais, com paginação para fácil navegação.
    * **Busca Avançada:**
        * Por nome científico (campo `especie`).
        * Por nome popular principal (campo `nome_c`).
        * Por nomes populares adicionais (associados na tabela `NOMES_POPULARES`).
        * Por ID da árvore (ex: busca por `#123` ou `123`).
        * O termo de busca é mantido nos links de paginação para uma experiência de usuário fluida.
    * **Detalhes Expansíveis por Árvore (`templates/tree_card.php`):**
        * Ao clicar em "Expandir", são reveladas informações completas:
            * ID da árvore e data/hora do cadastro/atualização.
            * Nome científico e todos os nomes populares.
            * Classificação (nativa/exótica), localização descritiva, tipo de vegetação.
            * Latitude e Longitude.
            * Características físicas: diâmetro do peito, estados fitossanitário, do tronco e da copa.
            * Características do ambiente: tamanho da calçada, espaço para a árvore, tipo de raízes, acessibilidade.
            * Curiosidades sobre a espécie.
        * **Galeria de Imagens Específica:** Cada card expandido possui sua própria galeria (Swiper.js) com imagens da API PlantNet (folha, flor, fruto, casca, hábito), com placeholders caso a imagem não seja encontrada.
        * Links de ação para administradores (Alterar, Deletar) visíveis apenas se o admin estiver logado.
* **Integração com API PlantNet (`src/api_functions.php`, `src/db_functions.php`):**
    * A função `buscarImagensPlantNet()` consulta a API PlantNet usando o nome científico da árvore.
    * As URLs das imagens retornadas são armazenadas em cache nas tabelas `LINKS` e `CATEGORIA_LINKS` (`salvarImagensCache()`).
    * Antes de consultar a API, o sistema verifica o cache local (`buscarImagensCache()`).
* **Design Responsivo e Temas:**
    * Interface totalmente responsiva utilizando Tailwind CSS.
    * Alternador de tema (Dark/Light Mode) com persistência da escolha do usuário via `localStorage` (`assets/js/style.js`).
    * Animações sutis ao rolar a página (AOS - Animate On Scroll).

### Painel Administrativo (`admin/`)

* **Acesso Seguro (`admin/login_admin.php`, `admin/logout_admin.php`):**
    * Login com usuário e senha. As senhas são armazenadas com hash seguro (bcrypt).
    * Sessões PHP para gerenciar o estado de login do administrador.
    * Redirecionamento para a página de login se o acesso a páginas restritas for tentado sem autenticação.
* **Gerenciamento de Árvores (`admin/admin.php`):**
    * **Formulário Unificado para Cadastro e Edição:**
        * Campos para todas as informações da árvore, incluindo nome científico, nome popular principal, nativa/exótica, data/hora (automática ou manual), localização descritiva, vegetação, características físicas, ambientais, latitude, longitude e curiosidades.
        * **Gerenciamento Dinâmico de Nomes Populares Adicionais:** Interface para adicionar ou remover múltiplos nomes populares para uma mesma árvore.
        * Validação de campos no backend.
    * **Funcionalidades CRUD:**
        * **C**riar (Cadastrar): Insere uma nova árvore no banco de dados, incluindo a associação dos nomes populares.
        * **R**ecuperar (Carregar para Edição): Ao acessar `admin.php?carregar_id=ID_DA_ARVORE`, o formulário é pré-preenchido com os dados da árvore selecionada.
        * **U**pdate (Atualizar): Salva as modificações feitas em uma árvore existente.
        * **D**elete:
            * A partir do `tree_card.php` no catálogo (se admin logado): remove a árvore e seus dados associados (nomes populares, links de imagens em cache) com mensagem de confirmação. O redirecionamento mantém o filtro de busca ativo no catálogo.
    * Mensagens de feedback (sucesso/erro) para o usuário.
* **Gerenciamento de Administradores (`admin/gerenciar_usuarios_admin.php`, `admin/processa_usuario_admin.php`):**
    * **Listagem de Administradores:** Exibe todos os usuários administradores cadastrados, com suas informações (ID, usuário, nome completo, data de criação).
    * **Formulário para Criar e Editar Administradores:**
        * Campos para nome de usuário, nome completo, senha e confirmação de senha.
    * **Funcionalidades CRUD:**
        * **C**riar: Adiciona um novo administrador.
        * **R**ecuperar (Carregar para Edição): Permite editar os dados de um administrador existente.
        * **U**pdate: Salva alterações no nome de usuário e nome completo. Permite definir uma nova senha.
        * **D**elete: Remove um administrador, com as seguintes restrições:
            * O usuário 'admin' principal não pode ser excluído.
            * Um administrador não pode excluir a si próprio.
    * Mensagens de feedback.
* **Navegação:** Links para voltar ao catálogo ou para outras seções do painel administrativo.

## 🛠️ Tecnologias Utilizadas

* **Backend:**
    * PHP 8.0+ (com uso de sessões, funções para interação com BD, manipulação de formulários).
    * PDO (PHP Data Objects) para interação segura com o banco de dados PostgreSQL.
* **Banco de Dados:**
    * PostgreSQL.
    * Estrutura relacional com tabelas para árvores, nomes populares, categorias de imagens, links de imagens e administradores.
* **Frontend:**
    * HTML5 (semântico).
    * CSS3.
    * JavaScript (ES6+) para interatividade no cliente (manipulação de DOM, eventos, tema dark/light, adição dinâmica de campos).
* **Frameworks e Bibliotecas:**
    * [Tailwind CSS](https://tailwindcss.com/): Framework CSS utility-first para design responsivo e estilização ágil.
    * [Swiper.js](https://swiperjs.com/): Para carrosséis de imagens interativos e responsivos.
    * [AOS (Animate On Scroll)](https://michalsnik.github.io/aos/): Para animações de elementos ao rolar a página.
    * [Font Awesome](https://fontawesome.com/): Biblioteca de ícones vetoriais.
* **API Externa:**
    * [PlantNet API](https://my.plantnet.org/usage/api): Utilizada para obter imagens das espécies de árvores com base no nome científico.

## 📂 Estrutura do Projeto

```
catalogo_de_arvores/
├── admin/                    # Scripts e interface da área administrativa
│   ├── admin.php             # Painel principal para CRUD de árvores
│   ├── gerenciar_usuarios_admin.php # Interface para CRUD de administradores
│   ├── login_admin.php       # Página de login do painel administrativo
│   ├── logout_admin.php      # Script para finalizar a sessão do administrador
│   └── processa_usuario_admin.php # Backend para processar ações de CRUD de administradores
├── assets/                   # Recursos estáticos do frontend
│   ├── css/
│   │   ├── custom_styles.css # Estilos CSS personalizados adicionais
│   │   └── index_styles.css  # Estilos específicos para a página inicial
│   ├── js/
│   │   ├── catalogo_scripts.js # JavaScript para interatividade na página do catálogo (expansão de cards, etc.)
│   │   ├── index.js          # JavaScript específico para a página inicial (ex: galeria principal)
│   │   └── style.js          # JavaScript geral (controle de tema, manipulação de DOM dinâmica no admin)
│   └── images/               # (Diretório sugerido para imagens estáticas do projeto, como logos)
├── config/                   # Configurações, scripts de banco de dados e dados iniciais
│   ├── data/
│   │   └── dados_arvore.csv  # Arquivo CSV com dados de árvores para importação inicial
│   ├── import_arvores.php    # Script PHP para importar dados do CSV para o banco
│   ├── ScriptAdmin.sql       # Script SQL para criar a tabela 'administradores' e um admin padrão
│   └── ScriptBD.sql          # Script SQL principal para criar a estrutura de tabelas do catálogo
├── src/                      # Funções PHP reutilizáveis (biblioteca do projeto)
│   ├── api_functions.php     # Funções para interagir com a API PlantNet
│   └── db_functions.php      # Funções para todas as operações de banco de dados (CRUDs, buscas)
├── templates/                # Componentes de interface PHP reutilizáveis (parciais)
│   ├── footer.php            # Rodapé padrão das páginas
│   ├── header.php            # Cabeçalho padrão das páginas (inclui menu, metatags, links CSS/JS)
│   ├── pagination.php        # Componente para a navegação de páginas no catálogo
│   └── tree_card.php         # Template para exibir cada árvore no catálogo
├── conexao.php               # Script PHP para estabelecer a conexão com o banco de dados PostgreSQL
├── catalogo.php              # Página pública para visualização e busca no catálogo de árvores
├── index.php                 # Página inicial (landing page) do projeto
├── favicon.ico               # Ícone do site exibido na aba do navegador
└── README.md                 # Este arquivo de documentação
```

## ⚙️ Configuração do Ambiente de Desenvolvimento

**Pré-requisitos Essenciais:**

* **Servidor Web:** Um servidor web com suporte a PHP, como Apache ou Nginx (comumente encontrados em pacotes como XAMPP, WAMP, MAMP ou configurações Docker).
* **PHP:** Versão 8.0 ou superior.
    * **Extensão PDO PostgreSQL (`pdo_pgsql`):** Absolutamente necessária para a comunicação entre PHP e o banco de dados PostgreSQL. Verifique se está habilitada no seu arquivo `php.ini`. O script `conexao.php` tenta verificar sua existência.
    * **Extensão cURL (`curl`):** Essencial para que o PHP possa fazer requisições HTTP à API externa da PlantNet. Verifique se está habilitada no seu `php.ini`.
* **PostgreSQL:** Servidor de banco de dados PostgreSQL instalado e em execução.
* **Acesso à Internet:** Necessário para que a funcionalidade de busca de imagens na API PlantNet funcione corretamente.

**Passos para Instalação:**

1.  **Clone o Repositório:**
    Obtenha os arquivos do projeto. Se estiver usando Git:
    ```bash
    git clone [https://github.com/JablesPoles/mapeamento-arvores.git](https://github.com/JablesPoles/mapeamento-arvores.git)
    cd catalogo_de_arvores 
    ```
    (Ajuste o URL se o seu repositório for diferente. Assumindo que o projeto ficará em uma pasta chamada `catalogo_de_arvores`).

2.  **Configure o Banco de Dados PostgreSQL:**
    * Acesse seu servidor PostgreSQL (ex: via `psql` ou uma ferramenta gráfica como pgAdmin).
    * **Crie o Banco de Dados:** É crucial que o banco de dados seja nomeado exatamente como **`projeto_arvores`**, pois este nome está configurado no arquivo `conexao.php`.
        ```sql
        CREATE DATABASE projeto_arvores;
        ```
    * **Execute os Scripts SQL:** Conecte-se ao banco `projeto_arvores` recém-criado e execute os scripts SQL localizados na pasta `config/` para criar a estrutura de tabelas e dados iniciais. **A ordem de execução é importante:**
        1.  `config/ScriptBD.sql`: Cria as tabelas principais do catálogo (`ARVORE`, `NOMES_POPULARES`, `CATEGORIA`, `LINKS`, etc.) e suas relações.
        2.  `config/ScriptAdmin.sql`: Cria a tabela `administradores` e insere um usuário administrador padrão (`admin` / `admin123`).
        Exemplo de execução via `psql` (substitua `seu_usuario_postgres` pelo seu usuário do PostgreSQL):
        ```bash
        psql -U seu_usuario_postgres -d projeto_arvores -f config/ScriptBD.sql
        psql -U seu_usuario_postgres -d projeto_arvores -f config/ScriptAdmin.sql
        ```

3.  **Configure a Conexão PHP com o Banco (`conexao.php`):**
    * Abra o arquivo `conexao.php` localizado na raiz do projeto (`catalogo_de_arvores/conexao.php`).
    * Verifique e, se necessário, atualize as seguintes variáveis com as suas credenciais de acesso ao PostgreSQL:
        ```php
        $host = 'localhost'; // Geralmente 'localhost' se o PostgreSQL estiver na mesma máquina
        $port = '5432';      // Porta padrão do PostgreSQL
        $dbname = 'projeto_arvores'; // Deve ser mantido como 'projeto_arvores'
        $user = 'postgres';  // Seu nome de usuário do PostgreSQL
        $pass = 'sua_senha'; // A senha do seu usuário do PostgreSQL
        ```

4.  **Verifique as Extensões PHP:**
    * Confirme novamente que as extensões `pdo_pgsql` e `curl` estão ativas e corretamente configuradas no seu `php.ini`. Reinicie seu servidor web após qualquer alteração no `php.ini`.

5.  **Importe os Dados Iniciais do CSV (`config/import_arvores.php`):**
    * O projeto inclui um arquivo de exemplo `config/data/dados_arvore.csv`.
    * **Como funciona:** O script `config/import_arvores.php` foi projetado para ler este arquivo CSV e popular a tabela `ARVORE` no banco de dados. Ele espera colunas específicas no CSV e as mapeia para os campos da tabela.
    * **Execução:**
        * **Via Navegador:** Acesse `http://localhost/catalogo_de_arvores/config/import_arvores.php` (ajuste o URL conforme a configuração do seu servidor web).
        * **Via Linha de Comando (CLI):** Navegue até a pasta `config/` no terminal e execute `php import_arvores.php`.
    * **Pré-requisitos para o CSV:**
        * O arquivo `dados_arvore.csv` deve estar presente em `config/data/`.
        * O script ignora a primeira linha (cabeçalho).
        * Ele espera um formato de data específico (`n/j/Y H:i:s`) na primeira coluna do CSV.
        * O script atualmente mapeia colunas específicas do CSV para os campos da tabela `ARVORE` (ex: `nome_c` da coluna 3, `especie` da coluna 4, etc.). Verifique o script `import_arvores.php` para o mapeamento exato.
        * **Importante:** O script `import_arvores.php` pode precisar de ajustes se a estrutura do seu `dados_arvore.csv` for diferente ou se você quiser importar campos como latitude/longitude e múltiplos nomes populares (atualmente, ele insere um valor fixo para `acessibilidade` e não trata múltiplos nomes populares via CSV).

6.  **Acesse o Projeto:**
    * Certifique-se de que a pasta `catalogo_de_arvores/` está no diretório raiz do seu servidor web (ex: `htdocs/` no XAMPP, `www/` no WAMP).
    * Abra seu navegador e acesse o `index.php` (ex: `http://localhost/catalogo_de_arvores/`).

## 📜 Scripts SQL Detalhados

* **`config/ScriptBD.sql`**:
    * Cria a tabela `ARVORE` com colunas para todas as características da árvore, incluindo `NOME_C` (nome popular principal), `ESPECIE` (nome científico), `NAT_EXO`, `HORARIO`, `LOCALIZACAO`, `VEGETACAO`, `DIAMETRO_PEITO`, estados (fitossanitário, tronco, copa), dados ambientais, curiosidade, e `LATITUDE`, `LONGITUDE`.
    * Cria `NOMES_POPULARES` para armazenar uma lista única de nomes populares.
    * Cria `NOMES_POPULARES_ARVORE` como tabela de junção para o relacionamento N-M entre árvores e nomes populares.
    * Cria `CATEGORIA` para tipos de imagens (folha, flor, etc.).
    * Cria `LINKS` para armazenar URLs de imagens cacheadas da API PlantNet.
    * Cria `CATEGORIA_LINKS` como tabela de junção para associar árvores, categorias de imagem e links de imagem.
    * Define chaves primárias, estrangeiras e alguns índices para otimização.
* **`config/ScriptAdmin.sql`**:
    * Cria a tabela `administradores` com campos `id`, `usuario` (único), `senha` (para hash bcrypt), `nome_completo`, `data_criacao`.
    * Insere um usuário administrador padrão:
        * Usuário: `admin`
        * Senha: `admin123` (o hash `$2y$10$7Qi0/ZNnK5pUvyfuXC5rNOKdHZkgwceWdBj8Bj9/GR3YpqNw0OMTS` corresponde a esta senha).

## 🔑 Administração do Sistema

* **Acesso ao Painel:** Navegue até `admin/login_admin.php` no seu navegador (ex: `http://localhost/catalogo_de_arvores/admin/login_admin.php`).
* **Credenciais Padrão:**
    * Usuário: `admin`
    * Senha: `admin123`
    * **Recomendação Forte:** Altere a senha do usuário `admin` e crie seus próprios usuários administradores através do painel "Gerenciar Administradores" após o primeiro login, por questões de segurança.

## 🌐 API Externa (PlantNet)

* O sistema busca imagens de espécies na API PlantNet (`https://api.plantnet.org/`).
* A busca é feita prioritariamente pelo nome científico (`especie`) da árvore.
* As URLs das imagens encontradas são armazenadas localmente (cache) para reduzir chamadas futuras à API e acelerar o carregamento das páginas do catálogo.
* Tipos de imagens buscados: `fruit` (fruto), `leaf` (folha), `bark` (casca), `habit` (hábito/porte), `flower` (flor).

## 🚀 Próximos Passos e Melhorias Futuras

* **Integração com API de Mapas:**
    * Utilizar os dados de latitude e longitude já coletados para exibir as árvores em um mapa interativo (ex: usando Leaflet.js, OpenLayers, ou Google Maps API).
    * Permitir filtros e buscas baseadas na localização no mapa.
* **Gerador de QR Code:**
    * Implementar uma funcionalidade que gere um QR Code para cada árvore no catálogo.
    * Este QR Code poderia levar diretamente à página de detalhes da árvore no sistema, facilitando o acesso à informação em campo (ex: em placas de identificação de árvores).

## 👥 Nossa Equipe

Este projeto foi desenvolvido com a colaboração dos seguintes estudantes da Fatec Itapetininga:

* Nathanael Netto
* Camilly Santos
* Matheus Poles
* Otavio Augusto
* Enzo Padilha
* Kayke Yuji
* Marciel Silva

## 🤝 Contribuir

Este é um projeto acadêmico. Se você é um estudante da Fatec Itapetininga ou tem interesse em contribuir para a melhoria do Catálogo de Árvores:
1.  Faça um "Fork" do repositório.
2.  Crie uma nova "branch" para suas modificações (`git checkout -b minha-funcionalidade`).
3.  Realize as alterações e faça "commits" (`git commit -m 'Adiciona nova funcionalidade X'`).
4.  Envie para a sua branch no seu fork (`git push origin minha-funcionalidade`).
5.  Abra um "Pull Request" no repositório original para revisão.

Para alterações significativas ou novas ideias, por favor, abra primeiro uma "Issue" para discussão.

## 📜 Licença

Este projeto é um trabalho acadêmico desenvolvido no âmbito da Fatec Itapetininga. Para uso fora do contexto acadêmico ou pessoal, por favor, entre em contato com os autores ou com a instituição para discutir questões de licenciamento.
