var App = (function () {
  'use strict';

  App.dataTables = function( ){
    var fecha =  new Date().toLocaleString();
    var url = typeof urlSite != 'undefined' ? urlSite : "/";
   
    //We use this to apply style to certain elements/*
    $.extend( true, $.fn.dataTable.defaults, {
      dom:
        "<'row mai-datatable-header'<'col-sm-6'l><'col-sm-6'f>>" +
        "<'row mai-datatable-body'<'col-sm-12'tr>>" +
        "<'row mai-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    } );

    $.extend( $.fn.dataTable.ext.classes, {
      sFilterInput:  "form-control form-control-sm",
      sLengthSelect: "form-control form-control-sm",
    } );

    $("#table1").dataTable();

    //Remove search & paging dropdown
    $("#table2").dataTable({
      pageLength: 6,
      dom:  "<'row mai-datatable-body'<'col-sm-12'tr>>" +
            "<'row mai-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    });

    //Enable toolbar button functions
    $("#table3, #table4").dataTable({
    "responsive": false,
    // "ordering": true,
    "language": {
      "search": "Filtrar"
    },
    "autoWidth": false, 
    // "order": false,
    // "order": [[1, "asc"]],
    "bSort": true,    
		"columnDefs": [
        { responsivePriority: 1, targets: 0 },
        { responsivePriority: 2, targets: -1 }
        // { targets: 9, type:  'datetime', def: function () { return new Date() },
        // format: 'YYYY-MM-DD h:mm A'}
    ],
      paging: false,		
      buttons: [
                  {extend: 'excel', className: 'btn-secondary'},
                  {extend: 'copy', className: 'btn-secondary'},        
                   // {extend: 'pdf', className: 'btn-secondary'},
                  {extend: 'print', className: 'btn-secondary'}
                ],
      //"lengthMenu": [[6, 10, 25, 50, -1], [6, 10, 25, 50, "All"]],
	  // posición de filtrado
	  //dom: 'Bfrtip',
	  //"sDom": 'lftip',
      dom:  "<'row mai-datatable-header'<'col-sm-6'B><'col-sm-6 text-right'f>>" +
            "<'row mai-datatable-body'<'col-sm-12'tr>>" 
           // "<'row mai-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    });

    /// dashboard
    $("#tableHome").dataTable({
      //"responsive": false,
      // "ordering": true,
      "language": {
        "search": "Filtrar"
      },
      "autoWidth": false, 
      // "order": false,
      "order": [[1, "asc"]],
      "bSort": false,    
      "columnDefs": [
          // { responsivePriority: 1, targets: 0 },
          // { responsivePriority: 2, targets: -1 },
          { targets: 13, type:  'DATE', def: function () { return new Date() },
           format: 'd/m/Y h:mm A'}
      ],
        paging: false,		
       /* buttons: [
                 {extend: 'excel', className: 'btn-secondary', exportOptions: {
                    columns: [ 6,7, 8, 9, 10,11,12,13,14,15 ]}},
                  {extend: 'copy', className: 'btn-secondary', exportOptions: {
                    columns: [ 6,7, 8, 9, 10,11,12,13,14,15 ]}},  
                  {extend: 'print', className: 'btn-secondary', exportOptions: {
                    columns: [ 6,7, 8, 9, 10,11,12,13,14,15 ]}}
                  ],*/
                  buttons: {
                    dom: {
                      button: {
                        tag: 'button',
                        className: ''
                      }
                    },	
                    buttons: [
                            {extend: 'excel',text: 'Descargar reporte Excel', className: 'btn btn-space btn-secondary btn-sm hover ', exportOptions: {
                              columns: [6,7, 8, 9, 10,11,12,13,14,15,16,17 ]}},
                                {extend: 'copy',text: 'Copiar', className: 'btn btn-space btn-secondary btn-sm hover', exportOptions: {
                                  columns: [ 6,7, 8, 9, 10,11,12,13,14,15,16,17 ]}},
                              {extend: 'print',text: 'Imprimir', className: 'btn btn-space btn-secondary btn-sm hover', exportOptions: {
                                columns: [ 6,7, 8, 9, 10,11,12,13,14,15,16,17 ]}},
                              ],
                            },
       
         dom:  "<'row mai-datatable-header px-0'<'col-sm-6 px-0'B><'col-sm-6 text-right px-0'f>>" +
              "<'row mai-datatable-body'<'col-sm-12'tr>>" 
        // "<'row mai-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
      });

       /// dashboard
    $("#tableProfile").dataTable({
      //"responsive": false,
      // "ordering": true,
      "language": {
        "search": "Filtrar"
      },
      "autoWidth": false, 
      // "order": false,
      "order": [[1, "asc"]],
      "bSort": false,    
      "columnDefs": [
          // { responsivePriority: 1, targets: 0 },
          // { responsivePriority: 2, targets: -1 },
          { targets: 13, type:  'DATE', def: function () { return new Date() },
           format: 'd/m/Y h:mm A'}
      ],
        paging: false,	
        buttons: {
          dom: {
            button: {
              tag: 'button',
              className: ''
            }
          },	
          buttons: [
                  {extend: 'excel',text: 'Descargar reporte Excel', className: 'btn btn-space btn-secondary btn-sm hover ', exportOptions: {
                      columns: [ 5,6,7, 8, 9, 10,11,12,13,14,15,16,17,18 ]}},
                      {extend: 'copy',text: 'Copiar', className: 'btn btn-space btn-secondary btn-sm hover', exportOptions: {
                        columns: [  5,6,7, 8, 9, 10,11,12,13,14,15,16,17,18]}},  
                    {extend: 'print',text: 'Imprimir', className: 'btn btn-space btn-secondary btn-sm hover', exportOptions: {
                      columns: [  5,6,7, 8, 9, 10,11,12,13,14,15,16,17,18  ]},
                      title: 'R-FILE // Reporte de solicitudes: Impreso el día ' + fecha ,
                      customize: function ( win ) {
                        $(win.document.body)
                            .css( 'font-size', '10pt' )
                            .prepend(
                                '<img src="'+url+'assets/img/clinica/ricoh-logo-tagline.jpg" style="position:absolute; top:0; right:0; width: 230px;" />'
                            );
     
                        $(win.document.body).find( 'table' )
                            .addClass( 'compact' )
                            .css( 'font-size', 'inherit' );
                    }
                    }
                    ],
                  },
         dom:  "<'row mai-datatable-header px-0'<'col-sm-6 px-0'B><'col-sm-6 text-right px-0'f>>" +
              "<'row mai-datatable-body'<'col-sm-12'tr>>" 
        // "<'row mai-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
      });
    
      // $(".dt-buttons a").removeClass("");

  };
 
  return App;
})(App || {});
