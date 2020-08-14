<template>
    <div class="flex items-center flex-row flex-wrap justify-between pr-4" style="font-size: 0.9rem;">
        <div class="">
            <inertia-link href="#" class="inline-block nav-item mr-4"
                ><i class="fas fa-icons" /><span> Start quiz</span></inertia-link
            >
            <inertia-link href="#" class="inline-block nav-item mr-4"
                ><i class="fas fa-icons" /><span> Clips</span></inertia-link
            >
        </div>

        <div class="flex items-center">
            <div v-if="$page.auth.check" class="flex items-center">
                <a @click="logout()" class="inline-block nav-item mr-4"><span>Logout</span></a>
                <span>{{ $page.auth.user.username }}</span>
                <img class="rounded-full h-16 w-16 p-2 align-bottom block" :src="userAvatar" alt="Profile Avatar" />
            </div>
            <div v-else class="flex items-center">
                <inertia-link :href="loginURL" class="inline-block nav-item mr-4"
                    ><i class="fas fa-icons" /><span> Login</span></inertia-link
                >
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        logout() {
            var data = new FormData();

            data.append('_method', 'post');

            this.$inertia.post(route('logout.post'), data).then(() => {
                // Do something
            });
        }, // End logout()
    }, // End Methods
    computed: {
        userAvatar: function () {
            if (this.$page.auth.check) {
                if (this.$page.auth.user.avatar) {
                    return this.$page.auth.user.avatar;
                }
                return 'https://png2.cleanpng.com/sh/66c63df9480cad03ab0067c57654d339/L0KzQYm3V8IzN5R2kJH0aYP2gLBuTgV0baMyiOR4ZnnvdX65UME5NZpzReVyZ3j3Pcb6hgIua5Dzftd7ZX7mdX7smQBwNWZnTac9Y0C8SYjqgBUzNmY5TqUANUW0QYa6UsMyPmc9Sag7MUixgLBu/kisspng-user-profile-2018-in-sight-user-conference-expo-5b554c0997cce2.5463555115323166816218.png';
            }
        }, // End userAvatar
        loginURL: function () {
            if (['production', 'staging'].includes(this.$page.app.env)) {
                return '/login/discord';
            }
            return 'login/devenv';
        }, // End userAvatar
    }, // End watch
};
</script>
