# 🎯 SimplePractice - Projeto sobre Procrastinação

Projeto acadêmico desenvolvido para a disciplina de Desenvolvimento Web com foco em conscientização e combate à procrastinação através de uma experiência web interativa.

O site apresenta conteúdos educativos sobre causas da procrastinação, estratégias de solução e incentivo à ação prática, utilizando apenas tecnologias web nativas, sem frameworks.

---

# 📚 Objetivo do Projeto

O principal objetivo foi desenvolver um site funcional, organizado e responsivo utilizando:

- HTML5 semântico
- CSS3 puro
- JavaScript Vanilla
- PHP puro
- Banco de dados MySQL/MariaDB
- Estrutura modular de arquivos
- Navegação dinâmica entre páginas

Além disso, o projeto também buscou aplicar conceitos de:

- organização visual
- experiência do usuário
- responsividade
- manipulação do DOM
- integração entre páginas
- versionamento com Git e GitHub

---

# 🖥️ Funcionalidades

O projeto possui:

✅ Navegação entre páginas  
✅ Menu hamburger responsivo  
✅ Sumário lateral dinâmico  
✅ Barra de progresso de leitura  
✅ Scroll suave entre seções  
✅ Interações em JavaScript  
✅ Registro de compromissos com PHP
✅ Consulta de dados salvos no MySQL/MariaDB
✅ Estrutura organizada por páginas  
✅ Conteúdo educativo e motivacional  

---

# 🗄️ Como rodar com PHP e banco de dados

1. Crie o banco importando o arquivo `database.sql` no MySQL/MariaDB.
2. Confira usuário e senha em `api/config.php`.
3. Rode o projeto com servidor PHP:

```bash
php -S localhost:8080
```

4. Acesse:

```bash
http://localhost:8080
```

O formulário da página `Ação` grava a tarefa na tabela `compromissos`.
A página `Public/pages/compromissos.php` lista os registros salvos.

---

# 📂 Estrutura do Projeto

```bash
SIMPLEPRACTICE/
├── .vscode/
│   └── launch.json
├── Public/
│   ├── assets/
│   │   ├── CSS/
│   │   │   └── style.css
│   │   ├── data/
│   │   │   └── site.json
│   │   └── js/
│   │       └── script.js
│   └── pages/
│       ├── tela1.html
│       ├── tela2.html
│       ├── tela3.html
│       └── compromissos.php
├── api/
│   ├── compromissos.php
│   ├── config.php
│   └── database.php
├── .gitignore
├── database.sql
├── index.html
├── LICENSE
└── README.md
