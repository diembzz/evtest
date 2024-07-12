<template>
  <el-menu
    class="el-menu-demo"
    mode="horizontal"
    background-color="#545c64"
    text-color="#fff"
    active-text-color="#ffd04b"
    :default-active="'/' + $route.path.split('/')[1]"
    router
  >
    <el-menu-item index="/">Home</el-menu-item>
    <el-menu-item index="/events">Events</el-menu-item>
    <el-menu-item index="/venues">Venues</el-menu-item>
  </el-menu>
</template>

<script lang="ts">
export default defineNuxtComponent({});
</script>

