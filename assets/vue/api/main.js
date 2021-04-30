///var/www/html/sf42w/assets/vue/api
import Vue from 'vue';
import App from '../components/App'

const app = new Vue({
    el: '#app',
    template: '<App/>',
    components: { App },
    // data: {
    //     message: 'le test de depart'
    // }

});
//.$mount('#app');

// app.$mount('#app');


var app2 = new Vue({
    el: '#app2',
    data: {
        message: 'Hello Vue !'
    }
});


// import axios from 'axios';

// // Performing a GET request
// axios.get("{{ path('role_new') }}")
//     .then(function (response) {
//         console.log(response.data); // ex.: { user: 'Your User'}
//         console.log(response.status); // ex.: 200
//     });

// // Performing a POST request
// axios.post("{{ path('role_show') }}", { firstName: 'Marlon', lastName: 'Bernardes' })
//     .then(function (response) {
//         console.log('saved successfully')
//     }); 