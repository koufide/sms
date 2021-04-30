// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
var $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;
// global.$ = $;
// global.jQuery = $;


// the bootstrap module doesn't export/return anything
require('bootstrap');


// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');


require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');


// import('../css/bootstrap.min.css');
// import('../css/dashboard.css');
// import('../css/vireprel.css');

import '../css/global.scss';
// import '../css/custom.scss';


import Axios from 'axios';
window.Axios = Axios;

import Vue from 'vue';
window.Vue = Vue;

//https://stackoverflow.com/questions/45388795/uncaught-referenceerror-vue-is-not-defined-when-put-vue-setting-in-index-html


//les variables globables
Vue.config.productionTip = true; // false ## à modifier
Vue.options.delimiters = ['${', '}'];
// Vue.config.silent = true;
Vue.config.devtools = true;
// window.Vue.use(VuejsDialog.main.default);


import VuejsDialog from "vuejs-dialog";
// import VuejsDialogMixin from "vuejs-dialog/vuejs-dialog-mixin.min.js"; // only needed in custom components
import VuejsDialogMixin from "vuejs-dialog/dist/vuejs-dialog-mixin.min.js"; // only needed in custom components


// include the default style
// import 'vuejs-dialog/vuejs-dialog.min.css'
import 'vuejs-dialog/dist/vuejs-dialog.min.css'


// Tell Vue to install the plugin.
window.Vue.use(VuejsDialog);
// window.Vue.use(VuejsDialog.main.default);


var VueResource = require('vue-resource');
window.Vue.use(VueResource);

import 'select2'; // globally assign select2 fn to $ element
import 'select2/dist/css/select2.css'; // optional if you have css loader


//var dt = require('datatables.net');
// var dt = require('datatables.net-bs4'); //OK


require('jszip');
// require('pdfmake');
// require('script!pdfmake');
require('datatables.net-bs4');
require('datatables.net-autofill-bs4');
require('datatables.net-buttons-bs4');
require('datatables.net-buttons/js/buttons.colVis.js');
require('datatables.net-buttons/js/buttons.flash.js');
require('datatables.net-buttons/js/buttons.html5.js');
require('datatables.net-buttons/js/buttons.print.js');
require('datatables.net-colreorder-bs4');
require('datatables.net-fixedcolumns-bs4');
require('datatables.net-fixedheader-bs4');
require('datatables.net-keytable-bs4');
require('datatables.net-responsive-bs4');
require('datatables.net-rowgroup-bs4');
require('datatables.net-rowreorder-bs4');
require('datatables.net-scroller-bs4');
require('datatables.net-select-bs4');


//import dt from 'datatables.net-bs4';
// import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css'

// import 'datatables.net-dt/css/jquery.dataTables.css'; // this works //OK
import 'datatables.net-dt/css/jquery.dataTables.min.css'; // this works // OK




// ==============================================================================================
// import App2 from './components/App.vue'; 
// new Vue({
//     delimiters: ['${', '}'],
//     el: '#app2',
//     render: h => h(App2)
//     // data: {
//     //     message: 'Hello Vue !'
//     // }
// });

// new Vue({
//     delimiters: ['${', '}'],
//     el: '#app3',
//     data: {
//         message: 'Hello Vue  Fidelin!'
//     }
// });



//----------------------------------------
$(document).ready(function () {
    console.log("ready!");

    //$('#example').DataTable();

    $('#myTableOutgoing').DataTable();


    //================================================================================
    //======== abonnement
    var pos = 1;
    $('#towns_dt_table tfoot th').each(function () {
        var title = $(this).text();
        $(this).html("<input id='input" + pos + "' type='text' class='form-control' placeholder='' style='font-family:Arial, FontAwesome; width:100%'/>");
        pos++;
    });


    // var selected = [];

    const url_abonne = window.location.href + '/ajax';
    // console.log(url_abonne);

    var table_abonne = $('#towns_dt_table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excel', 'copy', 'pdf'
        ],
        "language": {
            url: '../json/French.json' // correspond à public/json/French.json
        },
        "columnDefs": [
            // These are the column name variables that will be sent to the server
            {
                "name": "phone",
                "targets": 0
            }, {
                "name": "client",
                "targets": 1
            }, {
                "name": "compte",
                "targets": 2
            }, {
                "name": "agence",
                "targets": 3
            }, {
                "name": "actif",
                "targets": 4
            },

        ],
        // Server-side parameters
        "processing": true,
        "serverSide": true,
        // Ajax call
        "ajax": {
            // "url": "{{ path('abonnement_list_ajax') }}",
            "url": url_abonne,
            "type": "POST"
        },
        // "rowCallback": function (row, data) {
        //     if ($.inArray(data.DT_RowId, selected) !== -1) {
        //         $(row).addClass('selected');
        //     }
        // },
        // Classic DataTables parameters
        "paging": true,
        "info": true,
        "searching": true,
        "pageLength": 10,
        "order": [
            [2, 'asc']
        ]
    });

    // Apply the search
    pos = 1;
    table_abonne.columns().every(function () {
        var that = this;
        $("#input" + pos).on('keyup change', function () {
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
        pos++;
    });
    //--------------------------------------------------------


    //---------- charger la liste des abonnements 
    $("#btnLoadAbonne").click(function () {
        // alert("Handler for .click() called.");

        // console.log(window.location);
        const url = window.location.href + '/load/ajax';
        // console.log(url);

        // var request = $.ajax({
        //     url: url,
        //     type: "POST"
        //     // data: { id: menuId },
        //     // dataType: "html"
        // });

        // request.done(function (msg) {
        //     // $("#log").html(msg);

        //     var response = $.parseJSON(msg);
        //     // console.log(response);

        //     // console.log(response.nbre_lignes);

        //     Vue.dialog.alert(response.nbre_lignes + ': lignes importées', {
        //         loader: false,
        //         okText: 'Fermer',
        //         cancelText: 'Non',
        //     })
        //         .then(function (dialog) {
        //             console.log('Fermer')
        //         });

        //     $('#towns_dt_table').DataTable().ajax.reload();

        // });


        Vue.dialog.confirm('Voulez vous continuer ?', {
            loader: true,
            okText: 'Oui',
            cancelText: 'Non'
        }).then(function (dialog) {

            setTimeout(() => {

                Vue.http.post(url).then(response => {

                    // get body data
                    var someData = response.body;
                    // console.log(someData);
                    dialog.close();

                    // var response = $.parseJSON(msg);
                    // console.log(response);

                    // console.log(response.nbre_lignes);

                    Vue.dialog.alert(someData.nbre_lignes + ': lignes chargées', {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });


                    $('#towns_dt_table').DataTable().ajax.reload();

                }, response => {
                    // error callback
                    //console.log(response);
                    dialog.close();
                    Vue.dialog.alert('ok: ' + response.ok + ', ' + response.status + ', ' + ' ' + response.statusText + ' ' + response.url, {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });
                })

            }, 2500); //setTimeout

        });//Dialog

    });//btn


    //-----nbre d abonnement
    const url = window.location.href + '/nbre/ajax';
    var request_nbre_abonne = $.ajax({
        url: url,
        type: "POST"
        // data: { id: menuId },
        // dataType: "html"
    });

    request_nbre_abonne.done(function (msg) {
        var response = $.parseJSON(msg);
        // console.log(response);
        $("#nbre_abonne").html('(' + response + ')');
        // console.log(response.nbre_lignes);()
    });

    //----------tout activer -- abonnement
    $("#btnActiverAbonne").click(function () {
        // alert("Handler for .click() called.");

        // console.log(window.location);
        const url = window.location.href + '/activer/all/ajax';
        // console.log(url);

        // var request_activer =
        // $.ajax({
        //     url: url,
        //     type: "POST"
        //     // data: { id: menuId },
        //     // dataType: "html"
        // });

        Vue.dialog.confirm('Voulez vous continuer ?', {
            loader: true,
            okText: 'Oui',
            cancelText: 'Non'
        }).then(function (dialog) {

            setTimeout(() => {

                Vue.http.post(url).then(response => {

                    // get body data
                    var someData = response.body;
                    // console.log(someData);
                    dialog.close();

                    $('#towns_dt_table').DataTable().ajax.reload();

                }, response => {
                    // error callback
                    //console.log(response);
                    dialog.close();
                    Vue.dialog.alert('ok: ' + response.ok + ', ' + response.status + ', ' + ' ' + response.statusText + ' ' + response.url, {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });
                })

            }, 2500); //setTimeout

        });//Dialog

        // request_activer.done(function (msg) {
        //     // $("#log").html(msg);

        //     var response = $.parseJSON(msg);
        //     // console.log(response);

        //     // console.log(response.nbre_lignes);

        //     Vue.dialog.alert(response.nbre_lignes + ': lignes importées', {
        //         loader: false,
        //         okText: 'Fermer',
        //         cancelText: 'Non',
        //     })
        //         .then(function (dialog) {
        //             console.log('Fermer')
        //         });


        //     });
        // $('#towns_dt_table').DataTable().ajax.reload();

    });//btn


    //----------tout desactiver -- abonnement
    $("#btnDesactiverAbonne").click(function () {
        // alert("Handler for .click() called.");

        // console.log(window.location);
        const url = window.location.href + '/desactiver/all/ajax';
        // console.log(url);


        Vue.dialog.confirm('Voulez vous continuer ?', {
            loader: true,
            okText: 'Oui',
            cancelText: 'Non'
        }).then(function (dialog) {

            setTimeout(() => {

                // $.ajax({
                //     url: url,
                //     type: "POST"
                // });
                // $('#towns_dt_table').DataTable().ajax.reload();


                Vue.http.post(url).then(response => {

                    // get body data
                    var someData = response.body;
                    // console.log(someData);
                    dialog.close();

                    $('#towns_dt_table').DataTable().ajax.reload();

                }, response => {
                    // error callback
                    //console.log(response);
                    dialog.close();
                    Vue.dialog.alert('ok: ' + response.ok + ', ' + response.status + ', ' + ' ' + response.statusText + ' ' + response.url, {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });
                })

            }, 2500); //setTimeout

        });//Dialog

    });//btn





    //------------supprimer une ligne -- abonnement
    $('#towns_dt_table tbody').on('click', 'tr', function () {
        // var id = this.id;
        // console.log(id)
        // var index = $.inArray(id, selected);
        // console.log(index);


        // if (index === -1) {
        //     selected.push(id);
        // } else {
        //     selected.splice(index, 1);
        // }

        // $(this).toggleClass('selected');

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }

        // table.row('.selected').remove().draw(false);
        // table.row($(this).parents('tr')).remove().draw();


    });

    //------------- supprimer un abonnement
    $('#btnSupprimerAbonne').click(function () { // console.log('supprimer');
        var rowData = table_abonne.row('.selected').data();

        // console.log(rowData);

        var id;

        if (typeof rowData === 'undefined') { // does not exist
            Vue.dialog.alert('Sélectionner une ligne', {
                loader: false,
                okText: 'Fermer',
                cancelText: 'Non'
            }).then(function (dialog) { // console.log('Fermer');
            });
        } else {
            var id = rowData[0];
            // Vue.dialog.confirm('Etes vous sûr de vouloir supprimerVoulez vous vraiment l utilisateur : ' + rowData[2] + ' ?', {
            Vue.dialog.confirm('Etes vous sûr de vouloir supprimer? : ' + rowData[2] + ' ?', {
                loader: true,
                okText: 'Oui',
                cancelText: 'Non'
            }).then(function (dialog) { // console.log('Clicked on proceed')


                setTimeout(() => {
                    // console.log('setTimeout');

                    const url = window.location.href + rowData[0] + '/supprimer/ajax';
                    // var url = window.location.href + rowData[0] + '/delete/ajax';
                    console.log(url);

                    // Vue.http.post(url).then(response => { // get status;
                    //     console.log(response);

                    //     // get status text

                    //     console.log(response.statusText);

                    //     // get 'Expires' header
                    //     console.log(response.headers.get('Expires'));;

                    //     // get body data
                    //     Vue.http.someData = response.body;

                    //     // get body data
                    //     var someData = response.body;
                    //     console.log(response.body);

                    table_abonne.row('.selected').remove().draw(false);

                    // Vue.dialog.alert(someData.result, {
                    //     loader: false,
                    //     okText: 'Fermer',
                    //     cancelText: 'Non'
                    // }).then(function (dialog) {
                    //     console.log('Fermer')
                    // });


                    // }, response => { // error callback
                    // });


                    // console.log('Delete action completed ');
                    dialog.close();
                }, 500); // setTimeout


            }).catch(function () { // console.log('Clicked on cancel')
                dialog.close();
            });

            // does exist
        }
        // ligne selectionnee


        // table.row('.selected').remove().draw(false);
    });






});
//----------------------------------------