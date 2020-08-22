<template>
    <layout title="User : ">
        <div class="font-bold text-xl mb-8">User : {{ user.username }} (#{{ user.discriminator }})</div>

        <div class="w-full">
            <div
                class="bg-primary shadow-md p-4 flex flex-col justify-between leading-normal border rounded relative"
                style="border-color: #172340;"
            >
                <div class="absolute right-0 top-0 p-4">
                    <span
                        class="flex rounded-full bg-red-500 uppercase px-2 py-1 text-xs font-bold mb-2 cursor-pointer text-gray-300 hover:text-white"
                        ><i class="bx bx-x-circle text-xl mr-1" />Editor</span
                    >
                    <span
                        class="flex rounded-full bg-red-500 uppercase px-2 py-1 text-xs font-bold mb-2 cursor-pointer text-gray-300 hover:text-white"
                        ><i class="bx bx-x-circle text-xl mr-1" />Super Admin</span
                    >
                    <span
                        class="flex rounded-full bg-red-500 uppercase px-2 py-1 text-xs font-bold mb-2 cursor-pointer text-gray-300 hover:text-white"
                        ><i class="bx bx-x-circle text-xl mr-1" />Banned</span
                    >
                    <span
                        class="flex rounded-full bg-green-500 uppercase px-2 py-1 text-xs font-bold mb-2 cursor-pointer text-gray-300 hover:text-white"
                        ><i class="bx bx-check text-xl mr-1" />Confirmed</span
                    >
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <form class="px-8 pt-6 pb-8 mb-4 col-span-12">
                        <div class="mb-6 col-span-4 w-full">
                            <label class="block text-sm font-medium mb-2" for="name">
                                Name
                            </label>
                            <input
                                v-bind:class="{ 'border border-red-500': $page.errors.username }"
                                class="shadow appearance-none rounded w-64 py-2 px-3 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                                id="name"
                                type="text"
                                placeholder="Bazaar"
                                v-model="form.username"
                            />
                            <i class="ml-1 bx bxs-pencil text-lg" />
                            <p
                                class="text-xs italic"
                                v-if="$page.errors.username"
                                v-for="error in $page.errors.username"
                            >
                                {{ error }}
                            </p>
                        </div>

                        <div class="flex items-center">
                            <button
                                class="bg-secondary hover:bg-blue-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="button"
                                @click="updateUser"
                                :disabled="loading"
                            >
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '@/Pages/Shared/Layout';

export default {
    components: {
        Layout,
    }, // End Components
    props: {
        user: Object,
    }, // End Props
    data() {
        return {
            form: {
                username: this.user.username,
            },
            loading: false,
        };
    }, // End Data
    methods: {
        updateUser() {
            this.loading = true;

            var data = new FormData();

            data.append('name', this.form.name);

            data.append('_method', 'patch');

            this.$inertia.post(route('admin.user.update', this.user.id), data).then(() => {
                this.loading = false;
            });
        }, // End updateUser()
    }, // End Methods
};
</script>
