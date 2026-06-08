const fallbackSiteConfig = {
    brand: {
        title: "Procrastinação",
        description: "Aprenda técnicas práticas para vencer a procrastinação",
        footer: "SimplePractice · 2026"
    },
    pages: [
        { id: "inicio", label: "Início", path: "index.html" },
        { id: "causas", label: "Causas", path: "Public/pages/tela1.html" },
        { id: "solucoes", label: "Soluções", path: "Public/pages/tela2.html" },
        { id: "acao", label: "Ação", path: "Public/pages/tela3.html" },
        { id: "compromissos", label: "Compromissos", path: "Public/pages/compromissos.php" }
    ]
};

document.addEventListener("DOMContentLoaded", async () => {
    document.body.classList.add("js-enhanced");

    const rootPrefix = document.body.dataset.rootPrefix || "";
    const currentPage = document.body.dataset.page || "";
    const siteConfig = await loadSiteConfig();

    renderDrawer(siteConfig, rootPrefix, currentPage);
    setupMenuToggle();
    buildSummary();
    setupSummaryTracking();
    setupProgressBar();
    setupSectionReveal();
    setupChecklist();
    setupCommitmentForm();
});

async function loadSiteConfig() {
    const configPath = document.body.dataset.config;

    if (!configPath) {
        return fallbackSiteConfig;
    }

    try {
        const response = await fetch(configPath);

        if (!response.ok) {
            throw new Error("Não foi possível carregar o JSON.");
        }

        const loadedConfig = await response.json();

        return {
            ...fallbackSiteConfig,
            ...loadedConfig,
            brand: {
                ...fallbackSiteConfig.brand,
                ...(loadedConfig.brand || {})
            },
            pages: Array.isArray(loadedConfig.pages) ? loadedConfig.pages : fallbackSiteConfig.pages
        };
    } catch (error) {
        return fallbackSiteConfig;
    }
}

function renderDrawer(config, rootPrefix, currentPage) {
    const drawerLinks = document.getElementById("drawerLinks");
    const brandTitle = document.getElementById("brandTitle");
    const brandFooter = document.getElementById("brandFooter");
    const brandDescription = document.querySelector(".page-drawer__description");

    if (brandTitle) {
        brandTitle.textContent = config.brand.title;
    }

    if (brandDescription) {
        brandDescription.textContent = config.brand.description;
    }

    if (brandFooter) {
        brandFooter.textContent = config.brand.footer;
    }

    if (!drawerLinks) {
        return;
    }

    drawerLinks.innerHTML = config.pages.map((page) => {
        const href = `${rootPrefix}${page.path}`;
        const activeClass = page.id === currentPage ? " active" : "";

        return `
            <li>
                <a class="drawer-link${activeClass}" href="${href}">${page.label}</a>
            </li>
        `;
    }).join("");
}

function setupMenuToggle() {
    const menuToggle = document.getElementById("menuToggle");
    const pageDrawer = document.getElementById("pageDrawer");
    const menuOverlay = document.getElementById("menuOverlay");

    if (!menuToggle || !pageDrawer || !menuOverlay) {
        return;
    }

    const setMenuState = (isOpen) => {
        document.body.classList.toggle("menu-open", isOpen);
        pageDrawer.classList.toggle("is-open", isOpen);
        menuToggle.setAttribute("aria-expanded", String(isOpen));
        menuOverlay.hidden = !isOpen;
    };

    menuToggle.addEventListener("click", () => {
        const isOpen = menuToggle.getAttribute("aria-expanded") === "true";
        setMenuState(!isOpen);
    });

    menuOverlay.addEventListener("click", () => setMenuState(false));

    pageDrawer.addEventListener("click", (event) => {
        if (event.target.closest(".drawer-link")) {
            setMenuState(false);
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            setMenuState(false);
        }
    });
}

function buildSummary() {
    const summaryLinks = document.getElementById("summaryLinks");
    const sections = document.querySelectorAll(".content-section[id]");

    if (!summaryLinks || sections.length === 0) {
        return;
    }

    summaryLinks.innerHTML = "";

    sections.forEach((section, index) => {
        const heading = section.querySelector("h2");

        if (!heading) {
            return;
        }

        const link = document.createElement("a");
        link.href = `#${section.id}`;
        link.textContent = section.dataset.summaryLabel || heading.textContent.trim();

        if (index === 0) {
            link.classList.add("active");
        }

        const item = document.createElement("li");
        item.appendChild(link);
        summaryLinks.appendChild(item);
    });
}

function setupSummaryTracking() {
    const sections = Array.from(document.querySelectorAll(".content-section[id]"));
    const summaryAnchors = Array.from(document.querySelectorAll(".summary-links a"));

    if (sections.length === 0 || summaryAnchors.length === 0) {
        return;
    }

    const setActive = (sectionId) => {
        summaryAnchors.forEach((anchor) => {
            anchor.classList.toggle("active", anchor.getAttribute("href") === `#${sectionId}`);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        const visibleEntry = entries
            .filter((entry) => entry.isIntersecting)
            .sort((entryA, entryB) => entryB.intersectionRatio - entryA.intersectionRatio)[0];

        if (visibleEntry) {
            setActive(visibleEntry.target.id);
        }
    }, {
        threshold: [0.2, 0.45, 0.7],
        rootMargin: "-18% 0px -45% 0px"
    });

    sections.forEach((section) => observer.observe(section));
}

function setupProgressBar() {
    const progressBar = document.getElementById("progressBar");

    if (!progressBar) {
        return;
    }

    const updateProgress = () => {
        const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
        const percentage = scrollableHeight > 0 ? (window.scrollY / scrollableHeight) * 100 : 0;
        const boundedValue = Math.min(Math.max(percentage, 0), 100);

        progressBar.style.width = `${boundedValue}%`;
    };

    updateProgress();
    window.addEventListener("scroll", updateProgress, { passive: true });
    window.addEventListener("resize", updateProgress);
}

function setupSectionReveal() {
    const sections = document.querySelectorAll(".content-section");

    if (sections.length === 0) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.16,
        rootMargin: "0px 0px -50px 0px"
    });

    sections.forEach((section) => observer.observe(section));
}

function setupChecklist() {
    const checkboxes = document.querySelectorAll(".checkbox-item input[type='checkbox']");

    checkboxes.forEach((checkbox) => {
        const updateState = () => {
            const label = checkbox.closest(".checkbox-item");

            if (!label) {
                return;
            }

            label.classList.toggle("is-checked", checkbox.checked);
        };

        checkbox.addEventListener("change", updateState);
        updateState();
    });
}

function setupCommitmentForm() {
    const commitButton = document.getElementById("commitBtn");
    const taskInput = document.getElementById("taskInput");
    const rootPrefix = document.body.dataset.rootPrefix || "";

    if (!commitButton || !taskInput) {
        return;
    }

    const submitCommitment = async () => {
        const taskValue = taskInput.value.trim();

        if (taskValue === "") {
            showToast("warning", "Falta sua tarefa", "Escreva uma ação concreta antes de registrar o compromisso.");
            taskInput.focus();
            return;
        }

        const formData = new FormData();
        formData.append("task", taskValue);

        commitButton.disabled = true;
        commitButton.textContent = "Registrando...";

        try {
            const response = await fetch(`${rootPrefix}api/compromissos.php`, {
                method: "POST",
                body: formData
            });
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || "Não foi possível registrar o compromisso.");
            }
            showToast("success", "Compromisso registrado", `Primeiro passo salvo no banco: ${taskValue}.`);
            taskInput.value = "";

            // If on the commitments page, refresh or remove/add items via network
            if (document.querySelector('.commitments-table')) {
                // reload the page to reflect the new record (simpler for server-rendered table)
                window.location.reload();
            }
        } catch (error) {
            showToast("warning", "Erro ao salvar", "Abra o projeto pelo servidor PHP e confira a conexão com o banco.");
        } finally {
            commitButton.disabled = false;
            commitButton.textContent = "Registrar compromisso";
        }
    };

    commitButton.addEventListener("click", submitCommitment);

    taskInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            submitCommitment();
        }
    });
}

// Delegate delete button clicks for commitments table
document.addEventListener('click', async (event) => {
    const btn = event.target.closest('.delete-commitment');

    if (!btn) return;

    const id = btn.dataset.id;

    if (!id || !confirm('Confirmar exclusão deste compromisso?')) {
        return;
    }

    try {
        btn.disabled = true;

        const response = await fetch(`${(document.body.dataset.rootPrefix || '')}api/compromissos.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}`
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Não foi possível excluir.');
        }

        showToast('success', 'Excluído', 'Compromisso removido do banco.');

        // Remove the row from the table
        const row = btn.closest('tr');
        if (row) row.remove();
    } catch (err) {
        showToast('warning', 'Erro', 'Não foi possível excluir o compromisso.');
        btn.disabled = false;
    }
});

// Edit commitment handler
document.addEventListener('click', async (event) => {
    const btn = event.target.closest('.edit-commitment');

    if (!btn) return;

    const id = btn.dataset.id;
    const row = btn.closest('tr');
    const taskCell = row ? row.querySelectorAll('td')[1] : null;
    const current = taskCell ? taskCell.textContent.trim() : '';

    const newTask = prompt('Editar tarefa:', current);

    if (newTask === null) return; // cancel

    const trimmed = newTask.trim();
    if (trimmed === '') {
        showToast('warning', 'Tarefa vazia', 'A tarefa não pode ficar vazia.');
        return;
    }

    try {
        btn.disabled = true;

        const response = await fetch(`${(document.body.dataset.rootPrefix || '')}api/compromissos.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&task=${encodeURIComponent(trimmed)}`
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Não foi possível atualizar.');
        }

        showToast('success', 'Atualizado', 'Compromisso atualizado com sucesso.');

        if (taskCell) taskCell.textContent = trimmed;
    } catch (err) {
        showToast('warning', 'Erro', 'Não foi possível atualizar o compromisso.');
        btn.disabled = false;
    }
});

function showToast(tone, title, message) {
    const toastStack = document.getElementById("toastStack");

    if (!toastStack) {
        return;
    }

    const toast = document.createElement("div");
    toast.className = "toast";
    toast.dataset.tone = tone;
    toast.innerHTML = `<strong>${title}</strong><span>${message}</span>`;

    toastStack.appendChild(toast);

    window.setTimeout(() => {
        toast.remove();
    }, 4200);
}
