<template>
  <el-container>
    <el-header>
      <UiMenu/>
    </el-header>
    <el-main>
      <UiBreadcrumbs/>
      <div class="app-container">
        <slot/>
      </div>
    </el-main>
  </el-container>
</template>

<script lang="ts">
import UiBreadcrumbs from "~/components/layout/UiBreadcrumbs.vue";
import UiMenu from "~/components/layout/UiMenu.vue";

export default defineNuxtComponent({
  components: {UiMenu, UiBreadcrumbs},
})
</script>

