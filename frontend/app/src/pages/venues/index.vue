<template>
  <router-link :to="'/venues/create'">
    <el-button
      type="primary"
      class="ui-datalist-create-btn"
    >Create
    </el-button>
  </router-link>

  <el-table
    :data="items"
    :border="!loading"
    :empty-text="loading ? 'Loading...' : 'Data records not found.'"
    v-loading="loading"
    :default-sort="{
        prop: String($route.query.sort ?? '').replace(/^-/, ''),
        order: String($route.query.sort ?? '').startsWith('-') ? 'descending' : 'ascending',
    }"
    @sort-change="$router.replace({query: {...$route.query, sort: $event.order
      ? ($event.order == 'descending' ? '-' : '') + $event.prop
      : ''
    }})"
  >
    <el-table-column prop="name" label="Name" sortable="custom"/>
    <el-table-column label="Actions" width="81">
      <template #default="{row}: {row: IVenue}">
        <el-space direction="horizontal" :size="7">
          <router-link :to="'/venues/' + row.id">
            <el-button type="primary" :icon="Edit" size="small" circle/>
          </router-link>
          <router-link :to="'/venues/' + row.id + '/delete'">
            <el-button type="danger" :icon="Delete" size="small" circle/>
          </router-link>
        </el-space>
      </template>
    </el-table-column>
  </el-table>

  <div v-if="!loading" class="venues-search-pagination">
    <el-pagination
      background
      layout="prev, pager, next"
      @current-change="$router.replace({query: { ...$route.query, page: $event }})"
      :current-page="pagination.page"
      :page-size="pagination.size"
      :total="pagination.total"/>
  </div>
</template>


<script lang="ts" setup>
import {Delete, Edit} from '@element-plus/icons-vue';
</script>

<script lang="ts">
import Api from "~/classes/tools/Api";

export default defineComponent({
  data: () => ({
    loading: true,
    items: [] as IVenue[],
    pagination: {
      page: 0,
      pages: 0,
      total: 0,
      size: 0,
    },
  }),
  watch: {
    '$route.query': {
      async handler() {
        this.loading = true;
        const api = new Api<IVenue>('venues');
        const res = await api.index({
          page: this.$route.query.page,
          sort: this.$route.query.sort,
        });

        this.items = res;
        this.pagination = res.meta().pagination;
        this.loading = false;
      },
      deep: true,
      immediate: true,
    },
  },
})
</script>

<style>
.venues-search-pagination {
  display: flex;
  justify-content: center;
  padding: 30px;
}
</style>
