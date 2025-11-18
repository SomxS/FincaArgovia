# Implementation Plan - Módulo de Costos

## Task List

- [x] 1. Setup project structure and database schema


  - Create directory structure for module files
  - Verify existing database tables (purchases, warehouse_output, product, product_class)
  - Add database indexes for performance optimization
  - _Requirements: 1.1, 5.1, 5.2, 6.3_




- [ ] 2. Implement backend model (mdl-costos.php)
- [ ] 2.1 Create base model structure
  - Extend CRUD class
  - Configure database connection ($this->bd)

  - Initialize utility class ($this->util)
  - _Requirements: 6.3, 6.4_

- [x] 2.2 Implement filter data methods

  - Code `lsUDN()` method to get business units list
  - Return data in format compatible with frontend selects
  - _Requirements: 3.1, 3.5_

- [ ] 2.3 Implement main query method
  - Code `listCostos()` method with date range and UDN parameters

  - Join purchases and warehouse_output tables
  - Group by product_class and date
  - Calculate daily totals and grand totals
  - _Requirements: 1.2, 5.1, 5.2, 5.3, 5.4_




- [ ] 2.4 Implement supporting query methods
  - Code `getCostosDirectos()` to query purchases module
  - Code `getSalidasAlmacen()` to query warehouse module
  - Use prepared statements with _Read() method

  - _Requirements: 5.1, 5.2, 7.5_

- [ ] 3. Implement backend controller (ctrl-costos.php)
- [ ] 3.1 Create base controller structure
  - Extend mdl class

  - Implement session validation
  - Add security headers
  - _Requirements: 4.2, 6.1_

- [ ] 3.2 Implement init() method
  - Load UDN list for filter
  - Return user level for conditional rendering

  - Format response for frontend consumption
  - _Requirements: 3.1, 3.2, 6.4_

- [ ] 3.3 Implement ls() method
  - Receive date range and UDN from POST
  - Validate user permissions for UDN filter
  - Call model's listCostos() method

  - Format data for table rendering (grouped rows, totals)
  - Return JSON response with row array
  - _Requirements: 1.2, 1.3, 3.3, 3.5, 5.5_

- [x] 3.4 Implement exportExcel() method



  - Validate date range and UDN parameters
  - Query data using listCostos()
  - Generate Excel file with PHPSpreadsheet
  - Include headers, filters applied, and formatted data
  - Return file download response

  - _Requirements: 2.2, 2.3, 2.4, 3.4_

- [ ] 3.5 Implement security validations
  - Validate allowed operations (init, ls, exportExcel only)
  - Check user authentication

  - Validate user level for UDN filter access
  - Sanitize all input parameters
  - _Requirements: 4.2, 4.4, 7.5_

- [ ] 4. Implement frontend module (costos.js)
- [x] 4.1 Create App class structure

  - Extend Templates class from CoffeeSoft
  - Define PROJECT_NAME = "costos"
  - Implement constructor with link and div_modulo parameters
  - _Requirements: 6.1, 6.5_

- [ ] 4.2 Implement render() method
  - Call layout() to create UI structure

  - Call filterBar() to setup filters
  - Call ls() to load initial data
  - _Requirements: 1.1, 1.5_

- [ ] 4.3 Implement layout() method
  - Use primaryLayout() component
  - Create filterBar section with ID
  - Create container section for table
  - Apply responsive classes
  - _Requirements: 1.1, 1.5, 6.5_


- [ ] 4.4 Implement filterBar() method
  - Use createfilterBar() component
  - Add date range picker (dataPicker)
  - Add UDN select (conditional based on user level from init())
  - Add "Exportar a Excel" button
  - Wire onchange events to ls() method

  - _Requirements: 1.2, 2.1, 3.1, 3.2, 7.1_

- [ ] 4.5 Implement ls() method
  - Get date range from dataPicker using getDataRangePicker()



  - Get UDN value from select (if visible)
  - Validate date range (start <= end, max 12 months)
  - Call backend with useFetch({ opc: 'ls', fi, ff, udn })
  - Use createTable() to render results
  - Configure table with theme 'corporativo', no action buttons (opc: 0)
  - Group rows by product_class

  - Display daily totals and grand totals
  - _Requirements: 1.2, 1.3, 1.4, 3.3, 4.1, 7.1, 7.2, 7.3_

- [ ] 4.6 Implement exportExcel() method
  - Validate date range is selected



  - Get current filter values (fi, ff, udn)
  - Call backend with useFetch({ opc: 'exportExcel', fi, ff, udn })
  - Handle file download response
  - Show success/error messages
  - _Requirements: 2.1, 2.2, 2.5, 3.4_


- [ ] 4.7 Implement read-only mode enforcement
  - Ensure no edit/delete buttons in table
  - Set opc: 0 in table configuration
  - Disable any modification actions
  - _Requirements: 1.4, 4.1, 4.3_




- [ ] 5. Implement data validation
- [ ] 5.1 Frontend validations
  - Validate date range (start <= end)

  - Validate max range (12 months)
  - Show descriptive error messages
  - Prevent invalid queries
  - _Requirements: 7.1, 7.2, 7.3_

- [ ] 5.2 Backend validations
  - Validate date format

  - Validate UDN exists in database
  - Validate user permissions for UDN filter
  - Sanitize all input parameters
  - _Requirements: 7.4, 7.5_




- [ ] 6. Implement error handling
- [ ] 6.1 Frontend error handling
  - Handle network errors
  - Handle backend error responses
  - Display user-friendly error messages

  - Log errors to console for debugging
  - _Requirements: 7.3_

- [ ] 6.2 Backend error handling
  - Return standardized error responses
  - Log security violations
  - Handle database query errors
  - Return appropriate HTTP status codes
  - _Requirements: 4.4, 7.5_

- [ ] 7. Implement Excel export functionality
- [ ] 7.1 Setup PHPSpreadsheet library
  - Install PHPSpreadsheet via Composer
  - Configure autoloader
  - _Requirements: 2.2_

- [ ] 7.2 Create Excel generation method
  - Format headers with applied filters




  - Add data rows with proper formatting
  - Apply currency formatting to amount columns
  - Add totals row
  - Set column widths
  - _Requirements: 2.2, 2.3, 2.4_


- [ ] 7.3 Implement file download
  - Set proper headers for Excel download
  - Generate unique filename with timestamp
  - Stream file to browser
  - Clean up temporary files
  - _Requirements: 2.5_


- [ ] 8. Implement permission-based features
- [ ] 8.1 Conditional UDN filter rendering
  - Check user level in init() response
  - Show UDN select only for level 3 users
  - Hide UDN select for level 2 users
  - _Requirements: 3.1, 3.2_

- [ ] 8.2 Backend permission validation
  - Validate user level before applying UDN filter
  - Return 403 error if unauthorized
  - Log unauthorized access attempts
  - _Requirements: 3.5, 4.4_

- [ ] 9. Optimize database queries
- [ ]* 9.1 Add database indexes
  - Create index on purchases.operation_date
  - Create index on purchases.udn_id
  - Create index on warehouse_output.operation_date
  - Create index on product.product_class_id
  - _Requirements: 5.5_

- [ ]* 9.2 Optimize SQL queries
  - Use LEFT JOIN for consolidation
  - Aggregate data in SQL (SUM, GROUP BY)
  - Limit result set to date range
  - _Requirements: 5.3, 5.4_

- [-] 10. Implement UI components and styling

- [ ] 10.1 Configure table theme
  - Use 'corporativo' theme from CoffeeSoft
  - Set title "Concentrado Diario de Costos"
  - Set subtitle with date range
  - Configure column alignment (center, right)
  - _Requirements: 1.5_



- [ ] 10.2 Style filter bar
  - Apply responsive grid layout
  - Style date picker component
  - Style UDN select dropdown
  - Style export button with icon
  - _Requirements: 1.1, 1.5_

- [ ] 10.3 Implement grouped table rows
  - Group rows by product_class
  - Add visual separators between groups
  - Highlight total rows
  - Apply alternating row colors
  - _Requirements: 1.3_

- [ ] 11. Integration with existing modules
- [ ] 11.1 Verify purchases module integration
  - Confirm purchases table structure
  - Test query for direct costs
  - Validate data consistency
  - _Requirements: 5.1_

- [ ] 11.2 Verify warehouse module integration
  - Confirm warehouse_output table structure
  - Test query for warehouse outputs
  - Validate data consistency
  - _Requirements: 5.2_

- [ ] 11.3 Test data consolidation
  - Verify JOIN operations work correctly
  - Validate totals calculation
  - Test with various date ranges
  - _Requirements: 5.3, 5.4, 5.5_

- [ ] 12. Create module entry point
- [ ] 12.1 Update navigation menu
  - Add "Costos" link in Finanzas section
  - Set proper permissions (level 2 and 3)
  - Configure route to costos module
  - _Requirements: 1.1_

- [ ] 12.2 Create index.php for module
  - Include CoffeeSoft framework files
  - Add module-specific scripts
  - Initialize App class
  - _Requirements: 6.2_

- [ ] 13. Documentation and deployment
- [ ]* 13.1 Create user documentation
  - Document how to use date range filter
  - Document UDN filter (for level 3 users)
  - Document Excel export feature
  - Include screenshots
  - _Requirements: 1.1, 2.1, 3.1_

- [ ]* 13.2 Create technical documentation
  - Document API endpoints
  - Document database schema
  - Document security considerations
  - Document deployment steps
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [ ] 13.3 Deploy to production
  - Upload files to server
  - Run database migrations (indexes)
  - Configure permissions
  - Test in production environment
  - _Requirements: All_
