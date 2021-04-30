/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

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


//===========================================================================================


import Axios from 'axios';
window.Axios = Axios;

import Vue from 'vue';
window.Vue = Vue;
//https://stackoverflow.com/questions/45388795/uncaught-referenceerror-vue-is-not-defined-when-put-vue-setting-in-index-html

// import App from './App'

//les variables gloables
Vue.config.productionTip = false;
Vue.options.delimiters = ['${', '}'];
// window.Vue.use(VuejsDialog.main.default);
//------------------------------------------------------


//------------------------------------------------------
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


// import DatatableFactory from 'vuejs-datatable.js';
// window.Vue.use(DatatableFactory);

//===========================================================================================

import 'select2';                       // globally assign select2 fn to $ element
import 'select2/dist/css/select2.css';  // optional if you have css loader

// $(() => {
// $('#abonnement_compte').select2();
// });

//===========================================================================================

// const app = new Vue({
//     el: '#app',
//     data: {
//         message: 'ceci un bon test1'
//     }
// });


// require('select2');
// $('select').select2();



// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');


// // var log = require('./log.js');
// import { log, log2 } from './log.js';
// log("salut4");
// log2("salut koufide");

// import { log, log2 } from '../vue/api/main.js';


// import Example from '../vue/components/App';
// new Vue({
//     el: '#app4',
//     components: { Example }
// });


// import App from '../vue/components/App'
// new Vue({
//     el: '#app',
//     template: '<App/>',
//     components: { App }
// })

// import { main } from '../vue/api/main.js';

//https://blog.dev-web.io/2018/01/11/symfony-4-utiliser-vue-js/





//############################################################################################
//var dt = require('datatables.net')(window, $);
// var dt = require('datatables.net-dt');
// var dt = require('datatables.net-dt');
//var dt = require('datatables.net');
var dt = require('datatables.net-bs4');    
//var dt = require('datatables.net-bs4')($);

// import dt from 'datatables.net-bs4';
// dt(window, $);
// import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css'

//import dt from 'datatables.net-bs';
//dt(window, global.$);

// import dt from 'datatables.net-dt';
// import 'datatables.net-dt/css/jquery.dataTables.css'; // this works
//import 'datatables.net-dt/css/jquery.dataTables.min.css'; // this works



 




//############################################################################################

// import App2 from './components/App.vue'; 
//############################################################################################

$(document).ready(function () {







    // LES DATATABLES
    var myTableCompte2 = $('#myTableCompte2').DataTable({
        "stateSave": true,
        "language": {
            url: '../json/French.json' //correspond à public/json/French.json
        },
        responsive: true,
    });

    var myTableOutgoing = $('#myTableOutgoing').DataTable({
        "stateSave": false,
        "language": {
            url: '../json/French.json' //correspond à public/json/French.json
        },
        responsive: true,
        "order": [[0, "desc"]],
        "scrollX": true
    });


    // var myDataTableAbonnenements = $('#myDataTableAbonnenements').DataTable({
    //     "stateSave": false,
    //     "language": {
    //         url: '../json/French.json' //correspond à public/json/French.json
    //     },
    //     responsive: true,
    //     "order": [[0, "desc"]],
    //     "scrollX": false,
    //     // "processing": true,
    //     // "serverSide": true,
    //     // "ajax": "scripts/server_processing.php"
    // });

    //http://192.168.56.102:8000/abonnement/list/ajax


    //==============================================================================================
    //import App2 from './components/App.vue';
    //import Vue from 'vue';
    // new Vue({
    //     el: '#app2',
    //     render: h => h(App2)
    // });
    //==============================================================================================


    //-------------------------------------------------------------------------
    //---------- selectionner un compte


    $('#abonnement_compte').hide();//cacher le select box

    $('#myTableCompte2 tbody').on('click', 'tr', function name(params) {
        //console.log('myTableCompte2 tbody TR');



        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        } else {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }


        if ($(this).hasClass('selected')) {

            var rowDataTableCompte2 = myTableCompte2.row('.selected').data();
            // var id = rowData[0];
            var id;

            if (typeof rowDataTableCompte2 == 'undefined') {
                console.log(typeof rowDataTableCompte2);
            } else {
                var id = rowDataTableCompte2[0];
                console.log(' rowdata: ' + id);

                var location = window.location;
                console.log(location);

                // var url = location.origin + location.pathname;
                // console.log(url);

                // url = url + '/select/compte/ajax';
                // console.log(url);

                // url = location.origin + '/compte/' + id + '/select/ajax';
                var url = location.origin + '/compte/' + id + '/select/ajax';
                console.log(url);

                Vue.http.get(url).then(response => {

                    // get body data
                    var someData = response.body;
                    console.log(someData);
                    console.log(someData.id);

                    // $('#abonnement_compte').val(someData.COMPTE);

                    var text1 = someData.COMPTE;
                    $("#abonnement_compte  option").filter(function () {
                        //may want to use $.trim in here
                        // alert($(this).text());
                        return $(this).text() == text1;
                    }).prop('selected', true);

                    $("#abonnement_compte option[value=" + text1 + "]").removeAttr('disabled');

                    $("input#abonnement_compte_text").val(text1);

                    $('#idInfoClient').html('<div class="row">\
                            <div class="col"> Indice: '+ someData.CLIENT + '</div>\
                            <div class="col">Nom: '+ someData.NOMCLIENT + '</div>\
                            <div class="col">Tel: '+ someData.TEL + '</div>\
                            <div class="col">RM: '+ someData.NOMGES + '</div>\
                        </div>');




                }, response => {
                    // error callback
                    //console.log(response);
                    // dialog.close();
                    Vue.dialog.alert('ok: ' + response.ok + ', ' + response.status + ', ' + ' ' + response.statusText + ' ' + response.url, {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });
                })

            }

        }//if ($(this).hasClass('selected')) {


        //fermer la boite de dialog
        $('#myModalCompte').modal('hide');
        $('#nouvAbonneCompteModal').modal('hide');



    });//$('#myTableCompte2 tbody').on('click', 'tr', function name(params) {




    // $('#idFormNouvAbonne').on('click', 'button', function (event) {
    //     event.preventDefault();
    //     alert('alert');
    // });



    //------------------------------------------------------------------
    //---------  soumettre le form de creation d un nouvel abonnement
    $('#idFormNouvAbonne').submit(function (event) {

        $('span#idRetourAjax').html('');//vider le champ

        $('#nouvAbonneModal').modal('hide');

        event.preventDefault();
        // console.log('submit');

        // var url = "{{ path('abonnement_new_ajax') }}";
        var url = window.location.origin + window.location.pathname + 'new/ajax';
        console.log(url);
        var request_method = $(this).attr("method"); //get form GET/POST method
        var formSerialize = $(this).serialize();
        console.log(formSerialize);


        $.ajax({
            url: url,
            type: request_method,
            data: formSerialize
        }).done(function (data) { //
            //$("#server-results").html(response);
            console.log(data);

            var response = $.parseJSON(data);
            console.log(response);

            console.log(response.id)
            ////myDataTable.ajax.reload();

            if (response.status == 'OK') {

                var t = $('.myDataTable').DataTable();
                t.row.add([
                    response.id,
                    response.phone,
                    response.statut,
                    response.detail,
                    response.motif,
                    response.suppr
                ]).draw(false);

            } else {

                $('span#idRetourAjax').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Erreur!</strong>' + response.message + '</div>');

            }//response ok

            // var counter = 1;

            // t.row.add([
            //     25,
            //     counter + '.2',
            //     counter + '.3',
            //     counter + '.4',
            //     counter + '.5',
            //     counter + '.6'
            // ]).draw(false);

            // counter++;




        });

        //var table = $('#myTableAbonne').DataTable();
        //table.fnDraw();

        // $.post(url, formSerialize, function (response) {
        //     //your callback here
        //     alert(response);
        // }, 'JSON');

        // Vue.http.post(url, formSerialize).then(response => {
        //     // Vue.http.post(url, formData).then(response => {
        //     var someData = response.body;
        //     console.log(someData);
        // }, response => {
        //     console.log(response.body);
        // });


    });





    // function selectCompte2() {  
    $('#myTableCompte tbody').on('click', 'tr', function () {
        // $(this).toggleClass('selected');

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');

            var rowData = myTableCompte.row('.selected').data();
            // var id = rowData[0];
            var id;

            if (typeof rowData == 'undefined') {
                console.log(typeof rowData);
            } else {
                var id = rowData[0];
                console.log(' rowdata: ' + id);

                var location = window.location;
                console.log(location);

                // var url = location.origin + location.pathname;
                // console.log(url);

                // url = url + '/select/compte/ajax';
                // console.log(url);

                // url = location.origin + '/compte/' + id + '/select/ajax';
                var url = location.origin + '/compte/' + id + '/select/ajax';
                console.log(url);

                Vue.http.get(url).then(response => {

                    // get body data
                    var someData = response.body;
                    console.log(someData);
                    console.log(someData.id);

                    // $('#abonnement_compte').val(someData.COMPTE);

                    var text1 = someData.COMPTE;
                    $("#abonnement_compte  option").filter(function () {
                        //may want to use $.trim in here
                        // alert($(this).text());
                        return $(this).text() == text1;
                    }).prop('selected', true);

                    $("#abonnement_compte option[value=" + text1 + "]").removeAttr('disabled');

                    $("input#abonnement_compte_text").val(text1);

                    $('#idInfoClient').html('<div class="row">\
                            <div class="col"> Indice: '+ someData.CLIENT + '</div>\
                            <div class="col">Nom: '+ someData.NOMCLIENT + '</div>\
                            <div class="col">Tel: '+ someData.TEL + '</div>\
                            <div class="col">RM: '+ someData.NOMGES + '</div>\
                        </div>');




                }, response => {
                    // error callback
                    //console.log(response);
                    // dialog.close();
                    Vue.dialog.alert('ok: ' + response.ok + ', ' + response.status + ', ' + ' ' + response.statusText + ' ' + response.url, {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });
                })

            }

            //fermer la boite de dialog
            $('#myModalCompte').modal('hide');
            $('#nouvAbonneCompteModal').modal('hide');

        }
    });
    // }//selectCompte2


    //--------------------------------------------------------------------



    $('#btnRechargerCompte').click(function () {
        //alert("recharger");

        // var url = window.location.href + rowData[0] + '/delete/ajax';
        // var url = window.location.href;
        var url = window.location.href + 'recharger/ajax';
        console.log(url);

        Vue.dialog.confirm('Voulez vous vraimment importer les comptes ?', {
            loader: true,
            okText: 'Oui',
            cancelText: 'Non'
        }).then(function (dialog) {

            setTimeout(() => {

                //var url = "{{ path('compte_recharger_ajax') }}";
                Vue.http.post(url).then(response => {

                    // get body data
                    var someData = response.body;
                    console.log(someData);
                    //app.results = response.data;
                    //console.log(app.results);

                    //app.message = 'fin test';

                    //console.log('Delete action completed ');
                    dialog.close();

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

        });


    });//btnRechargerCompte




    $('[data-toggle="popover"]').popover();

    //datatble default
    // $.extend(true, $.fn.dataTable.defaults, {
    //     "searching": false,
    //     "ordering": false
    // });


    // console.log('datatable');

    // $('.myTable').dataTable();
    const table = $('.mytable').DataTable({
        // paging: false,
        // searching: false,
        // ordering: false,
        language: {
            url: '../json/French.json' //correspond à public/json/French.json
        }
    });


    $('#myTable tbody').on('click', 'tr', function () {
        // console.log('on click myTable');
        // console.log($(this));
        // const myTable = $('#myTable tbody tr');
        //const myTable = $(this);
        // console.log(myTable);

        //const table = $('table');

        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
        }
        else {
            $('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }

    });

    $('#supprimer').click(function () {
        //alert("supprimer")
        // Trigger an Alert dialog
        // windows.Vue.dialog.alert('Request completed!')
        //     .then(function (dialog) {
        //         console.log('Closed')
        //     });

        var rowData = table.row('.selected').data();
        // var id = rowData[0];
        var id;

        if (typeof rowData === 'undefined') {
            // does not exist
            Vue.dialog.alert('Sélectionner un utilisateur', {
                loader: false,
                okText: 'Fermer',
                cancelText: 'Non',
            })
                .then(function (dialog) {
                    console.log('Fermer')
                });
        }
        else {
            var id = rowData[0];
            // Vue.dialog.confirm('Etes vous sûr de vouloir supprimerVoulez vous vraiment l utilisateur : ' + rowData[2] + ' ?', {
            Vue.dialog.confirm('Etes vous sûr de vouloir supprimer? : ' + rowData[2] + ' ?', {
                loader: true,
                okText: 'Oui',
                cancelText: 'Non',
            })
                .then(function (dialog) {
                    console.log('Clicked on proceed')


                    setTimeout(() => {
                        console.log('setTimeout');

                        // var url = "{{path('user_delete_ajax',{'id': 'id_user'})}}";
                        // var url = '/user/id_user/delete/ajax';
                        // console.log(url);
                        // url = url.replace("id_user", id);
                        // console.log(url);

                        // var origin = window.location.origin;
                        // console.log(origin);

                        // url = origin + url;
                        // console.log(url);

                        var url = window.location.href + rowData[0] + '/delete/ajax';
                        console.log(url);



                        // app.$http.get(url).then(response => {
                        Vue.http.post(url).then(response => {

                            // get status
                            ;
                            console.log(response);

                            // get status text

                            console.log(response.statusText);

                            // get 'Expires' header
                            console.log(response.headers.get('Expires'));
                            ;

                            // get body data
                            Vue.http.someData = response.body;

                            // get body data
                            var someData = response.body;
                            console.log(response.body);

                            table.row('.selected').remove().draw(false);

                            Vue.dialog.alert(someData.result, {
                                loader: false,
                                okText: 'Fermer',
                                cancelText: 'Non',
                            })
                                .then(function (dialog) {
                                    console.log('Fermer')
                                });


                        }, response => {
                            // error callback
                        });


                        //console.log('Delete action completed ');
                        dialog.close();
                    }, 500); //setTimeout


                })
                .catch(function () {
                    console.log('Clicked on cancel')
                    dialog.close();
                });

            // does exist
        }//ligne selectionnee

    });//supprimer



    //------------------------------------------
    //---- message
    // console.log('debut');
    const table_message = $('#mytable_message').DataTable({
        stateSave: true,
        // 'paging': false, 
        // 'ordering': false,

        // "acolumnDefs": [
        //     {
        //         // The `data` parameter refers to the data for the cell (defined by the
        //         // `data` option, which defaults to the column being worked with, in
        //         // this case `data: 0`.
        //         "render": function (data, type, row) {
        //             return data + ' (' + row[2] + ')';
        //         },
        //         "atargets": 1
        //     },
        //     { "visible": false, "targets": [0] }
        // ],

        "dom": '<"toolbar">frtip',

        "createdRow": function (row, data, index) {
            if (data[5] == "No") {
                // console.log(data);
                $('td', row).eq(5).addClass('text-primary');
            }
        },

        "aoColumnDefs": [{
            "aTargets": [0],
            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                // console.log('test');
                // console.log(nTd);
                // console.log(sData);
                // console.log(oData);

                // console.log(iRow);
                // console.log(iCol);
                // if (sData == "3") {
                //     $(nTd).css('color', 'blue')
                // }
                if (sData == '0') {
                    $(nTd).html("<a href='" + sData + '/show' + "' >" + sData + "</a>");
                }
                if (sData == '2') {
                    $(nTd).html("<a href='" + sData + '/show/ajax' + "' >" + sData + "</a>");
                }
            },
            "render": function (data, type, row) {
                // console.log(data);
                // console.log(type);
                // console.log(row);
                // return data + ' (' + row[2] + ')';
                // return "<a href='#'  v-on:click='detailmessage' >" + data + "</a>";
                // return "<button class='btn btn-primary detailmessage' v-on:click='detailmessage()'>" + data + "</button>";
                // return "<button class='btn btn-default detailmessage' v-on:click='detailmessage()'>" + data + "</button>";
                return "<button class='btn btn-default detailmessage' v-on:click='detailmessage()'>" + data + "</button>";
                // return data;
            },
        }],
        language: {
            url: '../json/French.json', //correspond à public/json/French.json 

        }
    });//datable
    $("div.toolbar").html('<div class=row> test</div>');
    // console.log('fin');

    $('#mytable_message').on('click', 'tbody button.detailmessage', function () {

        //get textContent of the TD
        console.log('TD cell textContent : ', this.textContent)

        //get the value of the TD using the API 
        // console.log('value by API : ', table.cell({ row: this.parentNode.rowIndex, column: this.cellIndex }).data());

        app_message.detailmessage();
    });

    // $('#mytable_message').on('click', 'tbody tr', function () {
    //     console.log('API row values : ', table.row(this).data());
    // })


    //----------------------------------------------------------------------


    // const myTableCompte = $('#myTableCompte').DataTable({
    //     "info": false,
    //     "stateSave": true,
    //     "language": {
    //         url: '../json/French.json' //correspond à public/json/French.json
    //     },
    //     responsive: true,
    // });


    //-------------------------------------------------------------------------------
    //------table des abonnements
    const myDataTable = $('.myDataTable').DataTable({
        "language": {
            url: '../json/French.json' //correspond à public/json/French.json
        },
        "info": true,
        "stateSave": false,
        "responsive": true,
        "paging": false,
        "searching": true,
        "ordering": false,
    });

    //-------------------------------------------------------------

    // $('.myDataTable tbody').on('click', 'td', function () {
    //     alert('Clicked on cell in visible column: ' + myDataTable.cell(this).index().columnVisible);
    // });


    //-------click sur le bouton desactiver
    // $('.myDataTable tbody').on('click', 'a.btnDesac', function () {
    $('.myDataTable tbody').on('click', 'td.btnDesac', function () {
        // console.log('a.btnDesac');

        const colonne = myDataTable.cell(this).index().columnVisible;
        // alert('Clicked on cell in visible column: ' + myDataTable.cell(this).index().columnVisible);
        //alert('Clicked on cell in visible column: ' + myDataTable.cell(this).index().columnVisible);
        // console.log(colonne);


        var row = myDataTable.row($(this).parents('tr'));


        // var rowData = myDataTable.row('.selected').data();
        // var rowData = myDataTable.row().data();
        var rowData = row.data();
        // console.log(rowData);
        var id = rowData[0];
        //var id;

        const url = window.location.origin + window.location.pathname + id + '/desactiver/ajax';
        //console.log(url);


        Vue.dialog.confirm('Vous confirmez la désactivation?', {
            loader: true,
            okText: 'Oui',
            cancelText: 'Non'
        }).then(function (dialog) {
            setTimeout(() => {

                Vue.http.post(url).then(response => {
                    var retour = response.body;
                    // console.log(retour);

                    if (retour.result == 'NOK') {
                        // $('span#idRetourAjax').html(retour.message);
                        // $('span#idRetourAjax').text(retour.message);
                        $('span#idRetourAjax').html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Erreur!</strong>' + retour.message + '</div>');

                    } else {
                        // console.log('rafraichir');

                        var icone = '<a class="btn btnDesac" href="#"> <i class="fa fa-lock" aria-hidden="true"></i></a>';
                        if (retour.actif == '1') {
                            var icone = '<a class="btn btnDesac" href="#"> <i class="fa fa-unlock" aria-hidden="true"></i></a>';
                        }

                        // var temp = myDataTable.row(0).data();
                        var temp = row.data();
                        // temp[colonne] = 'Tom';//collone
                        temp[colonne] = icone;//collone
                        // $('#myTableAbonne').dataTable().fnUpdate(temp, 0, undefined, false);
                        //$('.myDataTable').dataTable().fnUpdate(temp, 2, undefined, false);

                        //## myDataTable.row(2).data(temp).invalidate();
                        row.data(temp).invalidate();

                    }

                    dialog.close();
                }, response => {

                    dialog.close();
                });

            }, 1500);//setTimeout
        });//vue.dialig.confirm



    });//click sur le bouton desactiver
    //------------------------------------------------------------------


    $('.myDataTable tbody').on('click', 'a.btnSupprimer', function () {

        var row = myDataTable.row($(this).parents('tr'));

        Vue.dialog.confirm('Vous confirmez la suppression?', {
            loader: true,
            okText: 'Oui',
            cancelText: 'Non'
        }).then(function (dialog) {

            setTimeout(() => {

                // var rowData = myDataTable.row('.selected').data();
                // var rowData = myDataTable.row().data();
                var rowData = row.data();
                // console.log(rowData);
                var id = rowData[0];
                //var id;

                const location = window.location;
                // console.log(location);
                const url = location.origin + location.pathname + id + '/delete/ajax';
                // console.log(url);




                Vue.http.post(url).then(response => {

                    // get body data
                    var someData = response.body;
                    console.log(someData);
                    // app_compte.message = someData;
                    // console.log(app_compte.message);
                    //app.results = response.data;
                    //console.log(app.results);

                    //app.message = 'fin test';

                    try {
                        if (someData.result == 'OK') {
                            row.remove();
                            myDataTable
                                // .row($(this).parents('tr'))
                                // .row($(this).parents('.myDataTable tbody tr'))
                                // .remove()
                                .draw();
                        }

                    } catch (error) {
                        console.log(error);
                    }





                    //console.log('Delete action completed ');
                    dialog.close();

                }, response => {
                    // error callback
                    //console.log(response);
                    dialog.close();
                    Vue.dialog.alert(response.ok + ', ' + response.status + ', ' + ' ' + response.statusText + ' ' + response.url, {
                        loader: false,
                        okText: 'Fermer',
                        cancelText: 'Non',
                    })
                        .then(function (dialog) {
                            console.log('Fermer')
                        });
                })

            }, 500); //setTimeout

        });//dialig confirm



    });

    //-------------------------------------------------------------------------------


    const app_abonnement = new Vue({
        el: '#app_abonnement',
        data: {
            message: 'test'
        },
        methods: {
            selectCompte(event) {
                event.preventDefault();
                // alert('app_abonnement selectCompte');
                // selectCompte2();

                // var rowData = myTableCompte.row('.selected').data();
                // // var id = rowData[0];
                // var id;

                // if (typeof rowData == 'undefined') {
                //     console.log(typeof rowData);
                // } else {
                //     var id = rowData[2];
                //     console.log(' rowdata: ' + id);

                //     var location = window.location;
                //     console.log(location);

                //     var url = location.origin + location.pathname;
                //     console.log(url);
                // }

            }
        }

    });


    // $('#myTableCompte tbody').on('click', 'tr', function () {
    //     var data = myTableCompte.row(this).data();
    //     const compte = data[2];
    //     // var url = window.location.href + 'selectionner/ajax';
    //     var url = window.location.href;
    //     console.log(url);
    //     url.replace('e', 'Y');
    //     console.log(url);
    //    // alert('You clicked on ' + data[2] + '\'s row');
    // });




    //------------------------------------------------------------------------------
    // $('#abonnement_compte').select2({
    // });



    // $('#button').click(function () {
    //     alert(myTableCompte.rows('.selected').data().length + ' row(s) selected');
    // });




    // const app_client = new Vue({
    //     el: '#app_client',
    //     data: {

    //     },
    //     methods: {
    //         selectCompte(event) {
    //             event.preventDefault();
    //             alert('app_select_compte');
    //         }
    //     }
    // });




    //-----------------recharger la liste des comptes---------------------

    const app_compte = new Vue({
        el: '#app_compte',
        data: {
            message: '',
        },
        beforeCreate() {
            console.log('beforeCreate');
        },
        created() {
            console.log('created');
        },
        methods: {
            fnRechargerCompte: function (event) {
                event.preventDefault();
                //alert('test');

                var url = window.location.href + 'recharger/ajax';
                console.log(url);

                Vue.dialog.confirm('Voulez vous vraimment importer les comptes ?', {
                    loader: true,
                    okText: 'Oui',
                    cancelText: 'Non'
                }).then(function (dialog) {

                    setTimeout(() => {

                        //var url = "{{ path('compte_recharger_ajax') }}";
                        Vue.http.post(url).then(response => {

                            // get body data
                            var someData = response.body;
                            //console.log(someData);
                            app_compte.message = someData;
                            console.log(app_compte.message);
                            //app.results = response.data;
                            //console.log(app.results);

                            //app.message = 'fin test';

                            //console.log('Delete action completed ');
                            dialog.close();

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

                });//dialig confirm


            }//fct
        },
    });

    //----------------------------------------------------------------------------------

    /*
    $('#nouvAbonneModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var recipient = button.data('whatever') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-title').text('New message to ' + recipient)
        modal.find('.modal-body input').val(recipient)
    });
    */

    $('form').on('click', 'select#abonnement_compte', function () {
        // console.log('onclik');
        $('#nouvAbonneCompteModal').modal('toggle');
        $('#nouvAbonneCompteModal').modal('show');
        // $('#nouvAbonneCompteModal').modal('hide');
    });

    $('form').on('click', 'input#abonnement_compte_text', function () {
        // console.log('onclik');
        $('#nouvAbonneCompteModal').modal('toggle');
        $('#nouvAbonneCompteModal').modal('show');
        // $('#nouvAbonneCompteModal').modal('hide');
    });
    // $('#abonnement_compte').slice(2);
    // $('#abonnement_compte').hide();







    //############################################"






});//ready









// $("#myModal .close").click();


// const app_client = new Vue({
//     el: '#app_client',
//     data: {
//         message: 'ceci un bon test1',
//         form: {
//             isdiffere: false
//         }
//     },
//     methods: {
//         selectionner: function () {
//             alert('selectionner');
//         }
//     },
// });


// const lecontainer = new Vue({
//     el: '.lecontainer',
//     data: {
//         columns: [
//             { label: 'ID', field: 'id', align: 'center', filterable: false },
//             { label: 'Username', field: 'user.username' },
//             { label: 'First Name', field: 'user.first_name' },
//             { label: 'Last Name', field: 'user.last_name' },
//             { label: 'Email', field: 'user.email', align: 'right', sortable: false },
//             {
//                 label: 'Address', representedAs: function (row) {
//                     return row.address + ', ' + row.city + ', ' + row.state;
//                 }, align: 'right', sortable: false
//             },
//         ],
//         rows: window.rows
//     }
// });


// $('#myTableCompte tbody tr').mouseover(function () {
//     //console.log('mouseover');
//     //$(this).html('<tr><td>fidelin</td></tr>');
//     // $('#myTableCompte tbody tr td a').html(' <i class="fa fa-square" aria-hidden="true"></i>');
// });
// $('#myTableCompte tbody tr').mouseout(function () {
//     // console.log('mouseout');
//     // $('#myTableCompte tbody tr td a').html(' <i class="fa fa-check-square" aria-hidden="true"></i>');
// });



//############################################"


// const app_message = new Vue({
//     el: '#app_message',
//     data: {
//         message: 'ceci un bon test1',
//         form: {
//             isdiffere: false
//         }
//     },
//     methods: {
//         detailmessage: function () {
//             alert('test');
//         }
//     },
// });

//===========================================================================================

// Définit un nouveau composant appelé todo-item
// Vue.component('todo-item', {
//     props: ['todo'],
//     template: '<span>${ todo.text }</span>'
// });

// var app_compte2 = new Vue({
//     el: '#app_compte',
    // beforeMount() {
    //     console.log('beforeMount');
    // },
    //     data: {
    //         a: 1
    //     },
    //     beforeCreated: function () {
    //         console.log('beforeCreated')
    //     },
    //     created: function () {
    //         // `this` est une référence à l'instance de vm
    //         console.log('created')
    //     }
// })
