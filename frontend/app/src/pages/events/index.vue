<template>
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
    <el-table-column prop="poster" label="Poster" width="105">
      <template #default="{row}: {row: IEvent}">
        <img width="80" height="80" alt="Poster" :src="row.poster.src"/>
      </template>
    </el-table-column>
    <el-table-column prop="name" label="Name" sortable="custom"/>
    <el-table-column prop="venue.name" label="Venue" sortable="custom" width="200"/>
    <el-table-column prop="event_date" label="Event Date" sortable="custom" width="120"/>
    <el-table-column prop="event_date" label="Actions" width="81">
      <template #default="{row}: {row: IEvent}">
        <el-space direction="horizontal" :size="7">
          <router-link :to="'/events/' + row.id">
            <el-button type="primary" :icon="Edit" size="small" circle/>
          </router-link>
          <router-link :to="'/events/' + row.id + '/delete'">
            <el-button type="danger" :icon="Delete" size="small" circle/>
          </router-link>
        </el-space>
      </template>
    </el-table-column>
  </el-table>

  <div v-if="!loading" class="events-search-pagination">
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
    items: [] as IEvent[],
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
        const api = new Api<IEvent>('events');
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
.events-search-pagination {
  display: flex;
  justify-content: center;
  padding: 30px;
}
</style>
