let api = 'ctrl/ctrl-home.php';
let app;
let lsudn;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    lsudn = data.udn || [];
    
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
                filterBar: { class: 'w-full mt-16', id: 'containerFilterBar'  },
                container: { class: 'w-full h-full', id: 'container' + this.PROJECT_NAME }
            }
        });

        $('#containerFilterBar').html(`
            <div class="border bg-white p-3 rounded-lg " id="filterBar${this.PROJECT_NAME}"></div>
            <div class="py-3" id="containerCards"></div>
        `);

        this.filterBar();
    }

    filterBar() {
        const userName = this.getCookie('user') || 'Usuario';
        const currentDate = moment().format('dddd, DD [de] MMMM [del] YYYY');

        const headerHtml = `
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-sm font-semibold text-gray-800">Bienvenido, ${userName}</h1>
                    <p class="text-xs text-gray-500 capitalize">${currentDate}</p>
                </div>
            </div>
        `;

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "div",
                    class: "col-12 col-lg-6",
                    id: "titleHeader",
                    lbl: "",
                    html: headerHtml
                },
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Filtrar por udn",
                    class: "col-12 col-lg-3",
                    data: lsudn,
                    onchange: "app.loadDashboard()"
                },
                {
                    opc: "input-calendar",
                    id: "calendarHome",
                    lbl: "Seleccionar periodo",
                    class: "col-12 col-lg-3"
                }
            ]
        });

        $('#titleHeader').prev('label').remove();

        dataPicker({
            parent: "calendarHome",
            onSelect: () => this.loadDashboard()
        });
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return null;
    }

    // Dashboard.

    async loadDashboard() {
        const rangePicker = getDataRangePicker("calendarHome");
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val() || '';

        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getDashboardData",
                fi: rangePicker.fi,
                ff: rangePicker.ff,
                udn: udn
            }
        });

      

        this.showCards(request.summary);
        this.renderMainMenu();
    }

    showCards(data) {
        const container = $("<div>", { class: "px-8" });

        const title = $("<h1>", {
            class: "text-sm font-semibold text-gray-500 mb-2",
            text: "Resumen del periodo"
        });

        const cardsContainer = $("<div>", {
            class: "grid grid-cols-2 md:grid-cols-4 gap-2"
        });

        const cards = [
            { icon: "icon-basket", iconColor: "text-blue-500", bgColor: "bg-blue-50", title: "Ventas", value: formatPrice(data.ventas || 25000) },
            { icon: "icon-basket", iconColor: "text-green-500", bgColor: "bg-green-50", title: "Compras", value: formatPrice(data.compras || 22942.23) },
            { icon: "icon-money", iconColor: "text-red-500", bgColor: "bg-red-50", title: "Retiros", value: formatPrice(data.retiros || 2057.32) },
            { icon: "icon-folder-open", iconColor: "text-purple-500", bgColor: "bg-purple-50", title: "Archivos", value: data.archivos || 10 },
        ];

        cards.forEach(card => {
            const cardElement = $("<div>", {
                class: "bg-white px-3 py-4 rounded-lg border border-gray-200 flex items-center gap-3"
            });

            const iconContainer = $("<div>", {
                class: `w-10 h-10 ${card.bgColor} rounded-lg flex items-center justify-center`
            });

            iconContainer.append($("<i>", { class: `${card.icon} ${card.iconColor}` }));

            const textContainer = $("<div>", { class: "flex-1 text-right" });
            textContainer.append(
                $("<p>", { class: "text-xs text-gray-500", text: card.title }),
                $("<p>", { class: "text-base font-bold text-gray-800", text: card.value })
            );

            cardElement.append(iconContainer, textContainer);

            cardsContainer.append(cardElement);
        });

        container.append(title, cardsContainer);
        $('#containerCards').html(container);
    }

    // Modulos.

    renderModules(modules) {
        this.moduleCard({
            parent: 'container' + this.PROJECT_NAME,
            title: "Menú principal",
            subtitle: "",
            theme: "light",
            json: modules
        });
    }

    renderMainMenu() {
        const modules = this.jsonModules();
        this.renderModules(modules);
    }

    jsonModules() {
        return [
            {
                icon: "icon-cog",
                color: "bg-blue-50",
                textColor: "text-blue-600",
                titulo: "Administración",
                descripcion: "Desbloqueo de módulos y gestión de conceptos",
                enlace: "../finanzas/administrador/index.php"
            },
            {
                icon: "icon-folder-open",
                color: "bg-gray-50",
                textColor: "text-gray-600",
                titulo: "Archivos",
                descripcion: "Concentrado de archivos",
                enlace: "../captura/index.php"
            },
            // {
            //     icon: "icon-basket",
            //     color: "bg-green-50",
            //     textColor: "text-green-600",
            //     titulo: "Ventas",
            //     descripcion: "Concentrado de ventas",
            //     enlace: "../captura/index.php"
            // },
            // {
            //     icon: "icon-users",
            //     color: "bg-orange-50",
            //     textColor: "text-orange-600",
            //     titulo: "Clientes",
            //     descripcion: "Concentrado de consumos y pagos a créditos",
            //     enlace: "../finanzas/captura/clientes.php"
            // },
            // {
            //     icon: "icon-basket",
            //     color: "bg-orange-50",
            //     textColor: "text-orange-600",
            //     titulo: "Compras",
            //     descripcion: "Concentrado de compras",
            //     enlace: "../finanzas/captura/compras.php"
            // },
            // {
            //     icon: "icon-box",
            //     color: "bg-orange-50",
            //     textColor: "text-orange-600",
            //     titulo: "Almacén",
            //     descripcion: "Concentrado de entradas y salidas de almacén",
            //     enlace: "../finanzas/captura/almacen.php"
            // },
            // {
            //     icon: "icon-calculator",
            //     color: "bg-green-50",
            //     textColor: "text-green-600",
            //     titulo: "Costos",
            //     descripcion: "Concentrado de costos",
            //     enlace: "../captura/index.php"
            // },
            // {
            //     icon: "icon-briefcase",
            //     color: "bg-green-50",
            //     textColor: "text-green-600",
            //     titulo: "Proveedores",
            //     descripcion: "Concentrado de compras y pagos",
            //     enlace: "../captura/index.php"
            // },
            // {
            //     icon: "icon-doc-text",
            //     color: "bg-green-50",
            //     textColor: "text-green-600",
            //     titulo: "Carátula",
            //     descripcion: "Consulta el resumen por periodo",
            //     enlace: "../captura/index.php"
            // },
            // {
            //     icon: "icon-money",
            //     color: "bg-blue-50",
            //     textColor: "text-blue-600",
            //     titulo: "Tesorería",
            //     descripcion: "Concentrado de retiros y reembolsos"
            // },
            // {
            //     icon: "icon-user",
            //     color: "bg-blue-50",
            //     textColor: "text-blue-600",
            //     titulo: "Capital Humano",
            //     descripcion: "Consulta de anticipos"
            // },
           
        ];
    }

   
    // Components.

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
            class: "text-sm font-semibold text-gray-600 mb-2", 
            text: opts.title 
        });
        const subtitle = $("<p>", { 
            class: colors.subtitleColor + " ", 
            text: opts.subtitle 
        });
        titleContainer.append(title, subtitle);

        const container = $("<div>", {
            class: "w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 p-2"
        });

        opts.json.forEach((item) => {
            let iconContent = item.icon
                ? `<div class="w-8 h-8 flex items-center justify-center ${item.color} ${item.textColor} rounded-md text-sm mb-2 group-hover:bg-opacity-80 transition-all"><i class="${item.icon}"></i></div>`
                : item.imagen
                    ? `<img class="w-8 h-8 rounded-md mb-1" src="${item.imagen}" alt="${item.titulo}">`
                    : "";

            const badge = item.badge
                ? `<span class="px-2 py-0.5 rounded-full text-xs font-medium ${colors.badgeColor}">${item.badge}</span>`
                : "";

            const card = $(`
                <div class="group relative h-[140px] ${colors.cardBg} rounded-lg shadow-sm
                overflow-hidden p-2.5 flex flex-col justify-between cursor-pointer
                border border-gray-200 hover:scale-[1.03] hover:border-blue-600
                transition-transform duration-200 ease-in-out transform">
                    <div class="flex justify-between items-start">
                        ${iconContent}
                        ${badge}
                    </div>
                    <div class="flex-grow flex flex-col justify-center">
                        <h2 class="text-sm font-semibold ${colors.titleColor}">${item.titulo}</h2>
                        ${item.descripcion ? `<p class="${colors.subtitleColor} text-[10px] mt-0.5 line-clamp-2">${item.descripcion}</p>` : ""}
                    </div>
                    <div class="mt-1 flex items-center ${item.textColor} text-[10px]">
                        <span>Acceder</span>
                        <i class="icon-right-1 ml-1 text-[10px] transition-transform group-hover:translate-x-1"></i>
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
            class: 'lg:px-8 mt-3'
        });
        div.append(titleContainer, container);

        $(`#${opts.parent}`).empty().append(div);
    }
}
