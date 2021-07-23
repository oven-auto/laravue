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

import propertyList from './components/property/PropertyListComponent';
import propertyEdit from './components/property/PropertyEditComponent';

import motorTypeList from './components/motor/type/TypeListComponent';
import motorTypeEdit from './components/motor/type/TypeEditComponent';

import motorTransmissionList from './components/motor/transmission/TransmissionListComponent';
import motorTransmissionEdit from './components/motor/transmission/TransmissionEditComponent';

import motorDriverList from './components/motor/driver/DriverListComponent';
import motorDriverEdit from './components/motor/driver/DriverEditComponent';

import motorList from './components/motor/MotorListComponent';
import motorEdit from './components/motor/MotorEditComponent';

import colorList from './components/color/ColorListComponent';
import colorEdit from './components/color/ColorEditComponent';

import packList from './components/pack/PackListComponent';
import packEdit from './components/pack/PackEditComponent';

const routes = [
    {
        path: '/',
        component: brandList
    },

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
    ///////////
    //PROPERTIES//////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/properties/list',
        component: propertyList
    },
    {
        path: '/properties/create',
        component: propertyEdit
    },
    {
        path: '/properties/edit/:id',
        component: propertyEdit
    },
    ///////////
    //MOTOR TYPES//////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/motortypes/list',
        component: motorTypeList
    },
    {
        path: '/motortypes/create',
        component: motorTypeEdit
    },
    {
        path: '/motortypes/edit/:id',
        component: motorTypeEdit
    },
    ///////////
    //MOTOR TRANSMISSION//////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/motortransmissions/list',
        component: motorTransmissionList
    },
    {
        path: '/motortransmissions/create',
        component: motorTransmissionEdit
    },
    {
        path: '/motortransmissions/edit/:id',
        component: motorTransmissionEdit
    },
    ///////////
    //MOTOR DRIVER//////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/motordrivers/list',
        component: motorDriverList
    },
    {
        path: '/motordrivers/create',
        component: motorDriverEdit
    },
    {
        path: '/motordrivers/edit/:id',
        component: motorDriverEdit
    },
    ///////////
    //MOTOR ///////////////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/motors/list',
        component: motorList
    },
    {
        path: '/motors/create',
        component: motorEdit
    },
    {
        path: '/motors/edit/:id',
        component: motorEdit
    },
    ///////////
    //COLOR ///////////////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/colors/list',
        component: colorList
    },
    {
        path: '/colors/create',
        component: colorEdit
    },
    {
        path: '/colors/edit/:id',
        component: colorEdit
    },
    ///////////
    //PACK   ///////////////////////////////////////////////////////////////////////////////
    ///////////
    {
        path: '/packs/list',
        component: packList
    },
    {
        path: '/packs/create',
        component: packEdit
    },
    {
        path: '/packs/edit/:id',
        component: packEdit
    },







];

export default new vueRouter({
    mode: "history",
    routes: routes
});
