const DYNAMIC_INPUT_CLASSES =
  "flex-grow px-4 py-2.5 border border-gray-300 dark:border-dark-input-border dark:bg-dark-input-bg dark:text-dark-text rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-dark-input-focus-ring focus:border-primary-light dark:focus:border-dark-input-focus-ring transition-shadow";
const DYNAMIC_REMOVE_BUTTON_CLASSES =
  "bg-red-600 hover:bg-red-700 dark:bg-dark-remove-btn-bg dark:hover:bg-dark-remove-btn-hover-bg text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-300 ease-in-out transform hover:scale-105";

function initializeThemeToggle() {
  const themeToggleButton = document.getElementById("theme-toggle-button");
  const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
  const themeToggleLightIcon = document.getElementById(
    "theme-toggle-light-icon"
  );

  function applyThemePreference(theme) {
    if (theme === "dark") {
      document.documentElement.classList.add("dark");
      if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove("hidden");
      if (themeToggleLightIcon) themeToggleLightIcon.classList.add("hidden");
    } else {
      document.documentElement.classList.remove("dark");
      if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add("hidden");
      if (themeToggleLightIcon) themeToggleLightIcon.classList.remove("hidden");
    }
  }

  function initializeCurrentTheme() {
    const userPreference = localStorage.getItem("theme");
    const systemPrefersDark = window.matchMedia(
      "(prefers-color-scheme: dark)"
    ).matches;

    if (userPreference === "dark" || (!userPreference && systemPrefersDark)) {
      applyThemePreference("dark");
    } else {
      applyThemePreference("light");
    }
  }

  if (themeToggleButton) {
    themeToggleButton.addEventListener("click", () => {
      const isDarkMode = document.documentElement.classList.toggle("dark");
      localStorage.setItem("theme", isDarkMode ? "dark" : "light");
      applyThemePreference(isDarkMode ? "dark" : "light");
    });
  }

  initializeCurrentTheme();
}

function adicionarCampoNomePopular() {
  const container = document.getElementById("nomes-populares-container");
  if (!container) {
    console.warn(
      "Container 'nomes-populares-container' não encontrado para adicionar campo dinâmico."
    );
    return;
  }

  const wrapper = document.createElement("div");
  wrapper.className = "flex items-start gap-2 mt-2 popular-name-group";

  const input = document.createElement("input");
  input.type = "text";
  input.name = "nome_p[]";
  input.placeholder = "Outro nome popular";
  input.maxLength = 100;
  input.className = DYNAMIC_INPUT_CLASSES;

  const removeButton = document.createElement("button");
  removeButton.type = "button";
  removeButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
  removeButton.className =
    DYNAMIC_REMOVE_BUTTON_CLASSES + " remove-nome-popular-btn";
  removeButton.setAttribute("aria-label", "Remover nome popular");
  removeButton.onclick = function () {
    container.removeChild(wrapper);
    const currentFields = container.querySelectorAll(".popular-name-group");
    if (currentFields.length === 1) {
      const button = currentFields[0].querySelector(".remove-nome-popular-btn");
      if (button) {
        button.remove();
      }
    }
  };

  wrapper.appendChild(input);
  if (container.children.length > 0) {
    wrapper.appendChild(removeButton);
  }

  container.appendChild(wrapper);

  const allGroups = container.querySelectorAll(".popular-name-group");
  if (allGroups.length > 1) {
    allGroups.forEach((group) => {
      if (!group.querySelector(".remove-nome-popular-btn")) {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.innerHTML = '<i class="fas fa-trash-alt"></i>';
        btn.className =
          DYNAMIC_REMOVE_BUTTON_CLASSES + " remove-nome-popular-btn";
        btn.setAttribute("aria-label", "Remover nome popular");
        btn.onclick = function () {
          container.removeChild(group);
          const currentFields = container.querySelectorAll(
            ".popular-name-group"
          );
          if (currentFields.length === 1) {
            const button = currentFields[0].querySelector(
              ".remove-nome-popular-btn"
            );
            if (button) {
              button.remove();
            }
          }
        };
        group.appendChild(btn);
      }
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  initializeThemeToggle();

  const addNomePopularButton = document.getElementById("add-nome-popular-btn");
  if (addNomePopularButton) {
    addNomePopularButton.addEventListener("click", adicionarCampoNomePopular);
  } else {
    console.warn("Botão 'add-nome-popular-btn' não encontrado.");
  }

  const initialNameFields = document.querySelectorAll(
    "#nomes-populares-container .popular-name-group"
  );
  if (initialNameFields.length > 1) {
    initialNameFields.forEach((group) => {
      if (!group.querySelector(".remove-nome-popular-btn")) {
        const container = document.getElementById("nomes-populares-container");
        const btn = document.createElement("button");
        btn.type = "button";
        btn.innerHTML = '<i class="fas fa-trash-alt"></i>';
        btn.className =
          DYNAMIC_REMOVE_BUTTON_CLASSES + " remove-nome-popular-btn";
        btn.setAttribute("aria-label", "Remover nome popular");
        btn.onclick = function () {
          container.removeChild(group);
          const currentFields = container.querySelectorAll(
            ".popular-name-group"
          );
          if (currentFields.length === 1) {
            const button = currentFields[0].querySelector(
              ".remove-nome-popular-btn"
            );
            if (button) {
              button.remove();
            }
          }
        };
        group.appendChild(btn);
      }
    });
  } else if (initialNameFields.length === 1) {
    const button = initialNameFields[0].querySelector(
      ".remove-nome-popular-btn"
    );
    if (button) {
      button.remove();
    }
  }

  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 800,
      easing: "ease-in-out",
      once: true,
    });
  } else {
    console.warn(
      "AOS (Animate On Scroll) não está definido. Verifique se a biblioteca foi carregada corretamente."
    );
  }
});
