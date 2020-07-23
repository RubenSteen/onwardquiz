<template>
  <header>

    <nav class="min-h-screen bg-primary font-semibold" style="border-right: solid 1px #172340;">
      <div class="h-16 flex justify-between px-2 items-center" style="border-bottom: solid 1px #172340;">

        <div class="">
          <inertia-link href="/" class="block my-2 text-primary text-lg font-bold" v-if="!collapsed">{{ $page.app.name }}</inertia-link>
        </div>
        
        <div class="block">
          <button class="flex items-center px-3 py-2 text-gray-500 border-teal-400 hover:text-white hover:border-white" @click='toggleCollapse()'>
            <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
          </button>
        </div>

      </div>
      <div class="text-sm box-border p-4 flex flex-col justify-center" v-bind:class="{ 'text-center': collapsed, }">
        <span class="block nav-category uppercase mb-1"><i class="bx bxs-circle text-xs text-gray-700" v-if="collapsed"/><span v-else> Main</span></span>
        <inertia-link :href="$route('admin.map.index')" v-bind:class="{ 'active': currentActiveLink('admin.map.index') }" class="block my-1 nav-item"><i class='bx bxs-map-alt' v-bind:class="{ 'pr-3': !collapsed, 'text-xl': collapsed }" /><span v-if="!collapsed"> Maps</span></inertia-link>

        <span class="block nav-category uppercase mb-1 my-3"><i class="bx bxs-circle text-xs text-gray-700" v-if="collapsed"/><span v-else> Team</span></span>
        <inertia-link href="#" v-bind:class="{ 'active': currentActiveLink('#') }" class="block my-1 nav-item"><i class='bx bxs-group' v-bind:class="{ 'pr-3': !collapsed, 'text-xl': collapsed }" /> <span v-if="!collapsed"> My Teams</span></inertia-link>
        <inertia-link href="#" v-bind:class="{ 'active': currentActiveLink('#') }" class="block my-1 nav-item"><i class='bx bxs-user-detail' v-bind:class="{ 'pr-3': !collapsed, 'text-xl': collapsed }" /> <span v-if="!collapsed"> Teams Management</span></inertia-link>

        <span class="block nav-category uppercase mb-1 my-3" v-if="$page.auth.user.isSuperAdmin"><i class="bx bxs-circle text-xs text-gray-700" v-if="collapsed"/><span v-else> Admin</span></span>
        <inertia-link :href="$route('admin.user.index')" v-bind:class="{ 'active': currentActiveLink('admin.user.index') }" class="block my-1 nav-item"><i class='bx bxs-user' v-bind:class="{ 'pr-3': !collapsed, 'text-xl': collapsed }" /><span v-if="!collapsed">Users</span></inertia-link>
        <inertia-link :href="$route('admin.team.index')" v-bind:class="{ 'active': currentActiveLink('admin.team.index') }" class="block my-1 nav-item"><i class='bx bxs-group' v-bind:class="{ 'pr-3': !collapsed, 'text-xl': collapsed }" /><span v-if="!collapsed">Teams</span></inertia-link>
      </div>
    </nav>

  </header>
</template>

<script>
  export default {
    props: {
      collapse: {
        type: Boolean,
        default: false
      },
    }, // End Props
    data() {
      return {
        collapsed: null, // Default null because the watch gets triggered then
      }
    }, // End Data
    methods: {
      toggleCollapse() {
        this.collapsed = !this.collapsed
        $cookies.set('collapsedLeftSideNavBar', this.collapsed)
      }, // End toggleCollapse()
      currentActiveLink(string) {
        if (route().current() == string) {
          return true
        }
        return false
      } //End currentActiveLink
    }, // End Methods
    computed: {
      
    },
    watch: {
      collapsed: function () {
        this.$emit('collapse', this.collapsed)
      }, // End collapsed
    }, // End watch
    mounted: function () {
      if ($cookies.isKey('collapsedLeftSideNavBar')) {
        this.collapsed = ($cookies.get('collapsedLeftSideNavBar') == "true")
      }
    }, //End Mounted
  }
</script>