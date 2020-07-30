<template>
  <layout title="Maps" v-on:breadcrumbs="hideBreadcrumbs()">
  
    <!-- Replace with your content -->
    <div class="py-4">


      
    </div>
    <!-- /End replace -->


  </layout>
</template>

<script>
  import Layout from '../Shared/Layout'
  import Pagination from '../Shared/Pagination'

  export default {
    components: {
      Layout,
      Pagination,
    },
    data() {
      return {
        sidebarOpen: false,
      }
    }, // End Data
  }
</script>