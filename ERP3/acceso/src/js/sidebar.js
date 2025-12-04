class Sidebar {
    init(options) {
        this.render(options);
        this.initEvents();
        this.highlightCurrentRoute();
    }

    render(options) {
        const defaults = {
            parent: "#menu-sidebar",
            logo: "../../src/img/logos/coffee_icon.png",
            menuItems: [
                { icon: "icon-search", active: true, action: "search" },
                { icon: "icon-home", url: "/dev/home/" },
                { icon: "icon-chart-bar", url: "/dev/reportes/" },
                { icon: "icon-bell", action: "notifications" },
                { icon: "icon-clock", url: "/dev/historial/" },
                { icon: "icon-heart", url: "/dev/favoritos/" },
                { icon: "icon-credit-card", url: "/dev/pagos/" },
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
            <button class="w-12 h-12 hover:bg-[#4A3733] rounded-xl flex items-center justify-center transition" data-action="logout">
                <i class="icon-logout text-gray-400 hover:text-white text-xl"></i>
            </button>

        `;

        this.parent.html(sidebarHtml);
    }

    createMenuItems(menuItems) {
        return menuItems
            .map((item) => {
                const activeClass = item.active ? "bg-[#4A3733]" : "";
                const dataAttr = item.url ? `data-url="${item.url}"` : item.action ? `data-action="${item.action}"` : "";
                
                return `
                    <button class="w-12 h-12 ${activeClass} hover:bg-[#4A3733] rounded-xl flex items-center justify-center transition" ${dataAttr}>
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
        console.log("Toggle activado");
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