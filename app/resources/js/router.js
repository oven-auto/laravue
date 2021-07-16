import vueRouter from 'vue-router';
import Vue from 'vue';

Vue.use(vueRouter);

import brandList from './components/brand/BrandListComponent';
import brandEdit from './components/brand/BrandEditComponent';

import deviceTypesList from './components/device/type/TypeListComponent';
import deviceTypesEdit from './components/device/type/TypeEditComponent';

import deviceFilterList from './components/device/filter/FilterListComponent';
import deviceFilterEdit from './components/device/filter/FilterEditComponent';

import deviceList from './components/device/DeviceListComponent';
import deviceEdit from './components/device/DeviceEditComponent';

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
        component: deviceFilterList
    },
    {
        path: '/devicefilters/create',
        component: deviceFilterEdit
    },
    {
        path: '/devicefilters/edit/:id',
        component: deviceFilterEdit
    },
    ///////////
    //DEVICES//////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/devices/list',
        component: deviceList
    },
    {
        path: '/devices/create',
        component: deviceEdit
    },
    {
        path: '/devices/edit/:id',
        component: deviceEdit
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
