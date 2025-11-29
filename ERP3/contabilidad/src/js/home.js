let api = 'ctrl/ctrl-home.php';
let app;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    
    app = new App(api, "root");
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Home";
    }

    render() {
        this.layout();
        this.loadDashboard();
        this.navBar({
            theme:'light'
        })
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: 'w-full bg-gray-50 min-h-screen',
            card: {
                filterBar: { class: 'w-full', id: 'filterBar' + this.PROJECT_NAME },
                container: { class: 'w-full h-full', id: 'container' + this.PROJECT_NAME }
            }
        });

        this.headerDashboard();
    }

    headerDashboard() {
        const container = $("<div>", {
            class: "bg-white p-6 rounded-lg shadow-sm mb-6 mt-5"
        });

        const header = $("<div>", {
            class: "flex justify-between items-center"
        });

        const userName = this.getCookie('user') || 'Usuario';
        const almacen = this.getCookie('almacen') || 'Sin almacén seleccionado';

        const welcomeSection = $("<div>");
        welcomeSection.append(
            $("<h1>", {
                class: "text-2xl font-bold text-gray-800",
                text: `Bienvenido, ${userName}`
            }),
            $("<p>", {
                class: "text-sm text-gray-500",
                text: moment().format('dddd, DD [de] MMMM [de] YYYY')
            }),
         
        );

        const dateSection = $("<div>", {
            class: "flex items-center gap-2"
        });

        dateSection.append(
            $("<label>", {
                class: "text-sm text-gray-600",
                text: "Fecha de consulta"
            }),
            $("<div>", {
                id: "calendarHome",
                class: "w-64"
            })
        );

        header.append(welcomeSection, dateSection);
        container.append(header);
        $('#filterBar' + this.PROJECT_NAME).html(container);

        dataPicker({
            parent: "calendarHome",
            onSelect: () => this.loadDashboard()
        });
    }

    async loadDashboard() {
        const rangePicker = getDataRangePicker("calendarHome");

        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getDashboardData",
                fi: rangePicker.fi,
                ff: rangePicker.ff
            }
        });

        this.renderSummary(request.summary);
        this.renderMainMenu();
    }

    renderSummary(data) {
        const container = $("<div>", {
            class: "mb-6"
        });

        const title = $("<h2>", {
            class: "text-base font-semibold text-gray-700 mb-3",
            text: "Resumen del periodo"
        });

        const cardsContainer = $("<div>", {
            class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3"
        });

        const cards = [
            {
                icon: "icon-basket",
                iconColor: "text-blue-500",
                bgColor: "bg-blue-50",
                title: "Ventas del periodo",
                value: formatPrice(data.ventas || 25000)
            },
            {
                icon: "icon-basket",
                iconColor: "text-green-500",
                bgColor: "bg-green-50",
                title: "Compras del periodo",
                value: formatPrice(data.compras || 22942.23)
            },
           
            {
                icon: "icon-money",
                iconColor: "text-red-500",
                bgColor: "bg-red-50",
                title: "Retiros del periodo",
                value: formatPrice(data.retiros || 2057.32)
            },
            {
                icon: "icon-folder-open",
                iconColor: "text-purple-500",
                bgColor: "bg-purple-50",
                title: "Archivos subidos del periodo",
                value: data.archivos || 10
            },
        ];

        cards.forEach(card => {
            const cardElement = $("<div>", {
                class: "bg-white p-4 rounded-lg shadow-sm border border-gray-200"
            });

            const iconContainer = $("<div>", {
                class: `w-10 h-10 ${card.bgColor} rounded-lg flex items-center justify-center mb-2`
            });

            iconContainer.append(
                $("<i>", {
                    class: `${card.icon} text-lg ${card.iconColor}`
                })
            );

            cardElement.append(
                iconContainer,
                $("<p>", {
                    class: "text-xs text-gray-600 mb-1",
                    text: card.title
                }),
                $("<p>", {
                    class: "text-xl font-bold text-gray-800",
                    text: card.value
                })
            );

            cardsContainer.append(cardElement);
        });

        container.append(title, cardsContainer);
        $('#container' + this.PROJECT_NAME).html(container);
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return null;
    }

    renderMainMenu() {
        const modules = this.createModules();
        this.renderModules(modules);
    }

    createModules() {
        return [
            {
                icon: "icon-folder-open",
                color: "bg-gray-50",
                textColor: "text-green-600",
                titulo: "Archivos",
                descripcion: "Concentrado de archivos",
                enlace: "../captura/index.php"
            },
            {
                icon: "icon-basket",
                color: "bg-green-50",
                textColor: "text-green-600",
                titulo: "Ventas",
                descripcion: "Concentrado de ventas",
                enlace: "../captura/index.php"
            },
            {
                icon: "icon-users",
                color: "bg-orange-50",
                textColor: "text-orange-600",
                titulo: "Clientes",
                descripcion: "Concentrado de consumos y pagos a créditos",
                enlace: "../finanzas/captura/clientes.php"
            },
            {
                icon: "icon-basket",
                color: "bg-orange-50",
                textColor: "text-orange-600",
                titulo: "Compras",
                descripcion: "Concentrado de compras",
                enlace: "../finanzas/captura/compras.php"
            },
            {
                icon: "icon-box",
                color: "bg-orange-50",
                textColor: "text-orange-600",
                titulo: "Almacén",
                descripcion: "Concentrado de entradas y salidas de almacén",
                enlace: "../finanzas/captura/almacen.php"
            },
            {
                icon: "icon-calculator",
                color: "bg-green-50",
                textColor: "text-green-600",
                titulo: "Costos",
                descripcion: "Concentrado de costos",
                enlace: "../captura/index.php"
            },
            {
                icon: "icon-briefcase",
                color: "bg-green-50",
                textColor: "text-green-600",
                titulo: "Proveedores",
                descripcion: "Concentrado de compras y pagos",
                enlace: "../captura/index.php"
            },
            {
                icon: "icon-doc-text",
                color: "bg-green-50",
                textColor: "text-green-600",
                titulo: "Carátula",
                descripcion: "Consulta el resumen por periodo",
                enlace: "../captura/index.php"
            },
            {
                icon: "icon-money",
                color: "bg-blue-50",
                textColor: "text-blue-600",
                titulo: "Tesorería",
                descripcion: "Concentrado de retiros y reembolsos"
            },
            {
                icon: "icon-user",
                color: "bg-blue-50",
                textColor: "text-blue-600",
                titulo: "Capital Humano",
                descripcion: "Consulta de anticipos"
            },
            {
                icon: "icon-cog",
                color: "bg-blue-50",
                textColor: "text-blue-600",
                titulo: "Administración",
                descripcion: "Desbloqueo de módulos y gestión de conceptos",
                enlace: "../finanzas/administrador/index.php"
            }
        ];
    }

    renderModules(modules) {
        this.moduleCard({
            parent: 'container' + this.PROJECT_NAME,
            title: "Menú principal",
            subtitle: "",
            theme: "light",
            json: modules
        });
    }

    moduleCard(options) {
        const defaults = {
            parent: "root",
            title: "",
            subtitle: "",
            theme: "light",
            json: []
        };

        const opts = Object.assign({}, defaults, options);
        const isDark = opts.theme === "dark";

        const colors = {
            cardBg: isDark ? "bg-[#2C3544]" : "bg-white",
            titleColor: isDark ? "text-white" : "text-gray-800",
            subtitleColor: isDark ? "text-gray-400" : "text-gray-600",
            badgeColor: isDark ? "bg-blue-800 text-white" : "bg-blue-100 text-blue-800"
        };

        const titleContainer = $("<div>", { class: "w-full px-4 mt-2 mb-2" });
        const title = $("<h1>", { 
            class: "text-2xl font-bold text-gray-900 mb-2", 
            text: opts.title 
        });
        const subtitle = $("<p>", { 
            class: colors.subtitleColor + " ", 
            text: opts.subtitle 
        });
        titleContainer.append(title, subtitle);

        const container = $("<div>", {
            class: "w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 p-4"
        });

        opts.json.forEach((item) => {
            let iconContent = item.icon
                ? `<div class="w-10 h-10 flex items-center justify-center ${item.color} ${item.textColor} rounded-lg text-lg mb-3 group-hover:bg-opacity-80 transition-all"><i class="${item.icon}"></i></div>`
                : item.imagen
                    ? `<img class="w-10 h-10 rounded-lg mb-2" src="${item.imagen}" alt="${item.titulo}">`
                    : "";

            const badge = item.badge
                ? `<span class="px-2 py-0.5 rounded-full text-xs font-medium ${colors.badgeColor}">${item.badge}</span>`
                : "";

            const card = $(`
                <div class="group relative h-[180px] ${colors.cardBg} rounded-xl shadow-md
                overflow-hidden p-3 flex flex-col justify-between cursor-pointer
                border border-transparent hover:scale-[1.05] hover:border-blue-700
                transition-transform duration-300 ease-in-out transform font-[Poppins]">
                    <div class="flex justify-between items-start">
                        ${iconContent}
                        ${badge}
                    </div>
                    <div class="flex-grow flex flex-col justify-center">
                        <h2 class="text-base font-bold ${colors.titleColor}">${item.titulo}</h2>
                        ${item.descripcion ? `<p class="${colors.subtitleColor} text-xs mt-1 line-clamp-2">${item.descripcion}</p>` : ""}
                    </div>
                    <div class="mt-2 flex items-center ${item.textColor} text-[11px]">
                        <span>Acceder</span>
                        <i class="icon-right-1 ml-2 text-xs transition-transform group-hover:translate-x-2"></i>
                    </div>
                </div>
            `).click(function () {
                if (item.enlace) window.location.href = item.enlace;
                if (item.href) window.location.href = item.href;
                if (item.onClick) item.onClick();
            });

            container.append(card);
        });

        const div = $('<div>', {
            class: 'lg:px-8 mt-5'
        });
        div.append(titleContainer, container);

        $(`#${opts.parent}`).empty().append(div);
    }
}
