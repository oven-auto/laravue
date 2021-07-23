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

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Название</th>
                <th>Фильтр</th>
                <th>Тип</th>
                <th>Бренд</th>
            </tr>

            <tr v-for="device in devices">
                <td>
                    <router-link :to="toEdit + device.id">
                        Open
                    </router-link>
                </td>
                <td>{{ device.name }}</td>
                <td style="">{{ device.filter.name }}</td>
                <td style="width:150px;">{{ device.type.name }}</td>
                <td style="width:100px;">
                    <div v-for="brand in device.brands">
                        <span class="mr-1 badge badge-secondary">{{ brand.name }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';

export default {
    name: 'device-list',
    components: {
        Spin,
        Message
    },
    data() {
        return {
            devices: [],
            loading: true,
            toEdit: '/devices/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
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
