<template>
    <div id="pack-list">

        <message v-if="succes" :message="succesMessage"></message>

        <div class="row pb-3">
            <div class="col">
                <div class="h5">Склад</div>
            </div>
            <div class="col text-right">
                <router-link class="btn btn-primary" :to="'/cars/create'">Добавить новый автомобиль</router-link>
            </div>
        </div>

        <spin v-if="loading"></spin>

        <table v-else class="table">
            <tr>
                <th style="width: 80px;">#</th>
                <th>Модель</th>
                <th>Цвет</th>
                <th>$ Кузов</th>
                <th>$ Опций</th>
                <th>$ Оборуд.</th>
                <th>$ Итого</th>
            </tr>

            <tr v-for="item in data">
                <td>
                    <router-link :to="toEdit + item.id">
                        Open
                    </router-link>
                </td>
                <td>
                    {{ item.brand.name }}
                    {{ item.mark.name }}
                    {{ item.complectation.name }}
                    {{ item.complectation.motor.size }}
                    ({{ item.complectation.motor.power }}л.с.)
                </td>
                <td>{{ item.color.color.name }}</td>
                <td><small class="text-small"> {{ formatPrice(item.price.complectation_price) }} </small> </td>
                <td><small class="text-small">{{ formatPrice(item.price.pack_price) }} </small></td>
                <td><small class="text-small">{{ formatPrice(item.price.device_price) }} </small></td>
                <td>{{ formatPrice(item.price.full_price) }}</td>
            </tr>

        </table>
    </div>
</template>

<script>

import Spin from '../spinner/SpinComponent';
import Message from '../alert/MessageComponent';

export default {
    name: 'color-list',
    components: {
        Spin,
        Message
    },
    data() {
        return {
            data: [],
            loading: true,
            toEdit: '/cars/edit/',
            notFound: false,
            succes: false,
            succesMessage: null,
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        formatPrice(price)
        {
            return number_format(price,0,'',' ','руб.')
        },

        loadData() {
            axios.get('/api/cars')
            .then(res => {
                if(res.data.status == 1)
                    this.data = res.data.data;
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
