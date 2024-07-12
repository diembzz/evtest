<template>
  <el-breadcrumb :separator-icon="ArrowRight" v-if="$route.path != '/'">
    <el-breadcrumb-item v-for="item in items" :to="{ path: item.path }">{{ item.label }}</el-breadcrumb-item>
  </el-breadcrumb>
</template>

<script lang="ts" setup>
import {ArrowRight} from '@element-plus/icons-vue';
</script>

<script lang="ts">
export default defineNuxtComponent({
  computed: {
    items(): { path: string, label: string }[] {
      const stack = [];
      const result = [{path: '/', label: 'Home'}];
      for (const section of this.$route.path.split('/').splice(1)) {
        if (section !== '') {
          stack.push(section);
          result.push({path: '/' + stack.join('/'), label: this.title(section)});
        }
      }
      return result;
    }
  },

  methods: {
    title(str: string): string {
      str = str.charAt(0).toUpperCase() + str.slice(1);
      return str.replace(/(-[a-z])/g, group => group
        .toUpperCase().replace('-', ' ')
      );
    }
  }
})
</script>
