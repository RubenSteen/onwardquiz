<template>
    <layout title="Quiz : Map selection">
        <div class="w-full">
            <div class="relative bg-white pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8">
                <div class="absolute inset-0">
                    <div class="bg-white h-1/3 sm:h-2/3"></div>
                </div>
                <div class="relative max-w-7xl mx-auto">
                    <div class="text-center">
                        <h2
                            class="text-3xl leading-9 tracking-tight font-extrabold text-gray-900 sm:text-4xl sm:leading-10"
                        >
                            Maps
                        </h2>
                        <p class="mt-3 max-w-2xl mx-auto text-xl leading-7 text-gray-500 sm:mt-4">
                            Not seeing a specific map? ask for it to be added!
                        </p>
                    </div>
                    <div class="mt-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
                        <inertia-link
                            :href="$route('quiz.show', { map: map.id })"
                            v-for="(map, index) in maps"
                            :key="index"
                            class="flex flex-col rounded-lg shadow-lg overflow-hidden"
                        >
                            <div class="flex-shrink-0">
                                <img
                                    class="h-48 w-full object-cover"
                                    :src="map.template"
                                    :alt="'Template of ' + map.name"
                                />
                            </div>
                            <div class="flex-1 p-6 flex flex-col justify-between">
                                <div class="flex-1">
                                    <p class="text-sm leading-5 font-medium text-indigo-600">
                                        <span href="#"> Questions : {{ map.questions_count }}</span>
                                    </p>
                                    <div href="#" class="block">
                                        <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                            {{ map.name }}
                                        </h3>
                                        <p class="mt-3 text-base leading-6 text-gray-500">
                                            {{ map.description ? map.description : 'No description available...' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </inertia-link>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '../Shared/Layout';

export default {
    components: {
        Layout,
    }, // End Components
    props: {
        maps: Array,
    }, // End Props
};
</script>
