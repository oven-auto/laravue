<template>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Справочники
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                        <router-link :to="'/brands/list'" class="dropdown-item">Бренды</router-link>

                        <div class="dropdown-divider"></div>

                        <router-link :to="'/devicetypes/list'" class="dropdown-item">Типы оборудование</router-link>
                        <router-link :to="'/devicefilters/list'" class="dropdown-item">Фильтры оборудования</router-link>
                        <router-link :to="'/devices/list'" class="dropdown-item">Оборудование</router-link>
                        <router-link :to="'/packs/list'" class="dropdown-item">Опции</router-link>

                        <div class="dropdown-divider"></div>

                        <router-link :to="'/motortypes/list'" class="dropdown-item">Типы моторов</router-link>
                        <router-link :to="'/motortransmissions/list'" class="dropdown-item">Типы трансмиссий</router-link>
                        <router-link :to="'/motordrivers/list'" class="dropdown-item">Типы приводов</router-link>
                        <router-link :to="'/motors/list'" class="dropdown-item">Агрегаты</router-link>

                        <div class="dropdown-divider"></div>

                        <router-link :to="'/properties/list'" class="dropdown-item">Характеристики</router-link>

                        <div class="dropdown-divider"></div>

                        <router-link :to="'/colors/list'" class="dropdown-item">Палитра цветов</router-link>

                    </div>
                </li>

                <!-- <li v-for="link in links" class="nav-item {{ item.href ? '' : 'dropdown' }}">
                    <router-link :to="link.href" class="nav-link"> {{ link.title }}</router-link>
                </li> -->
            </ul>
        </div>
    </div>
</nav>
</template>

<script>
export default {
    name: 'top-menu',
    data() {
        return {
            links: [

            ]
        }
    }
}
</script>
