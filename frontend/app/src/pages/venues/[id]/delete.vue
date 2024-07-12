<script lang="ts">
import Api from "~/classes/tools/Api";
import {ElNotification} from "element-plus";

export default defineNuxtComponent({
  async created() {
    const res = (await (new Api('venues')).destroy(this.$route.params.id));

    navigateTo('/venues');

    if (res.success()) {
      ElNotification.success({
        title: 'Success',
        message: 'Record is successfully deleted.',
        offset: 10,
      });
    } else {
      ElNotification.error({
        title: 'Failed',
        message: res.error() as string ?? "API request error.",
        offset: 10,
      })
    }
  }
})
</script>
