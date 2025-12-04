let level = 0;

class Navbar {
    init(options) {
        this.render(options);
        this.initEvents();
    }

    render(options) {
        const defaults = {
            logo: "https://upload.wikimedia.org/wikipedia/commons/5/59/User-avatar.svg",
            imgPerfil: "https://images.ctfassets.net/xjcz23wx147q/iegram9XLv7h3GemB5vUR/0345811de2da23fafc79bd00b8e5f1c6/Max_Rehkopf_200x200.jpeg",
            company: "Compañia",
            username: "Roberto G.",
            parent: "body",
        };

        this.settings = Object.assign({}, defaults, options);
        this.parent = $(this.settings.parent);

        const navbarHtml = `
            <nav class="bg-[#7C5031] fixed top-0 left-0 w-full px-4 py-3 h-16 z-50 shadow flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button id="toggleSidebar" class="text-white text-2xl">☰</button>
                </div>
                <ul class="hidden md:flex flex-1 justify-end space-x-6">
                    <li class="invisible"><a href="#" class="hover:text-gray-400">Inicio</a></li>
                    <li class="invisible"><a href="#" class="hover:text-gray-400">Servicios</a></li>
                    <li>
                        <button id="btnAppsMenu" class="text-white hover:bg-white/10 p-2 rounded-full transition-all duration-200" title="Aplicaciones">
                            <i class="icon-th-large-3 text-xl"></i>
                        </button>
                    </li>

                </ul>
                <button id="btnUserMenu" class="ml-2 flex items-center justify-center border-l border-gray-500 pl-2">
                    <img src="${this.settings.imgPerfil}" alt="Usuario" class="w-10 h-10 rounded-full border-2 border-white" />
                </button>
            </nav>
            <div class="relative mt-16 z-50">
                <div id="appsMenuDropdown" class="absolute right-16 w-80 bg-white rounded-2xl shadow-2xl opacity-0 scale-95 invisible transition-all duration-200 ease-out p-6">
                    <div class="grid grid-cols-3 gap-3">
                        <a href="/dev/finanzas/" class="flex flex-col items-center p-1 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mb-2 transition-transform">
                                <i class="icon-dollar text-white text-2xl"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Finanzas</span>
                        </a>
                        <a href="/dev/contabilidad/" class="flex flex-col items-center p-1 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mb-2 transition-transform">
                                <i class="icon-calculator text-white text-2xl"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Contabilidad</span>
                        </a>
                        <a href="/dev/operacion/" class="flex flex-col items-center p-1 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center mb-2 transition-transform">
                                <i class="icon-cog text-white text-2xl"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Operación</span>
                        </a>
                        <a href="/dev/almacen/" class="flex flex-col items-center p-1 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center mb-2 transition-transform">
                                <i class="icon-box text-white text-2xl"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Almacén</span>
                        </a>
                        <a href="/dev/tics/" class="flex flex-col items-center p-1 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center mb-2 transition-transform">
                                <i class="icon-laptop text-white text-2xl"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">TICs</span>
                        </a>
                        <a href="/dev/reportes/" class="flex flex-col items-center p-1 hover:bg-gray-100 rounded-xl transition-all duration-200 group">
                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center mb-2 transition-transform">
                                <i class="icon-chart-bar text-white text-2xl"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Reportes</span>
                        </a>
                    </div>
                </div>
                <div id="userMenuDropdown" class="absolute right-0 w-64 bg-white rounded-2xl shadow-lg opacity-0 scale-95 invisible transition-all duration-500 ease-out">
                    
                    <div class="relative flex flex-col items-center bg-[#7C5031]  h-20 rounded-t-2xl">
                        <button id="btnCloseUserMenu" class="btn btn-sm p-1 absolute top-2 right-3 text-gray-400 hover:text-white focus:outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    <div class="absolute -bottom-10">
                            <img src="${this.settings.imgPerfil}" alt="Usuario" class="w-20 h-20 rounded-full border-2 border-white shadow-lg" />
                        </div>
                    </div>
                    <div class="flex flex-col items-center pt-4 px-4 space-y-2">
                        <p class=" font-semibold text-lg mt-3">${this.settings.username}</p>
                    </div>
                    <div class="w-full space-y-2 px-4 mt-2 mb-3">
                        <button class="w-full font-medium hover:bg-gray-100 border shadow-sm px-3 py-2 rounded-lg cursor-pointer text-left" id="btn_perfil"><i class="icon-user"></i> Mi Perfil</button>
                        <button class="w-full font-medium hover:bg-gray-100 border shadow-sm px-3 py-2 rounded-lg cursor-pointer text-left" id="btn_admin"><i class="icon-cog"></i> Configuración</button>
                    </div>
                    <div class="w-full px-4 mt-2 mb-3">
                        <button id="btnLogout" class="w-full px-3 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg transition-all duration-300">Cerrar sesión</button>
                    </div>
                </div>

            </div>
        `;

        this.parent.prepend(navbarHtml);
    }

    initEvents() {
        $("#btnUserMenu, #btnCloseUserMenu").on("click", () =>
            this.toggleUserMenu(),
        );
        $("#btnAppsMenu").on("click", () => this.toggleAppsMenu());
        $("#btnLogout").on("click", () => this.logout());
        $("#toggleSidebar").on("click", () => this.toggleSidebar());
        $("#btn_perfil").on("click", () => window.location.href = "/dev/perfil/");
        $("#btn_admin").on("click", () => window.location.href = "/dev/admin/");
        $("#btn_pqts").on("click", () => window.location.href = "/dev/catalogos/");
        
        $(document).on("click", (e) => {
            if (!$(e.target).closest("#btnAppsMenu, #appsMenuDropdown").length) {
                $("#appsMenuDropdown").addClass("opacity-0 scale-95 invisible");
            }
            if (!$(e.target).closest("#btnUserMenu, #userMenuDropdown").length) {
                $("#userMenuDropdown").addClass("opacity-0 scale-95 invisible");
            }
        });
    }

    toggleUserMenu() {
        $("#appsMenuDropdown").addClass("opacity-0 scale-95 invisible");
        $("#userMenuDropdown").toggleClass("opacity-0 scale-95 invisible");
    }

    toggleAppsMenu() {
        $("#userMenuDropdown").addClass("opacity-0 scale-95 invisible");
        $("#appsMenuDropdown").toggleClass("opacity-0 scale-95 invisible");
    }

    toggleSidebar() {
        $("#sidebar").removeAttr("style").toggleClass("-translate-x-full");
    }

    logout() {
        Swal.fire({
            title: "¿Está seguro?",
            text: "Está a punto de cerrar su sesión actual.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Cerrar sesión",
            cancelButtonText: "Cancelar",
            customClass: {
                popup: "rounded-lg shadow-lg",
                title: "text-2xl font-semibold",
                content: "",
                confirmButton:
                    "py-2 px-4 rounded",
                cancelButton:
                    "bg-secondary border border-gray-500 py-2 px-4 rounded hover:bg-[#555555]",
            },
            background: "#ffff",
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) window.location.href = "../../salir";
        });
    }
}

$(async () => {
    // const data = await useFetch({ url: "../access/ctrl/ctrl-access.php", data: { opc: 'company' } });
    let data = [];
    let navbar = new Navbar();

    let user = '';
    if (data['user']) {
        user = data['user'];
    } else {
        user = 'Usuario';
    }

    level = data.level;

    navbar.init({
        // logo: "",
        // imgPerfil: data['photo'],
        // company: data['company'],
        // username: user,
        parent: "#menu-navbar",
    });
});
