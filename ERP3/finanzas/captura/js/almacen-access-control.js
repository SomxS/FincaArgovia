// Módulo de Control de Acceso - Almacén
// Define los niveles de acceso y permisos del módulo

const ACCESS_LEVELS = {
    CAPTURA: 1,
    GERENCIA: 2,
    CONTABILIDAD: 3,
    ADMINISTRACION: 4
};

const PERMISSIONS = {
    [ACCESS_LEVELS.CAPTURA]: {
        name: "Captura",
        moduleName: "Salidas de almacén",
        canCreate: true,
        canEdit: true,
        canDelete: true,
        canView: true,
        canViewConcentrado: false,
        canExportExcel: false,
        canUploadFiles: true,
        canLockModule: false,
        canManageProducts: false,
        description: "Puede capturar, modificar y consultar las salidas de almacén del día"
    },
    [ACCESS_LEVELS.GERENCIA]: {
        name: "Gerencia",
        moduleName: "Almacén",
        canCreate: false,
        canEdit: false,
        canDelete: false,
        canView: true,
        canViewConcentrado: true,
        canExportExcel: true,
        canUploadFiles: false,
        canLockModule: false,
        canManageProducts: false,
        description: "Puede consultar el concentrado diario y balances individuales o generales, con opción de exportar a Excel"
    },
    [ACCESS_LEVELS.CONTABILIDAD]: {
        name: "Contabilidad/Dirección",
        moduleName: "Almacén",
        canCreate: false,
        canEdit: false,
        canDelete: false,
        canView: true,
        canViewConcentrado: true,
        canExportExcel: true,
        canUploadFiles: false,
        canLockModule: false,
        canManageProducts: false,
        description: "Puede filtrar por unidad de negocio sin modificar datos"
    },
    [ACCESS_LEVELS.ADMINISTRACION]: {
        name: "Administración",
        moduleName: "Almacén",
        canCreate: true,
        canEdit: true,
        canDelete: true,
        canView: true,
        canViewConcentrado: true,
        canExportExcel: true,
        canUploadFiles: true,
        canLockModule: true,
        canManageProducts: true,
        description: "Puede gestionar clases de insumos, productos y bloquear o desbloquear el módulo"
    }
};

function getUserAccessLevel() {
    return parseInt(localStorage.getItem('userAccessLevel')) || ACCESS_LEVELS.CAPTURA;
}

function setUserAccessLevel(level) {
    localStorage.setItem('userAccessLevel', level);
}

function getUserPermissions() {
    const level = getUserAccessLevel();
    return PERMISSIONS[level] || PERMISSIONS[ACCESS_LEVELS.CAPTURA];
}

function hasPermission(permission) {
    const permissions = getUserPermissions();
    return permissions[permission] === true;
}

function getModuleName() {
    const permissions = getUserPermissions();
    return permissions.moduleName;
}

function canAccessTab(tabId) {
    const permissions = getUserPermissions();
    
    switch(tabId) {
        case 'dashboard':
            return true;
        case 'outputs':
            return permissions.canView;
        case 'concentrado':
            return permissions.canViewConcentrado;
        default:
            return false;
    }
}

function applyAccessRestrictions() {
    const permissions = getUserPermissions();
    
    if (!permissions.canCreate) {
        $('.btn-create, #btnNuevaSalida, [onclick*="addOutput"]').hide();
    }
    
    if (!permissions.canEdit) {
        $('[onclick*="editOutput"]').hide();
    }
    
    if (!permissions.canDelete) {
        $('[onclick*="deleteOutput"]').hide();
    }
    
    if (!permissions.canExportExcel) {
        $('#btnExportarExcel, [onclick*="exportToExcel"]').hide();
    }
    
    if (!permissions.canUploadFiles) {
        $('[onclick*="uploadFileModal"]').hide();
    }
    
    if (!permissions.canLockModule) {
        $('[onclick*="lockModule"], [onclick*="unlockModule"]').hide();
    }
    
    if (!permissions.canViewConcentrado) {
        $('#tabs-almacen-tab-concentrado').parent().hide();
    }
}

function showAccessDeniedMessage() {
    alert({
        icon: "error",
        title: "Acceso denegado",
        text: "No tienes permisos para realizar esta acción",
        btn1: true,
        btn1Text: "Entendido"
    });
}

function initAccessControl() {
    const level = getUserAccessLevel();
    console.log(`Access Level: ${level} - ${PERMISSIONS[level]?.name}`);
    
    setTimeout(() => {
        applyAccessRestrictions();
    }, 500);
}
