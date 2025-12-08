class Sidebar {
    init(options) {
        this.render(options);
        this.initEvents();
        this.highlightCurrentRoute();
        this.loadDarkMode();
    }

    render(options) {
        const defaults = {
            parent: "#menu-sidebar",
            logo: "../../src/img/logos/coffee_icon.png",
            menuItems: [
                { icon: "icon-home",   url: "/ERP3/operacion/almacen/", title: "Inicio" },
                { icon: "icon-chart-line-1", url: "/ERP3/operacion/almacen/reporte.php", title: "Reportes" },
                { icon: "icon-gauge", url: "/ERP3/operacion/almacen/dashboard.php", title: "Dashboard" },
                // { icon: "icon-clock", url: "/ERP3/operacion/historial/", title: "Historial" },
                // { icon: "icon-heart", url: "/ERP3/operacion/favoritos/", title: "Favoritos" },
            ],
        };

        this.settings = Object.assign({}, defaults, options);
        this.parent = $(this.settings.parent);

        const sidebarHtml = `
            <div class="py-2">
                <img class='w-14 h-14' src='${this.settings.logo}' alt="Logo" />
            </div>
            ${this.createMenuItems(this.settings.menuItems)}
            <div class="flex-1"></div>
            <button class="w-12 h-12 hover:bg-[#4A3733] rounded-xl flex items-center justify-center transition" data-action="logout" title="Cerrar sesión">
                <i class="icon-logout text-gray-400 hover:text-white text-xl"></i>
            </button>
            <button class="w-12 h-12 bg-[#4A3733] hover:bg-gray-600 rounded-xl flex items-center justify-center transition" data-action="toggle" title="Modo oscuro">
                <i class="icon-toggle-on text-white text-xl"></i>
            </button>
        `;

        this.parent.html(sidebarHtml);
    }

    createMenuItems(menuItems) {
        return menuItems
            .map((item) => {
                const activeClass = item.active ? "bg-[#4A3733]" : "";
                const dataAttr = item.url ? `data-url="${item.url}"` : item.action ? `data-action="${item.action}"` : "";
                const title = item.title || "";
                
                return `
                    <button class="w-12 h-12 ${activeClass} hover:bg-[#4A3733] rounded-xl flex items-center justify-center transition" ${dataAttr} title="${title}">
                        <i class="${item.icon} ${item.active ? 'text-white' : 'text-gray-400 hover:text-white'} text-xl"></i>
                    </button>
                `;
            })
            .join("");
    }

    initEvents() {
        this.parent.on("click", "button[data-url]", (e) => {
            const url = $(e.currentTarget).data("url");
            window.location.href = url;
        });

        this.parent.on("click", "button[data-action='search']", () => {
            this.handleSearch();
        });

        this.parent.on("click", "button[data-action='notifications']", () => {
            this.handleNotifications();
        });

        this.parent.on("click", "button[data-action='logout']", () => {
            this.handleLogout();
        });

        this.parent.on("click", "button[data-action='toggle']", () => {
            this.handleToggle();
        });
    }

    handleSearch() {
        console.log("Búsqueda activada");
    }

    handleNotifications() {
        console.log("Notificaciones");
    }

    handleLogout() {
        Swal.fire({
            title: "¿Cerrar sesión?",
            text: "¿Estás seguro de que deseas cerrar sesión?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, cerrar sesión",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../../salir";
            }
        });
    }

    handleToggle() {
        const body = $("body");
        const isDark = body.hasClass("dark-mode");
        
        if (isDark) {
            body.removeClass("dark-mode");
            localStorage.setItem("darkMode", "false");
            this.parent.find("button[data-action='toggle'] i")
                .removeClass("icon-toggle-on")
                .addClass("icon-toggle-off");
        } else {
            body.addClass("dark-mode");
            localStorage.setItem("darkMode", "true");
            this.parent.find("button[data-action='toggle'] i")
                .removeClass("icon-toggle-off")
                .addClass("icon-toggle-on");
        }
    }

    loadDarkMode() {
        const darkMode = localStorage.getItem("darkMode");
        if (darkMode === "true") {
            $("body").addClass("dark-mode");
            this.parent.find("button[data-action='toggle'] i")
                .removeClass("icon-toggle-off")
                .addClass("icon-toggle-on");
        }
    }

    highlightCurrentRoute() {
        const currentUrl = window.location.pathname;
        this.parent.find("button[data-url]").each(function () {
            const button = $(this);
            if (button.data("url") === currentUrl) {
                button.addClass("bg-[#4A3733]");
                button.find("i").removeClass("text-gray-400").addClass("text-white");
            }
        });
    }
}

$(async () => {
    let sidebar = new Sidebar();
    sidebar.init({});
});