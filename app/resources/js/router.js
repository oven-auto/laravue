import vueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(vueRouter);

import brandList from './components/brand/BrandListComponent';
import brandEdit from './components/brand/BrandEditComponent';
import brandCreate from './components/brand/BrandCreateComponent';

import motorList from './components/motor/MotorListComponent';

const routes = [

    {
        path: '/brands/list',
        component: brandList
    },
    {
        path: '/brands/create',
        component: brandCreate
    },
    {
        path: '/brands/:id',
        component: brandEdit
    },



    {
        path: '/',
        component: brandList
    },
    {
        path: '/motors/list',
        component: motorList
    }
];

export default new vueRouter({
    mode: "history",
    routes: routes
});
