<template>

    <b-modal ref="device-filter-modal" size="lg" hide-footer :title="'Поиск оборудования'">
        <div class="d-block text-left">
            <slot name="body">
                <div class="v-modal-content">
                    <div class="row">
                        <div class="col-6">
                            <text-input v-model="search.name" :label="'Название'" class=""></text-input>
                            <brand-select v-model="search.brand_id" class="pt-3"></brand-select>

                            <div class="pt-3">
                                <label>Тюнинг</label>
                                <select v-model="search.tuning" class="form-control">
                                    <option value="0">Все оборудование</option>
                                    <option value="1">Только тюнинг</option>
                                    <option value="2">Все кроме тюнинга</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <DeviceTypeSelect v-model="search.device_type_id" class=""></DeviceTypeSelect>
                            <FilterDeviceSelect v-model="search.device_filter_id" class="pt-3"></FilterDeviceSelect>

                            <div class="pt-3">
                                <label>Целевой коэффициент установки</label>
                                <select v-model="search.install_target" class="form-control">
                                    <option value="0">Все оборудование</option>
                                    <option value="1">Только с ЦКУ</option>
                                    <option value="2">Все кроме ЦКУ</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
            </slot>
            <slot name="footer">
                <div class="v-modal-footer">
                    <div class="row">
                        <div class="col-6"></div>
                        <div class="col-3">
                            <button class="btn btn-primary btn-block" @click="acceptFilter">
                                Найти
                            </button>
                        </div>
                        <div class="col-3">
                            <button class=" btn btn-danger btn-block" @click="clearFilter">
                                Очистить
                            </button>
                        </div>
                    </div>



                </div>
            </slot>
        </div>
    </b-modal>
</template>

<script>

import TextInput from '../html/TextInput';
import BrandSelect from '../html/BrandSelect';
import DeviceTypeSelect from '../html/Select/DeviceTypeSelect';
import FilterDeviceSelect from '../html/Select/FilterDeviceSelect';

export default {
    name: 'device-filter-modal',
    components: {TextInput,BrandSelect,DeviceTypeSelect,FilterDeviceSelect},
    data() {
        return {
            show: false,
            search: {
                name: '',
                brand_id: 0,
                device_type_id: 0,
                device_filter_id: 0,
                tuning: 0,
                install_target: 0,
            },
        }
    },
    methods: {
        closeModal: function () {
            this.$refs['device-filter-modal'].hide()
        },
        acceptFilter() {
            this.closeModal()
            this.$emit('updateParent', this.search)
        },
        clearFilter() {
            this.closeModal()
            this.search.name = '';
            this.search.brand_id = 0;
            this.search.device_filter_id = 0;
            this.search.device_type_id = 0;
            this.search.tuning = 0
            this.search.install_target = 0
            this.$emit('updateParent', this.search);
        }
    }
}
</script>

<style scoped lang="scss">
    .item-color{
       cursor: pointer;
       border-radius: 10px;
       background: #ddd;
    }
    .v-modal-shadow {
        position: fixed;
        top: 0;
        left: 0;
        min-height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;;
    }

    .v-modal {
        background: #fff;
        border-radius: 8px;
        padding: 15px;
        min-width: 1000px;
        max-width: 1000px;
        position: absolute;
        top: 200px;
        left: 50%;
        transform: translate(-50%, -50%);

        &-close {
            border-radius: 50%;
            color: #fff;
            background: #2a4cc7;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 7px;
            right: 7px;
            width: 30px;
            height: 30px;
            cursor: pointer;
        }

        &-title {
            color: #0971c7;
        }

        &-content {
            margin-bottom: 20px
        }

        &-footer {
            &__button {
                background-color: #0971c7;
                color: #fff;
                border: none;
                text-align: center;
                padding: 8px;
                font-size: 17px;
                font-weight: 500;
                border-radius: 8px;
                min-width: 150px;
            }
        }
    }
</style>

