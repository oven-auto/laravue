<template>
<div id="mark-list">
    <div class="row pb-3">
        <div class="col">
            <div class="h5">Список моделей</div>
        </div>
        <div class="col text-right">
            <router-link class="btn btn-primary" :to="'/marks/create'">Создать новую модель</router-link>
        </div>
    </div>

    <spin v-if="loading"></spin>

    <!-- <table v-else class="table">
        <tr>
            <th style="width: 80px;">#</th>
            <th>Название</th>
            <th>Статус</th>
        </tr>

        <tr v-for="mark in marks">
            <td>
                <router-link :to="toEdit + mark.id">
                    Open
                </router-link>
            </td>
            <td>
                <brand-badge :brand="mark.brand"></brand-badge>
                {{ mark.prefix ? mark.prefix : '' }} {{ mark.name  }}
            </td>
            <td>
                <span class="badge badge-success" v-if="mark.status">
                    Активна
                </span>
                <span v-else class="badge badge-danger">
                    Отключена
                </span>
            </td>
        </tr>
    </table> -->

    <div class="row marks-row" v-else>
        <div class="col-3 "  v-for="mark in marks" >
            <router-link :to="toEdit + mark.id">
            <div class="border rounded py-3 mark-col">
                <div :class="{'gray-image': mark.status==0}">
                    <div class="text-center">
                        {{mark.predix}}
                        {{mark.brand.name}}
                        <b>{{mark.name}}</b>
                    </div>
                    <img :src="writeImage(mark.icon.image)" >
                    <div class="text-muted text-center">
                        <small>{{mark.bodywork.name}}</small>
                    </div>
                </div>
            </div>
            </router-link>
        </div>
    </div>

</div>

</template>

<script>
import Spin from '../spinner/SpinComponent';
import BrandBadge from '../badge/BrandBadge';

export default {
    name: 'mark-list',
    components: {
        Spin,
        BrandBadge
    },
    data() {
        return {
            toEdit: '/marks/edit/',
            loading: true,
            marks: [],
            notFound: false,
        }
    },
    mounted() {
        this.loadData()
    },
    methods: {
        writeImage(url) {
            return storageUrl+url;
        },
        loadData() {
            axios.get('/api/marks')
            .then(response => {
                if(response.data.status == 1) {
                    this.marks = response.data.data;
                    this.loading = false;
                }
                else{
                    this.loading = false;
                    this.notFound = true;
                }
            })
            .catch(errors => {
                this.loading = false;
                this.notFound = true;
            })
        }
    }
}
</script>

<style scoped>
.marks-row img{
    width: 100%;
}
.marks-row a{
    text-decoration: none;
    color: inherit;
}
.mark-col{
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}
.mark-col:hover{
    transform: scale(1.02,1.02);
    box-shadow: 0 0 30px rgb(226, 226, 226);
}
.gray-image{
    opacity: .3;
}
</style>
