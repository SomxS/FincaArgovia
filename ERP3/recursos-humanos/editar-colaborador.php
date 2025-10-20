<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
    require_once('layout/script.php'); 
?>
<body>
    <?php require_once('../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <link rel="stylesheet" href="src/css/colaboradores.css">
<nav aria-label="breadcrumb" class="p-2 p-sm-0">
    <ol class="breadcrumb">
        <li class="breadcrumb-item text-uppercase text-muted">RH</li>
        <li class="breadcrumb-item text-uppercase pointer" onClick="redireccion('recursos-humanos/lista-de-colaboradores.php');"><u><i>Colaboradores</i></u>
        </li>
        <li class="breadcrumb-item fw-bold active">Modificación</li>
    </ol>
</nav>
<form class="row" id="formDatos">
    <div class="accordion p-0 m-0" id="accordionRH">
        <div class="accordion-item">
            <h2 class="accordion-header" id="informacionPersonalRH">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRH"
                    aria-expanded="true" aria-controls="collapseRH">
                    Información personal
                </button>
            </h2>
            <div id="collapseRH" class="accordion-collapse collapse show" aria-labelledby="informacionPersonalRH"
                data-bs-parent="#accordionRH">
                <div class="accordion-body row">
                    <div class="col-12 col-sm-12 col-md-2 col-lg-3 p-0 mb-3 mt-3" id="photo__perfil">
                        <input type="file" class="hide" id="file-profile" accept=".jpg, .jpeg, .png">
                        <img id="imgPerfil" src="../src/img/user.png" alt="Colaborador">
                        <span class="fs-6" onclick="$('#file-profile').click();" alt="Cambiar foto">
                            <i class="icon-camera fs-4"></i>SUBIR FOTO</span>
                        <p>
                            <i class='icon-pencil-5'></i>
                        </p>
                    </div>
                    <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptNombre" class="form-label fw-bold">Nombre *</label>
                                <input type="text" class="form-control text-uppercase" id="iptNombre"
                                    placeholder="Nombre(s) del colaborador" aria-label="Nombre" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptApePat" class="form-label fw-bold">Apellido paterno
                                    *</label>
                                <input type="text" class="form-control text-uppercase" id="iptApePat"
                                    placeholder="Apellido paterno" aria-label="Apellido paterno" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptApeMat" class="form-label fw-bold">Apellido materno *</label>
                                <input type="text" class="form-control text-uppercase" id="iptApeMat"
                                    placeholder="Apellido materno" aria-label="Apellido materno" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptEmail" class="form-label fw-bold">Correo electrónico *</label>
                                <input type="text" class="form-control" id="iptEmail" placeholder="ejemplo@ejemplo.com"
                                    aria-label="Correo electrónico" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptCurp " class="form-label fw-bold">CURP *</label>
                                <input type="text" class="form-control text-uppercase" id="iptCurp"
                                    placeholder="xxxx050101xxxxxx02" aria-label="CURP" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptNacimiento" class="form-label fw-bold">Fecha nacimiento</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="iptNacimiento" placeholder="01-01-2005"
                                        aria-label="Fecha nacimiento" aria-describedby="gv-dob" disabled>
                                    <span class="input-group-text" id="gv-dob"><i class="icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptEdad" class="form-label fw-bold">Edad</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="iptEdad" placeholder="xx años"
                                        aria-label="Edad" aria-describedby="gv-edad" disabled>
                                    <span class="input-group-text fw-bold" id="gv-edad">Años</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                                <label for="iptGenero" class="form-label fw-bold">Género</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="iptGenero"
                                        placeholder="Femenino / Masculino" aria-label="Género"
                                        aria-describedby="gv-genero" disabled>
                                    <span class="input-group-text" id="gv-genero"><i class="icon-male"></i><i
                                            class="icon-female"></i></span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 mb-3">
                                <label for="iptLugarNac" class="form-label fw-bold">Lugar de nacimiento</label>
                                <input type="text" class="form-control text-uppercase" id="iptLugarNac" placeholder=""
                                    aria-label="Lugar de nacimiento" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <label for="cbGradoEstudio" class="form-label fw-bold">Último grado de
                                    estudio</label>
                                <select class="form-select" id="cbGradoEstudio" aria-label="Último grado de estudio">
                                    <option value="0">NINGUNO</option>
                                    <option value="1">PRIMARIA</option>
                                    <option value="2">SECUNDARIA</option>
                                    <option value="3">PREPARATORIA</option>
                                    <option value="4">CARRERA TÉCNICA</option>
                                    <option value="5">LICENCIATURA</option>
                                    <option value="6">INGENIERÍA</option>
                                    <option value="7">MAESTRÍA</option>
                                    <option value="8">DOCTORADO</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <label for="iptCarrera" class="form-label fw-bold">Carrera</label>
                                <input type="text" class="form-control text-uppercase" id="iptCarrera" placeholder=""
                                    aria-label="Carrera" disabled=true />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <label for="iptTelefono" class="form-label fw-bold">Teléfono *</label>
                                <input type="text" class="form-control" id="iptTelefono" placeholder="962 123 45 67"
                                    aria-label="Teléfono" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <label for="iptCodigoPostal" class="form-label fw-bold">Código postal</label>
                                <input type="number" class="form-control" id="iptCodigoPostal" placeholder="30700"
                                    aria-label="Código postal" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-12 mb-3">
                                <label for="iptDireccion" class="form-label fw-bold">Dirección</label>
                                <input type="text" class="form-control text-uppercase" id="iptDireccion"
                                    placeholder="dirección" aria-label="Dirección" />
                                <span class="form-text text-danger d-none">
                                    <i class="icon-warning-1"></i>
                                    Campo requerido.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header text-center" id="accordionRH2">
                <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseRH2" aria-expanded="false" aria-controls="collapseRH2">
                    Información laboral
                </button>
            </h2>
            <div id="collapseRH2" class="accordion-collapse collapse" aria-labelledby="accordionRH2"
                data-bs-parent="#accordionRH">
                <div class="accordion-body row">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptPatron" class="form-label fw-bold">Patrón *</label>
                        <select class="form-select" id="iptPatron" aria-label="Patrón">
                            <option value="">Juan Carlos Valera de la Torre</option>
                            <option value="">María Bárbara Rocha Adames</option>
                            <option value="">Chacoys</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptUDN" class="form-label fw-bold">Ubicación laboral *</label>
                        <select class="form-select" id="iptUDN" aria-label="Ubicación laboral">
                            <option value="">CORPORATIVO</option>
                            <option value="">CHACOYS</option>
                            <option value="">FOGAZA</option>
                            <option value="">QUINTA TABACHINES</option>
                            <option value="">BAOS</option>
                            <option value="">PUNTO MODELO</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptArea" class="form-label fw-bold">Departamento *</label>
                        <select class="form-select" id="iptArea" aria-label="Departamento">
                            <option value="">Dirección operativa</option>
                            <option value="">Compras</option>
                            <option value="">Contabilidad</option>
                            <option value="">Tesorería</option>
                            <option value="">Capital humano</option>
                            <option value="">TICS</option>
                            <option value="">Marketing</option>
                            <option value="">Mantenimiento</option>
                            <option value="">Limpieza</option>
                            <option value="">Relaciones públicas</option>
                            <option value="">Dirección general</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptPuesto" class="form-label fw-bold">Puesto *</label>
                        <select class="form-select" id="iptPuesto" aria-label="Puesto">
                            <option value="">Dirección operativa</option>
                            <option value="">Auxiliar</option>
                            <option value="">Limpieza</option>
                            <option value="">Director general</option>
                            <option value="">Auditoria</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptIngreso" class="form-label fw-bold">Fecha ingreso *</label>
                        <div class="input-group">
                            <input type="date" class="form-control" placeholder="01-01-2005" id="iptIngreso"
                                aria-label="Fecha nacimiento" aria-describedby="gv-dob">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptCrecimiento" class="form-label fw-bold">Crecimiento laboral</label>
                        <div class="input-group">
                            <input type="text" class="form-control" aria-label="Crecimiento laboral" id="iptCrecimiento"
                                aria-describedby="gv-c-laboral" disabled>
                            <span class="input-group-text" id="gv-c-laboral"><i class="icon-chart-line"></i></span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptSalarioDiario" class="form-label fw-bold">Salario diario *</label>
                        <div class="input-group">
                            <span class="input-group-text" id="gv-salario-diario"><i class="icon-dollar"></i></span>
                            <input type="text" class="form-control" placeholder="00.00" id="iptSalarioDiario"
                                aria-label="Salario diario *" aria-describedby="gv-salario-diario">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptSalarioFiscal" class="form-label fw-bold">Salario fiscal</label>
                        <div class="input-group">
                            <span class="input-group-text" id="gv-salario-fiscal"><i class="icon-dollar"></i></span>
                            <input type="text" class="form-control" placeholder="00.00" id="iptSalarioFiscal"
                                aria-label="Salario fiscal *" aria-describedby="gv-salario-fiscal">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptPorcentajeAnti" class="form-label fw-bold">Porcentaje de anticipo
                            *</label>
                        <div class="input-group">
                            <input type="text" class="form-control" aria-label="Porcentaje de anticipo"
                                id="iptPorcentajeAnti" aria-describedby="gv-porcentaje-anticipo">
                            <span class="input-group-text" id="gv-porcentaje-anticipo"><i
                                    class="icon-percent"></i></span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptImss" class="form-label fw-bold">Alta ante en IMSS</label>
                        <div class="input-group">
                            <input type="date" class="form-control" placeholder="01-01-2005" id="iptImss"
                                aria-label="Alta ante en IMSS" aria-describedby="gv-alta-imss">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptNSS" class="form-label fw-bold">NSS</label>
                        <div class="input-group">
                            <input type="text" class="form-control" aria-label="NSS" id="iptNSS"
                                aria-describedby="gv-nss-span" placeholder="0000000000">
                            <span class="input-group-text" id="gv-nss-span"><i class="icon-barcode"></i></span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptRFC" class="form-label fw-bold">RFC</label>
                        <input type="text" class="form-control text-uppercase" id="iptRFC" placeholder="xxxx050101x"
                            aria-label="RFC" />
                        <span class="form-text text-danger d-none">
                            <i class="icon-warning-1"></i>
                            Campo requerido.
                        </span>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptBanco" class="form-label fw-bold">Banco</label>
                        <select class="form-select" id="iptBanco" aria-label="Banco">
                            <option value="">BANORTE</option>
                            <option value="">BANCOMER</option>
                            <option value="">BANAMEX</option>
                            <option value="">SANTANDER</option>
                            <option value="">HSBC</option>
                            <option value="">BANJÉRCITO</option>
                            <option value="">BANCOPPEL</option>
                            <option value="">BANSEFI</option>
                            <option value="">SCOTIABANK</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <label for="iptCuenta" class="form-label fw-bold">No. Cuenta</label>
                        <input type="text" class="form-control" id="iptCuenta" placeholder="XXXX XXXX XXXX"
                            aria-label="No. Cuenta" />
                        <span class="form-text text-danger d-none">
                            <i class="icon-warning-1"></i>
                            Campo requerido.
                        </span>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-6 mb-3">
                        <label for="iptOpiniones" class="form-label fw-bold">Opiniones sobre el
                            colaborador</label>
                        <input type="text" class="form-control text-uppercase" id="iptOpiniones"
                            placeholder="Opiniones sobre el colaborador" aria-label="Opiniones sobre el colaborador" />
                        <span class="form-text text-danger d-none">
                            <i class="icon-warning-1"></i>
                            Campo requerido.
                        </span>
                    </div>
                    <div class="card bg-grey-light mb-3">
                        <div class="card-body">
                            <h5 class="text-center fw-bold">Copias del expediente</h5>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk1" class="hide">
                        <label for="iptCk1" class="ck pointer" onclick="modalCheckbox(1, 'Solicitud de empleado');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk1">Solicitud de empleado
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk2" class="hide">
                        <label for="iptCk2" class="ck pointer" onclick="modalCheckbox(2, 'Acta de nacimiento');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk2">Acta de nacimiento
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk3" class="hide">
                        <label for="iptCk3" class="ck pointer" onclick="modalCheckbox(3, 'CURP');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk3">CURP
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk4" class="hide">
                        <label for="iptCk4" class="ck pointer" onclick="modalCheckbox(4, 'INE');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk4">INE
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk5" class="hide">
                        <label for="iptCk5" class="ck pointer" onclick="modalCheckbox(5, 'NSS');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk5">NSS
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk6" class="hide">
                        <label for="iptCk6" class="ck pointer" onclick="modalCheckbox(6, 'Comprobante de domicilio');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk6">Comprobante de domicilio
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk7" class="hide">
                        <label for="iptCk7" class="ck pointer" onclick="modalCheckbox(7, 'Credito infonavit');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk7">No. Crédito infonavit
                        </label>
                        <span></span>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <input type="checkbox" id="iptCk8" class="hide">
                        <label for="iptCk8" class="ck pointer" onclick="modalCheckbox(8, 'Carta(s) de recomendación');">
                            <i class=""></i>
                        </label>
                        <label for="" class="form-label" id="labelCk8">Carta de recomendación
                        </label>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4 mb-4">
        <button type="submit" class="btn btn-primary col-12 col-sm-6 offset-sm-3 col-md-4 offset-md-4">Actualizar
            información</button>
    </div>
</form>
<script src="src/js/editar-colaborador.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>
</html>