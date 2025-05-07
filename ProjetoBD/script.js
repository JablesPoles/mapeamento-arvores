const treeData = [
  {
    category: "Figueira",
    trees: [
      {
        name: "Figueira gigante",
        descricao: "Árvore nativa com copa ampla que fornece sombra abundante na Vila Barth.",
        image: "https://images.pexels.com/photos/326055/pexels-photo-326055.jpeg?auto=compress&cs=tinysrgb&h=160",
        localizacao: "Praça Central",
        tipoVegetacao: "Vegetação nativa",
        numeroEspecie: "FG-001",
        numeroIndividuos: 3,
        especie: "Ficus macrophylla",
        DAP: "60-120 cm",
        estadoFitossanitario: "Bom",
        estadoFisicoTronco: "Sem danos aparentes",
        estadoFisicoCopa: "Densa e saudável",
        tamanhoCalcada: "Grande",
        acessibilidade: "Boa, calçada ampla",
        espacoArvore: "Espaço adequado",
        raizes: "Raízes superficiais controladas"
      },
      {
        name: "Figueira branca",
        descricao: "Espécie de menor porte, comum em jardins comunitários da Vila.",
        image: "https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg?auto=compress&cs=tinysrgb&h=160",
        localizacao: "Jardim Comunitário A",
        tipoVegetacao: "Vegetação nativa",
        numeroEspecie: "FB-002",
        numeroIndividuos: 5,
        especie: "Ficus benjamina",
        DAP: "15-40 cm",
        estadoFitossanitario: "Regular",
        estadoFisicoTronco: "Pequenas fissuras",
        estadoFisicoCopa: "Folhagem sutilmente rala",
        tamanhoCalcada: "Média",
        acessibilidade: "Adequada",
        espacoArvore: "Espaço limitado",
        raizes: "Raízes pouco invasivas"
      }
    ]
  },
  {
    category: "Ipê",
    trees: [
      {
        name: "Ipê amarelo",
        descricao: "Conhecido pela florada amarela vibrante no início da primavera.",
        image: "https://images.pexels.com/photos/979247/pexels-photo-979247.jpeg?auto=compress&cs=tinysrgb&h=160",
        localizacao: "Rua das Flores",
        tipoVegetacao: "Vegetação ornamental",
        numeroEspecie: "IA-003",
        numeroIndividuos: 7,
        especie: "Tabebuia chrysotricha",
        DAP: "45-70 cm",
        estadoFitossanitario: "Bom",
        estadoFisicoTronco: "Sem danos",
        estadoFisicoCopa: "Flores abundantes",
        tamanhoCalcada: "Grande",
        acessibilidade: "Boa",
        espacoArvore: "Espaço amplo",
        raizes: "Raízes profundas"
      },
      {
        name: "Ipê roxo",
        descricao: "Árvore ornamental muito apreciada nas ruas da Vila Barth.",
        image: "https://images.pexels.com/photos/814286/pexels-photo-814286.jpeg?auto=compress&cs=tinysrgb&h=160",
        localizacao: "Av. Principal",
        tipoVegetacao: "Vegetação ornamental",
        numeroEspecie: "IR-004",
        numeroIndividuos: 4,
        especie: "Handroanthus impetiginosus",
        DAP: "35-60 cm",
        estadoFitossanitario: "Regular",
        estadoFisicoTronco: "Pequenos cortes",
        estadoFisicoCopa: "Folhagem saudável",
        tamanhoCalcada: "Média",
        acessibilidade: "Adequada",
        espacoArvore: "Espaço limitado",
        raizes: "Raízes superficiais"
      }
    ]
  },
  {
    category: "Jacarandá",
    trees: [
      {
        name: "Jacarandá-mimoso",
        descricao: "Árvore com flores lilases que atraem polinizadores.",
        image: "https://images.pexels.com/photos/462354/pexels-photo-462354.jpeg?auto=compress&cs=tinysrgb&h=160",
        localizacao: "Praça do Jardim",
        tipoVegetacao: "Vegetação nativa",
        numeroEspecie: "JM-005",
        numeroIndividuos: 2,
        especie: "Jacaranda mimosifolia",
        DAP: "40-75 cm",
        estadoFitossanitario: "Bom",
        estadoFisicoTronco: "Saudável",
        estadoFisicoCopa: "Folhagem densa",
        tamanhoCalcada: "Grande",
        acessibilidade: "Boa",
        espacoArvore: "Espaço amplo",
        raizes: "Raízes vigorosas"
      }
    ]
  },
  {
    category: "Pau-brasil",
    trees: [
      {
        name: "Pau-brasil",
        descricao: "Árvore histórica e símbolo nacional, registrada em áreas de preservação.",
        image: "https://images.pexels.com/photos/34950/pexels-photo.jpg?auto=compress&cs=tinysrgb&h=160",
        localizacao: "Área de Preservação",
        tipoVegetacao: "Vegetação nativa",
        numeroEspecie: "PB-006",
        numeroIndividuos: 1,
        especie: "Paubrasilia echinata",
        DAP: "90-110 cm",
        estadoFitossanitario: "Bom",
        estadoFisicoTronco: "Robusto e íntegro",
        estadoFisicoCopa: "Copa ampla e saudável",
        tamanhoCalcada: "Grande",
        acessibilidade: "Limitada para pedestres",
        espacoArvore: "Espaço reservado para preservação",
        raizes: "Raízes profundas e extensas"
      }
    ]
  }
];

const categoriesNav = document.getElementById('categories-nav');
const treeList = document.getElementById('tree-list');
const searchInput = document.getElementById('tree-search');

// Criar botão categoria
function createCategoryButton(category, isActive = false) {
  const btn = document.createElement('button');
  btn.textContent = category;
  btn.className = isActive ? 'active' : '';
  btn.setAttribute('aria-pressed', isActive.toString());
  btn.addEventListener('click', () => {
    selectCategory(category);
    clearSearch();
  });
  return btn;
}

// Mostrar apenas um card por vez - o primeiro da lista passada
function showSingleTree(tree) {
  treeList.innerHTML = '';

  if (!tree) {
    treeList.innerHTML = '<p>Nenhuma árvore encontrada.</p>';
    return;
  }

  const card = document.createElement('article');
  card.className = 'tree-card';
  card.tabIndex = 0;

  const detailsHtml = `
    <dl>
      <dt>Localização:</dt><dd>${tree.localizacao}</dd>
      <dt>Tipo de vegetação:</dt><dd>${tree.tipoVegetacao}</dd>
      <dt>Número de espécie:</dt><dd>${tree.numeroEspecie}</dd>
      <dt>Número de indivíduos:</dt><dd>${tree.numeroIndividuos}</dd>
      <dt>Espécie:</dt><dd>${tree.especie}</dd>
      <dt>Classe de DAP:</dt><dd>${tree.DAP}</dd>
      <dt>Estado fitossanitário:</dt><dd>${tree.estadoFitossanitario}</dd>
      <dt>Estado físico do tronco:</dt><dd>${tree.estadoFisicoTronco}</dd>
      <dt>Estado físico da copa:</dt><dd>${tree.estadoFisicoCopa}</dd>
      <dt>Tamanho da calçada:</dt><dd>${tree.tamanhoCalcada}</dd>
      <dt>Acessibilidade:</dt><dd>${tree.acessibilidade}</dd>
      <dt>Espaço árvore:</dt><dd>${tree.espacoArvore}</dd>
      <dt>Raízes:</dt><dd>${tree.raizes}</dd>
    </dl>
  `;

  card.innerHTML = `
    <img src="${tree.image}" alt="Imagem da árvore ${tree.name}" loading="lazy" />
    <div class="tree-info">
      <h3>${tree.name}</h3>
      <p>${tree.descricao}</p>
      ${detailsHtml}
    </div>
  `;
  treeList.appendChild(card);
}

// Mostrar árvore única da categoria (primeira da lista)
function showTreeByCategory(category) {
  const cat = treeData.find(c => c.category === category);
  if (!cat || cat.trees.length === 0) {
    showSingleTree(null);
    return;
  }
  showSingleTree(cat.trees[0]);
}

// Atualizar seleção categoria
function selectCategory(category) {
  Array.from(categoriesNav.children).forEach(btn => {
    if (btn.textContent === category) {
      btn.classList.add('active');
      btn.setAttribute('aria-pressed', 'true');
    } else {
      btn.classList.remove('active');
      btn.setAttribute('aria-pressed', 'false');
    }
  });
  showTreeByCategory(category);
}

// Limpar pesquisa
function clearSearch() {
  searchInput.value = '';
}

// Pesquisa por nome - retorna primeira árvore que bate com filtro
function searchTreeByName(query) {
  const q = query.trim().toLowerCase();
  if (!q) {
    // Se pesquisa vazia, mostrar categoria ativa
    const activeBtn = categoriesNav.querySelector('button.active');
    if (activeBtn) {
      showTreeByCategory(activeBtn.textContent);
    } else if(treeData.length > 0) {
      selectCategory(treeData[0].category);
    }
    return;
  }
  const allTrees = treeData.flatMap(cat => cat.trees);
  const filtered = allTrees.filter(tree =>
    tree.name.toLowerCase().includes(q)
  );
  if (filtered.length === 0) {
    treeList.innerHTML = '<p>Nenhuma árvore encontrada para o termo pesquisado.</p>';
    // Remove seleção categoria
    Array.from(categoriesNav.children).forEach(btn => {
      btn.classList.remove('active');
      btn.setAttribute('aria-pressed', 'false');
    });
    return;
  }
  showSingleTree(filtered[0]);
  // Desmarcar categorias pois resultado é global
  Array.from(categoriesNav.children).forEach(btn => {
    btn.classList.remove('active');
    btn.setAttribute('aria-pressed', 'false');
  });
}

function init() {
  treeData.forEach((cat, idx) => {
    categoriesNav.appendChild(createCategoryButton(cat.category, idx === 0));
  });
  if (treeData.length > 0) {
    showTreeByCategory(treeData[0].category);
  }
  searchInput.addEventListener('input', e => {
    searchTreeByName(e.target.value);
  });
}

init();