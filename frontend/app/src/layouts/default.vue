<template>
  <el-container>
    <el-header>
      <Menu/>
    </el-header>
    <el-main>
      <Breadcrumbs/>
      <div class="app-container">
        <slot/>
      </div>

    </el-main>
  </el-container>
</template>

<script lang="ts">
import Breadcrumbs from "~/components/layout/breadcrumbs.vue";
import Menu from "~/components/layout/menu.vue";

export default defineNuxtComponent({
  components: {Menu, Breadcrumbs},
})
</script>

