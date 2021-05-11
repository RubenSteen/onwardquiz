<template>
    <layout title="Editing Map">
        <div class="w-full">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <div class="flex">
                        <img
                            v-if="user.avatar"
                            class="inline-block h-12 w-12 rounded-full"
                            :src="user.avatar"
                            alt="Discord avatar"
                        />

                        <span v-else class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </span>

                        <h3 class="text-lg leading-6 font-medium text-gray-900 self-center ml-3">
                            {{ user.username }}
                        </h3>
                    </div>
                    <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
                        Last discord sync : {{ user.last_discord_sync }}
                    </p>
                </div>
                <div class="px-4 py-5 sm:px-6">
                    <dl class="grid grid-cols-1 col-gap-4 row-gap-8 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm leading-5 font-medium text-gray-500">Full discord name</dt>
                            <dd class="mt-1 text-sm leading-5 text-gray-900">
                                {{ user.getFullUsername }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm leading-5 font-medium text-gray-500">Joined us at</dt>
                            <dd class="mt-1 text-sm leading-5 text-gray-900">
                                {{ user.created_at }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm leading-5 font-medium text-gray-500">Email address</dt>
                            <dd class="mt-1 text-sm leading-5 text-gray-900">
                                {{ user.email }}
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm leading-5 font-medium text-gray-500">Language</dt>
                            <dd class="mt-1 text-sm leading-5 text-gray-900">
                                {{ user.locale }}
                            </dd>
                        </div>
                        <!-- <div class="sm:col-span-2">
              <dt class="text-sm leading-5 font-medium text-gray-500">
                Title
              </dt>
              <dd class="mt-1 text-sm leading-5 text-gray-900">
                Content
              </dd>
            </div> -->
                    </dl>
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
        user: Object,
    }, // End Props
};
</script>
