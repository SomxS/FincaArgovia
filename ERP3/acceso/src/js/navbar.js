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
                        <a href="/dev/menu/" class="text-white hover:text-gray-400" title="Ir a Menús">
                            <i class="icon-th-large-3"></i>
                        </a>
                    </li>

                </ul>
                <button id="btnUserMenu" class="ml-2 flex items-center justify-center border-l border-gray-500 pl-2">
                    <img src="${this.settings.imgPerfil}" alt="Usuario" class="w-10 h-10 rounded-full border-2 border-white" />
                </button>
            </nav>
            <div class="relative mt-16 z-50">
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
        // Evento para abrir el menu del usuario
        $("#btnUserMenu, #btnCloseUserMenu").on("click", () =>
            this.toggleUserMenu(),
        );
        // Evento para cerrar sesión
        $("#btnLogout").on("click", () => this.logout());
        // Evento para abrir/cerrar sidebar
        $("#toggleSidebar").on("click", () => this.toggleSidebar());
        $("#btn_perfil").on("click", () => window.location.href = "/dev/perfil/");
        $("#btn_admin").on("click", () => window.location.href = "/dev/admin/");
        $("#btn_pqts").on("click", () => window.location.href = "/dev/catalogos/");
    }

    toggleUserMenu() {
        $("#userMenuDropdown").toggleClass("opacity-0 scale-95 invisible");
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
