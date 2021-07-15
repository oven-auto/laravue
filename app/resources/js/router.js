import vueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(vueRouter);

import brandList from './components/brand/BrandListComponent';
import brandEdit from './components/brand/BrandEditComponent';

import deviceTypesList from './components/device/type/TypeListComponent';
import deviceTypesEdit from './components/device/type/TypeEditComponent';

import motorList from './components/motor/MotorListComponent';

const routes = [
    //////////
    //BRANDS//////////////////////////////////////////////////////////////////
    //////////
    {
        path: '/brands/list',
        component: brandList
    },
    {
        path: '/brands/create',
        component: brandEdit
    },
    {
        path: '/brands/edit/:id',
        component: brandEdit
    },
    ///////////////
    //DEVICE TYPE/////////////////////////////////////////////////////////////
    ///////////////
    {
        path: '/devicetypes/list',
        component: deviceTypesList
    },
    {
        path: '/devicetypes/create',
        component: deviceTypesEdit
    },
    {
        path: '/devicetypes/edit/:id',
        component: deviceTypesEdit
    },
    /////////////////
    //DEVICE FILTER////////////////////////////////////////////////////////////
    /////////////////
    {
        path: '/devicefilters/list',
        component: brandList
    },
    {
        path: '/devicefilters/create',
        component: brandEdit
    },
    {
        path: '/devicefilters/edit/:id',
        component: brandEdit
    },
    ///////////
    //DEVICES//////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/devices/list',
        component: brandList
    },
    {
        path: '/devices/create',
        component: brandEdit
    },
    {
        path: '/devices/edit/:id',
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
