import vueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(vueRouter);

import brandList from './components/brand/BrandListComponent';
import brandEdit from './components/brand/BrandEditComponent';
import motorList from './components/motor/MotorListComponent';

const routes = [
    {
        path: '/',
        component: brandList
    },
    {
        path: '/brands/list',
        component: brandList
    },
    {
        path: '/brands/:id',
        component: brandEdit
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
