<template>
    <div id="device-type-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Список оборудования</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/devices/create'">Создать новое оборудование</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table table-hover device-table">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 80px;">#</th>
                    <th>Название</th>
                    <th>Фильтр</th>
                    <th>Тип</th>
                    <th>Бренд</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="device in devices">
                    <td>
                        <router-link :to="toEdit + device.id">
                            Open
                        </router-link>
                    </td>
                    <td>{{ device.name }}</td>
                    <td style="">{{ device.filter.name }}</td>
                    <td style="width:150px;">
                        <span
                            class="badge"
                           >
                            {{device.type.name }}
                        </span>
                    </td>
                    <td style="width:100px;">
                        <div v-for="brand in device.brands">
                            <brand-badge :brand="brand"></brand-badge>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';
import BrandBadge from '../badge/BrandBadge';

export default {
    name: 'device-list',
    components: {
        Spin,
        Message,
        BrandBadge
    },
    data() {
        return {
            devices: [],
            loading: true,
            toEdit: '/devices/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
            currentTypeId: 0,
        }
    },
    mounted() {
        this.loadTypes()
    },
    methods: {
        loadTypes() {
            axios.get('/api/devices')
            .then(res => {
                if(res.data.status == 1)
                    this.devices = res.data.data;
                else {
                    this.succes = true;
                    this.succesMessage = res.data.message;
                }
                this.loading = false;
            })
            .catch(errors => {
                console.log(errors)
            })
        }
    }
}
</script>

<style scoped>
.device-table td{
    vertical-align:middle;
}
</style>
