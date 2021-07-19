<template>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
            <li v-for="link in links" class="nav-item">
                <router-link :to="link.href" class="nav-link"> {{ link.title }}</router-link>
            </li>
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
                {
                    title: 'Главная',
                    href: '/'
                },
                {
                    title: 'Бренды',
                    href: '/brands/list'
                },
                {
                    title: 'Типы оборудования',
                    href: '/devicetypes/list'
                },
                {
                    title: 'Фильтры оборудования',
                    href: '/devicefilters/list'
                },
                {
                    title: 'Оборудование',
                    href: '/devices/list'
                },
                {
                    title: 'Характеристики',
                    href: '/properties/list'
                },
                {
                    title: 'Типы мотора',
                    href: '/motortypes/list'
                },
                {
                    title: 'Типы трансмиссий',
                    href: '/motortransmissions/list'
                },
                {
                    title: 'Типы привода',
                    href: '/motordrivers/list'
                },
            ]
        }
    }
}
</script>
