# Implementation Plan - Módulo de Almacén

## Task List

- [x] 1. Set up project structure and database schema



  - Create directory structure for almacen module in finanzas/captura/
  - Create database tables: warehouse_output, product, product_class, file, module, module_unlock, monthly_module_lock, audit_log
  - Add database indexes for performance optimization on operation_date, product_id, and active fields
  - Verify database connection and table relationships



  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_

- [ ] 2. Implement Model layer (mdl-almacen.php)
- [ ] 2.1 Create base model class structure
  - Extend CRUD class and initialize $bd and $util properties

  - Set database prefix to "rfwsmqex_finanzas."
  - Load required configuration files (_CRUD.php, _Utileria.php)
  - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1_

- [ ] 2.2 Implement warehouse output CRUD methods
  - Create listOutputs() method to retrieve outputs by date and UDN with JOIN to product and product_class tables
  - Create createOutput() method using _Insert for new warehouse outputs

  - Create updateOutput() method using _Update for editing existing outputs
  - Create deleteOutputById() method using _Delete for removing outputs
  - Create getOutputById() method using _Select to retrieve single output details
  - _Requirements: 1.4, 2.1, 2.4, 2.5, 3.1, 3.2_


- [ ] 2.3 Implement supply items and filters methods
  - Create lsSupplyItems() method using _Read to get active products for selector with id and name AS valor
  - Create lsUDN() method to retrieve business units list
  - Create lsProductClass() method to get product classifications
  - _Requirements: 1.3, 2.2, 4.1_


- [ ] 2.4 Implement consolidated report methods
  - Create listConcentrado() method with complex UNION query for inputs (from purchases) and outputs (from warehouse_output)
  - Create getBalanceByWarehouse() method to calculate initial balance, total inputs, total outputs, and final balance per product




  - Implement date range filtering in consolidated queries
  - Group results by product_id and calculate totals
  - _Requirements: 5.1, 5.2, 5.3, 5.6, 5.7_

- [x] 2.5 Implement audit logging methods

  - Create logAuditDelete() method using _Insert to record deletions in audit_log table
  - Include user_id, record_id, table_name, action, amount, and timestamp in audit entries
  - _Requirements: 3.2_

- [ ] 3. Implement Controller layer (ctrl-almacen.php)
- [ ] 3.1 Create base controller class and init method
  - Extend mdl class and add session_start()

  - Validate $_POST['opc'] exists or exit
  - Implement init() method to return supply items list and UDN list for filters
  - _Requirements: 1.3, 2.2, 4.1_

- [ ] 3.2 Implement warehouse output listing method
  - Create ls() method to retrieve outputs by date from $_POST['date'] and $_POST['udn']
  - Format each output row with id, Almacén, Monto (using evaluar()), Descripción

  - Add edit and delete action buttons to each row
  - Calculate and return daily total sum of all outputs
  - Return array with 'row' and 'total' keys
  - _Requirements: 1.4, 1.5, 1.6_

- [ ] 3.3 Implement add warehouse output method
  - Create addOutput() method receiving warehouse_id, amount, description from $_POST

  - Validate all fields are required and amount > 0
  - Set operation_date to current date and active to 1
  - Call createOutput() from model with $this->util->sql($_POST)
  - Return status 200 on success or 400/500 on error with appropriate message
  - _Requirements: 2.1, 2.3, 2.4, 2.6_

- [x] 3.4 Implement edit warehouse output method

  - Create getOutput() method to retrieve single output by id from $_POST['id']
  - Return status 200 with data or 404 if not found
  - Create editOutput() method receiving id, warehouse_id, amount, description from $_POST
  - Validate all fields and call updateOutput() from model with $this->util->sql($_POST, 1)
  - Return status 200 on success or 400/500 on error
  - _Requirements: 2.5, 2.6_

- [x] 3.5 Implement delete warehouse output method

  - Create deleteOutput() method receiving id from $_POST['id']
  - Call getOutputById() to retrieve output data before deletion
  - Call logAuditDelete() to record user_id, date, amount, and table_name in audit log
  - Call deleteOutputById() to remove the output
  - Return status 200 with success message or 500 on error
  - _Requirements: 3.1, 3.2, 3.4_

- [x] 3.6 Implement consolidated report method

  - Create getConcentrado() method receiving fi (fecha inicial), ff (fecha final), udn from $_POST
  - Call listConcentrado() from model with date range and UDN filter
  - Call getBalanceByWarehouse() for each product to calculate balances
  - Format data with separate columns for Entradas (green) and Salidas (orange)



  - Calculate and return general totals for inputs, outputs, and final balance
  - Return array with 'row' and 'totals' keys
  - _Requirements: 5.1, 5.2, 5.3, 5.5, 5.6, 5.7_

- [ ] 3.7 Implement file upload method
  - Create uploadFile() method receiving file from $_FILES['file'] and date from $_POST['date']
  - Validate file exists and size does not exceed 20MB (20 * 1024 * 1024 bytes)

  - Validate file extension is in allowed list: pdf, jpg, jpeg, png, xlsx, xls
  - Generate unique filename and move file to upload directory
  - Insert file record in file table with udn_id, user_id, file_name, upload_date, size_bytes, path, extension, operation_date
  - Return status 200 on success or 400 on validation error
  - _Requirements: 1.7_

- [ ] 3.8 Create helper functions
  - Create renderStatus() function to return HTML badge with color based on active status (1=green "Activo", 0=red "Inactivo")

  - Create formatOutputRow() function to format table row data with proper structure
  - Create calculateDailyTotal() function to sum all output amounts for the day
  - Place helper functions after ctrl class with comment "// Complements"
  - _Requirements: 1.6_

- [x] 4. Implement Frontend - Main App Class (almacen.js)

- [ ] 4.1 Create base App class structure
  - Extend Templates class from CoffeeSoft framework
  - Define constructor with link (API URL) and div_modulo ("root") parameters
  - Set PROJECT_NAME to "almacen"
  - Initialize api variable pointing to 'finanzas/captura/ctrl/ctrl-almacen.php'
  - Create global variables: app, almacenDashboard, concentrado
  - _Requirements: 1.1, 4.5, 4.6_

- [x] 4.2 Implement main render and layout methods

  - Create render() method to call layout(), filterBar(), and lsOutputs()
  - Create layout() method using primaryLayout with parent "root", id PROJECT_NAME
  - Add tabLayout with tabs: "dashboard" (Dashboard), "outputs" (Salidas de almacén), "concentrado" (Concentrado de almacén)
  - Set "dashboard" tab as active by default
  - Configure tab onClick handlers to call respective class methods
  - Generate containers: container-dashboard, container-outputs, container-concentrado
  - _Requirements: 1.1, 1.2, 4.5, 4.6_


- [ ] 4.3 Implement filterBar for date selection
  - Create filterBar() method using createfilterBar component
  - Add input-calendar field with id "dateAlmacen" for date selection
  - Initialize dataPicker with parent "dateAlmacen" and onSelect callback to refresh lsOutputs()
  - Set default date to current date
  - _Requirements: 1.5_

- [ ] 4.4 Implement warehouse outputs table listing
  - Create lsOutputs() method using createTable component

  - Set parent to "container-outputs", idFilterBar to "filterBaralmacen"
  - Pass data with opc "ls" and date from getDataRangePicker("dateAlmacen")
  - Configure coffeesoft: true, datatable: false, pag: 15
  - Set table attributes: id "tbOutputs", theme "corporativo", title "Salidas de almacén del día"
  - Configure center alignment for columns 1, 2 and right alignment for column 3 (Monto)
  - Add success callback to update daily total display

  - _Requirements: 1.4, 1.5, 1.6_

- [ ] 4.5 Implement add warehouse output form
  - Create addOutput() method using createModalForm component
  - Set bootbox title to "Nueva salida de almacén"

  - Define json form fields: select for warehouse_id (populated with supply items), input type "cifra" for amount with validation, textarea for description

  - Mark all fields as required
  - Set data with opc "addOutput"
  - Add success callback to show alert and refresh lsOutputs() on status 200
  - _Requirements: 2.1, 2.3, 2.4, 2.6_


- [ ] 4.6 Implement edit warehouse output form
  - Create editOutput(id) async method
  - Use useFetch to call getOutput with opc "getOutput" and id parameter
  - Create createModalForm with bootbox title "Editar salida de almacén"
  - Use autofill with retrieved output data
  - Define same json form fields as addOutput

  - Set data with opc "editOutput" and id parameter
  - Add success callback to show alert and refresh lsOutputs() on status 200
  - _Requirements: 2.5, 2.6_

- [x] 4.7 Implement delete warehouse output confirmation

  - Create deleteOutput(id) method using swalQuestion component
  - Set opts with title "¿Está seguro?", text explaining deletion will be logged, icon "warning"
  - Set data with opc "deleteOutput" and id parameter
  - Add methods.send callback to show success alert and refresh lsOutputs()
  - _Requirements: 3.1, 3.2, 3.4_

- [x] 4.8 Implement view description modal


  - Create viewDescription(id, description) method

  - Use bootbox.alert to display modal with description text
  - Set modal title to "Descripción de la salida"
  - Format description text with proper line breaks
  - _Requirements: 3.3_


- [ ] 5. Implement Frontend - AlmacenDashboard Class
- [ ] 5.1 Create AlmacenDashboard class structure
  - Extend Templates class from CoffeeSoft framework
  - Define constructor with link and div_modulo parameters
  - Set PROJECT_NAME to "almacenDashboard"
  - _Requirements: 1.2, 1.6_

- [x] 5.2 Implement dashboard render method

  - Create renderDashboard() method to display dashboard view in "container-dashboard"
  - Add header section with title "Salidas de almacén" and subtitle with current date
  - Create card to display total outputs with green background (#8CC63F)
  - Format total using formatPrice() function
  - _Requirements: 1.2, 1.6_

- [ ] 5.3 Implement dashboard action buttons
  - Add three action buttons in dashboard: "Concentrado de almacén" (onClick to show concentrado tab), "Subir archivos de almacén" (onClick to open uploadFileModal), "Registrar nueva salida de almacén" (onClick to call app.addOutput())

  - Style buttons with TailwindCSS classes for consistent appearance
  - Add icons to buttons for better UX
  - _Requirements: 1.3_

- [ ] 5.4 Implement file upload modal
  - Create uploadFileModal() method using bootbox.dialog
  - Add file input with accept attribute for allowed extensions (pdf, jpg, jpeg, png, xlsx, xls)

  - Add date input for operation_date
  - Validate file size does not exceed 20MB before upload
  - Use useFetch to call uploadFile with opc "uploadFile", file, and date
  - Show success or error alert based on response
  - _Requirements: 1.7_




- [ ] 6. Implement Frontend - Concentrado Class
- [ ] 6.1 Create Concentrado class structure
  - Extend Templates class from CoffeeSoft framework
  - Define constructor with link and div_modulo parameters
  - Set PROJECT_NAME to "concentrado"

  - _Requirements: 5.1_

- [ ] 6.2 Implement concentrado layout and filterBar
  - Create layout() method using primaryLayout with parent "container-concentrado"
  - Create filterBarConcentrado() method using createfilterBar component
  - Add input-calendar field for date range selection (fi, ff)




  - Add select field for UDN filter
  - Add button "Generar reporte" to call renderConcentrado()
  - Initialize dataPicker with type "all" for range selection
  - _Requirements: 5.2_

- [x] 6.3 Implement consolidated report table

  - Create renderConcentrado() method using createTable component
  - Get date range from getDataRangePicker and UDN from filter
  - Pass data with opc "getConcentrado", fi, ff, udn
  - Configure table with expandable rows for warehouse details
  - Apply color coding: green background for Entradas columns, orange background for Salidas columns
  - Display totals footer with general totals for inputs, outputs, and final balance

  - _Requirements: 5.1, 5.3, 5.5, 5.6, 5.7_

- [ ] 6.4 Implement Excel export functionality
  - Create exportToExcel() method to generate Excel file from consolidated report data
  - Use library (e.g., SheetJS) to create workbook with formatted data
  - Include headers, data rows, and totals in Excel file
  - Apply color formatting to match table display (green for inputs, orange for outputs)
  - Trigger download of generated Excel file with filename including date range
  - _Requirements: 5.4_

- [ ] 6.5 Implement expandable warehouse details
  - Create expandWarehouseDetails(warehouseId) method to show detailed movements
  - Use useFetch to call getWarehouseDetails with opc "getWarehouseDetails", warehouse_id, fi, ff
  - Display detailed movements in expandable row below warehouse summary
  - Show individual transactions with date, description, and amount
  - Calculate and display subtotals for the expanded section
  - _Requirements: 5.5_

- [ ] 7. Implement access level restrictions
- [ ] 7.1 Add access level validation in controller
  - Create getUserAccessLevel() method in controller to retrieve user's access level from session
  - Add access level validation in each controller method (init, ls, addOutput, editOutput, deleteOutput, getConcentrado, uploadFile)
  - Return status 403 with message "Acceso denegado" if user lacks required access level
  - Define access level requirements: Captura (1) for CRUD operations, Gerencia (2) for reports, Contabilidad (3) for read-only, Administración (4) for module management
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 7.2 Implement dynamic UI based on access level
  - Create getAccessLevel() method in frontend to retrieve user's access level
  - Modify render() method to conditionally display buttons and tabs based on access level
  - For Access Level 1 (Captura): show "Salidas de almacén" as module name, display add/edit/delete buttons, hide concentrado tab
  - For Access Level 2+ (Gerencia, Contabilidad, Administración): show "Almacén" as module name, display concentrado tab, hide add/edit/delete buttons for levels 3+
  - Add visual indicators for restricted actions (disabled state or hidden elements)
  - _Requirements: 4.5, 4.6, 4.7_

- [ ] 8. Implement module lock/unlock functionality
- [ ] 8.1 Create module lock methods in model
  - Create getModuleLockStatus() method to check if module is locked for current month
  - Create lockModule() method to insert record in module_unlock table with lock_date and lock_reason
  - Create unlockModule() method to update module_unlock record with unlock_date
  - Query monthly_module_lock table to get automatic lock time for the module
  - _Requirements: 4.4_

- [ ] 8.2 Create module lock methods in controller
  - Create checkModuleLock() method to validate if module is accessible based on lock status
  - Create lockModule() method receiving lock_reason from $_POST, validate user has Administración access level
  - Create unlockModule() method receiving unlock_reason from $_POST, validate user has Administración access level
  - Return status 200 on success or 403 if user lacks permission
  - _Requirements: 4.4_

- [ ] 8.3 Implement module lock UI
  - Add lock/unlock button in dashboard for Administración access level users
  - Create lockModuleModal() method to show confirmation dialog with reason input
  - Display lock status indicator in module header (locked icon with date)
  - Disable all input actions when module is locked (show read-only message)
  - Add unlock button with reason input for Administración users
  - _Requirements: 4.4_

- [ ] 9. Integration and testing
- [ ] 9.1 Test complete add warehouse output flow
  - Verify form validation for required fields
  - Test successful output creation with valid data
  - Verify table refreshes automatically after creation
  - Verify daily total updates correctly
  - Test error handling for invalid data (negative amount, empty fields)
  - _Requirements: 2.1, 2.3, 2.4, 2.6_

- [ ] 9.2 Test edit and delete warehouse output flow
  - Verify edit form loads with correct data
  - Test successful update with modified data
  - Verify delete confirmation modal displays
  - Test successful deletion and audit log entry creation
  - Verify table refreshes after edit/delete
  - _Requirements: 2.5, 2.6, 3.1, 3.2, 3.4_

- [ ] 9.3 Test consolidated report generation
  - Verify report generates with correct date range
  - Test balance calculations for accuracy (initial + inputs - outputs = final)
  - Verify color coding for inputs (green) and outputs (orange)
  - Test expandable warehouse details functionality
  - Verify Excel export generates correct file with formatted data
  - Test totals calculation accuracy
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7_

- [ ] 9.4 Test access level restrictions
  - Test each access level (Captura, Gerencia, Contabilidad, Administración) with different user accounts
  - Verify correct buttons and tabs display for each level
  - Test that restricted actions return 403 error
  - Verify module name changes based on access level
  - Test module lock/unlock functionality for Administración level
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7_

- [ ] 9.5 Test file upload functionality
  - Verify file size validation (reject files > 20MB)
  - Test file extension validation (accept only pdf, jpg, jpeg, png, xlsx, xls)
  - Verify successful file upload and database record creation
  - Test error handling for invalid files
  - Verify uploaded files are accessible and properly stored
  - _Requirements: 1.7_

- [ ] 9.6 Perform end-to-end testing scenarios
  - Test complete daily operations workflow: login, register outputs, edit, delete, view total
  - Test report generation workflow: login as gerencia, generate consolidated report, export to Excel
  - Test multi-UDN filtering: switch between UDNs, verify data changes
  - Test module lock workflow: lock module, verify read-only mode, unlock module
  - Verify audit log entries for all delete operations
  - _Requirements: All requirements_

- [ ] 9.7 Performance and load testing
  - Test table load time with 1000 records (target < 2 seconds)
  - Test form submission response time (target < 1 second)
  - Test consolidated report generation with 30-day range (target < 5 seconds)
  - Test Excel export with 1000 records (target < 10 seconds)
  - Verify database indexes improve query performance
  - Test concurrent user access (10+ simultaneous users)
  - _Requirements: All requirements_

- [ ] 10. Documentation and deployment
- [ ] 10.1 Create user documentation
  - Write user guide for Captura level: how to register, edit, delete outputs
  - Write user guide for Gerencia level: how to generate and export consolidated reports
  - Write user guide for Administración level: how to manage module lock/unlock
  - Create troubleshooting guide for common issues
  - Document file upload requirements and limitations
  - _Requirements: All requirements_

- [ ] 10.2 Prepare deployment package
  - Verify all files are in correct directory structure (finanzas/captura/almacen.js, ctrl/ctrl-almacen.php, mdl/mdl-almacen.php)
  - Create database migration script with table creation and index statements
  - Verify dependencies are included (CoffeeSoft framework files)
  - Create deployment checklist with configuration steps
  - Test deployment on staging environment
  - _Requirements: All requirements_

- [ ] 10.3 Production deployment
  - Execute database migration script on production database
  - Deploy frontend, controller, and model files to production server
  - Verify file permissions and directory structure
  - Test all functionality in production environment
  - Monitor error logs for any issues
  - Create backup of production database before deployment
  - _Requirements: All requirements_
